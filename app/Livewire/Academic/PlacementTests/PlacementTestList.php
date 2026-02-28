<?php

namespace App\Livewire\Academic\PlacementTests;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\CourseType;
use App\Models\Academic\PlacementTest;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\Shift;
use App\Models\CenterSettings\PlacementTestSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Carbon\Carbon;
use DB;
class PlacementTestList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'placement-test-list-addEditModal';
    public $table_name='placement_tests';
    public $selectedFields = [];
    public $pdfOrientation ='landscape';
    protected $listeners = ['modalClosed' => 'closeModal','globalDelete' => 'handleGlobalDelete'];
    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->modalId);

    }
    public function openModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: $this->modalId);
    }
     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function applySearch()
    {
        $this->resetPage();
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $programs=[];
    public $books=[];
    public $shifts=[];
    public $course_types=[];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
        $this->programs = Program::where('status','active')->get();
        $this->shifts = Shift::all();
        $this->course_types = CourseType::all();
    }

    public $test_id,$program_id,$book_id,$branch_id,$shift_id;
    public $student_id;
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'programs',
            'books',
            'shifts',

        ]);
    }
    public $search = [
            'identity' => null,
            'program_id' => null,
            'book_id' => null,
            'branch_id' => null,
            'student_id' => null,
        ];


    public function render()
    {
        $tests = PlacementTest::with('student','branch','program','book','shift')

        ->when(!empty($this->search['identity']), function ($query) {
            $search = $this->search['identity'];
            $query->whereHas('student',function($q) use ($search){
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            });
        })

        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['program_id']), function ($query) {
            $query->where('program_id',$this->search['program_id']);
        })
        ->when(!empty($this->search['book_id']), function ($query) {
            $query->where('book_id',$this->search['book_id']);
        })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.academic.placement-tests.placement-test-list',compact('tests'));
    }

    protected function rules()
    {
        $rules =  [
            'student_id' => $this->editMode ? 'nullable' : 'required',
            'program_id' => 'required',
            'book_id' => 'required',
            'shift_id' => 'required',
        ];
        if (!Auth::user()->branch_id) {
            $rules['branch_id'] = 'required';
        }
        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [

            'student_id.required'   => __('label.student.required'),
            'branch_id.required'   => __('label.branch.required'),
            'program_id.required'   => __('label.program.required'),
            'book_id.required'   => __('label.book.required'),
        ];
    }
    
    // Create role
   public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        DB::beginTransaction();

        try {

            $setting = PlacementTestSetting::first();

            if (!$setting) {
                DB::rollBack();
                return $this->dispatch(
                    'alert',
                    type: 'error',
                    message: __('label.no_setting_found')
                );
            }

            $test_date   = now();
            $expire_date = $setting->validity_months
                ? $test_date->copy()->addMonths($setting->validity_months)
                : $test_date;

            $fee_amount = $setting->has_fee ? ($setting->fee_amount ?? 0) : 0;
            $is_paid    = $setting->has_fee ? 0 : 1;

            $test = PlacementTest::firstOrCreate(
                [
                    'student_id' => $this->student_id,
                    'branch_id'  => Auth::user()->branch_id ?: $this->branch_id,
                    'program_id' => $this->program_id,
                    'book_id'    => $this->book_id,
                    'shift_id'    => $this->shift_id,
                ],
                [
                    'test_date'   => $test_date,
                    'expire_date' => $expire_date,
                    'fee_amount'  => $fee_amount,
                    'is_paid'     => $is_paid,
                    'user_id'     => Auth::id(),
                ]
            );

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id'   => $test->student?->id,
                'section' => __('label.placement_test').' ('.$test->student?->name.' ID:'.$test->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
            
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 
        $test = PlacementTest::find($id);
        $this->test_id = $id;  
        $this->student_id = $test->student_id;
        $this->branch_id = $test->branch_id;
        $this->program_id = $test->program_id;
        $this->loadProgramBook($this->program_id);
        $this->book_id = $test->book_id;
        $this->shift_id = $test->shift_id;
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();
        try {
            $exists = PlacementTest::where('student_id', $this->student_id)
                ->where('branch_id', $this->branch_id)
                ->where('program_id', $this->program_id)
                ->where('book_id', $this->book_id)
                ->where('shift_id', $this->shift_id)
                ->where('id', '!=', $this->test_id)
                ->exists();

            if ($exists) {
                $this->addError('duplicate',__('label.student.unique'));
                return;
            }


            $test = PlacementTest::findOrFail($this->test_id);

            $test->update([
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'program_id' => $this->program_id,
                'book_id' => $this->book_id,
                'shift_id' => $this->shift_id,
            ]);

             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $test->student?->id,
                'section' => __('label.placement_test').' ('.$test->student?->name.' ID:'.$test->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
        
            $this->dispatch('alert', type: 'error', message: __('label.update_error').' : '. $e->getMessage());
        }
    }

    
    public function handleGlobalDelete($payload)
    {

        if (!isset($payload['table']) || $payload['table'] !== $this->table_name) {
            return;
        }

        $this->delete($payload['id']);
    }

    public function delete($id)
    {
        if(!delete(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        try {
            $test = PlacementTest::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $test->student?->id,
                'section' => __('label.placement_test').' ('.$test->student?->name.' ID:'.$test->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $test->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $data = $this->getReport();
        $tests = $data['tests'];
        $fields = $data['fields'];

        $pdf = Pdf::loadView(
            'livewire.academic.placement-tests.placement-test-list-pdf',
            [
                'tests' => $tests,
                'fields' => $fields
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'placement-test-list' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function exportExcel()
    {
        $data = $this->getReport();
        $tests = $data['tests'];
        $fields = $data['fields'];

        return Excel::download(
            new class($tests, $fields) implements FromCollection, WithHeadings, WithEvents {

                protected $tests;
                protected $fields;

                public function __construct($tests, $fields)
                {
                    $this->tests  = $tests;
                    $this->fields = $fields;
                }

                public function collection()
                {
                    return $this->tests->map(function ($test, $index) {

                        $row = [];

                        foreach ($this->fields as $field) {
                            switch ($field) {
                                case 'no': $row[] = $index + 1; break;
                                case 'student_code': $row[] = $test->student?->student_code; break;
                                case 'name': $row[] = $test->student?->name; break;
                                case 'last_name': $row[] = $test->student?->last_name; break;
                                case 'father_name': $row[] = $test->student?->father_name; break;
                                case 'phone_no': $row[] = $test->student?->phone_no; break;
                                case 'program_id': $row[] = $test->program?->name; break;
                                case 'book_id': $row[] = $test->book?->name; break;
                                case 'shift_id': $row[] = $test->shift?->name; break;
                                case 'branch_id': $row[] = $test->branch?->name; break;
                                case 'status': $row[] = $test->status; break;
                                default: $row[] = '';
                            }
                        }

                        return $row;
                    });
                }

                
                public function headings(): array
                {
                     $headers = [
                        'no'             => __('label.no'),
                        'student_code'   => __('label.student_code'),
                        'name'           => __('label.name'),
                        'last_name'      => __('label.last_name'),
                        'father_name'    => __('label.father_name'),
                        'phone_no'       => __('label.phone_no'),
                        'program_id'     => __('label.program'),
                        'book_id'        => __('label.book'),
                        'shift_id'       => __('label.shift'),
                        'status'         => __('label.status'),
                        'branch_id'      => __('label.branch'),
                    ];

                    $translatedFields = [];

                    foreach ($this->fields as $field) {
                        $translatedFields[] = $headers[$field] ?? $field;
                    }

                    return [
                        [__('label.center_name')],
                        [__('label.placement_test')],
                        [],
                        $translatedFields
                    ];
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {

                            $sheet = $event->sheet->getDelegate();

                            $lastColumn = chr(64 + count($this->fields));

                            // --- Merge و عنوان ---
                            $sheet->mergeCells("A1:{$lastColumn}1"); // لوگو و عنوان اصلی
                            $sheet->mergeCells("A2:{$lastColumn}2"); // عنوان گزارش

                            // --- وسط‌چین ---
                            $sheet->getStyle("A1:{$lastColumn}2")->getAlignment()->setHorizontal('center');

                            // --- بولد و سایز فونت ---
                            $sheet->getStyle("A1:{$lastColumn}2")->getFont()->setBold(true)->setSize(14);

                            // --- لوگو ---
                            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                            $drawing->setName('Logo');
                            $drawing->setPath(public_path('logo.png'));
                            $drawing->setHeight(60);
                            $drawing->setCoordinates('A1');
                            $drawing->setWorksheet($sheet);

                            // --- Auto-size برای ستون‌ها ---
                            $highestColumn = $sheet->getHighestColumn();
                            foreach (range('A', $highestColumn) as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }
                        }
                    ];
                }

            },
            'placement-test-list-' . now()->format('Y-m-d-H-i') . '.xlsx'
        );
    }

    protected function getReport()
    {
        $defaultFields = [
            'no',
            'student_code',
            'name', 
            'last_name',
            'father_name',
            'phone_no',
            'program_id',
            'book_id',
            'shift_id',
            'status',
        ];

        $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
            if (!in_array('branch_id', $fields)) {
                $fields[] = 'branch_id';
            }
        }

        $query = PlacementTest::with('student','branch','program','book','shift')

            ->when(!empty($this->search['identity']), function ($query) {
                $search = $this->search['identity'];
                $query->whereHas('student', function($q) use ($search){
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%");
                    });
                });
            })
            ->when(!empty($this->search['branch_id']), function ($query) {
                $query->where('branch_id', $this->search['branch_id']);
            })
            ->when(!empty($this->search['program_id']), function ($query) {
                $query->where('program_id', $this->search['program_id']);
            })
            ->when(!empty($this->search['book_id']), function ($query) {
                $query->where('book_id', $this->search['book_id']);
            });

        $tests = $query->orderBy('id', 'asc')->get();

        return [
            'tests' => $tests,
            'fields' => $fields,
        ];
    }

    public function loadProgramBook($program_id)
    {
        $this->books = Book::where('status', 'active')
            ->where('program_id', $program_id)->get();
    }
}
