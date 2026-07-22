<?php

namespace App\Livewire\CenterSettings\Books;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\ExamType;
use App\Models\CenterSettings\BookSpecialDiscount;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
use DB;
use Illuminate\Validation\Rule;
class BookList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $programs=[];
    public $exam_types=[];
    public $book_special_discounts=[];
    public $book_special_discount_types = [];
    public $modalId = 'book-list-addEditModal';
    public $table_name='books';
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

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->programs = Program::all();
        $this->exam_types =  ExamType::orderBy('id','ASC')->get();

        // special discounts ----------------
        $this->book_special_discount_types = ['failed', 'makeup', 're_study', 'dropped'];

        $this->book_special_discounts = [];

        foreach ($this->book_special_discount_types as $type) {
            $this->book_special_discounts[$type] = [
                'amount' => null,
                'duration_days' => null,
            ];
        }
    }

    public $name,$abbreviation,$book_id, $program_id,$status = 'active',$fee,
    $pass_mark,$makeup_mark,
    $total_teaching_days,$min_capacity,$max_capacity;
    public $exam_type_ids = [];
    public $percentages = [];   
    public $exam_fine_amount;   
    public $level_number;   
    public $drop_days;   
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'programs',
            'exam_types',
            'book_special_discounts',
            'book_special_discount_types',
        ]);
    }
    public $search = [
            'name' => null,
            'program_id' => null,
        ];
    
    public function render()
    {
    
        $books = Book::with('program')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['program_id']), function ($query) {
            $query->where('program_id',$this->search['program_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);
        return view('livewire.center-settings.books.book-list',compact('books'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:books,name,' . $this->book_id,
            'abbreviation' => 'required|string|max:255|unique:books,abbreviation,' . $this->book_id,
            'program_id' => 'required',
            'pass_mark' => 'required',
            'makeup_mark' => 'required',
            'fee' => 'required',
            'total_teaching_days' => 'required|integer|min:1',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|gte:min_capacity',
            
            'exam_fine_amount' => 'required',
            'drop_days' => 'required',
            'level_number' => [
                'required',
                Rule::unique('books')
                    ->where(fn ($query) => $query->where('program_id', $this->program_id))
                    ->ignore($this->book_id),
            ],
            
            'exam_type_ids' => 'required|array|min:1',
            'percentages' => 'required|array',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.book_name.required'),
            'abbreviation.required' => __('label.abbreviation.required'),
            'name.string'   => __('label.book_name.string'),
            'name.max'      => __('label.book_name.max'),
            'name.unique'   => __('label.book_name.unique'),
            'program_id.required'   => __('label.program.required'),
            'pass_mark.required'   => __('label.pass_mark.required'),
            'makeup_mark.required'   => __('label.makeup_mark.required'),
            'fee.required'   => __('label.fee.required'),
            'total_teaching_days.required'   => __('label.total_teaching_days.required'),
            'min_capacity.required'   => __('label.min_capacity.required'),
            'max_capacity.required'   => __('label.max_capacity.required'),
            'max_capacity.required'   => __('label.max_capacity.required'),
            'exam_fine_amount.required'   => __('label.exam_fine_amount.required'),
            'level_number.required'   => __('label.level_number.required'),
            'drop_days.required'   => __('label.drop_days.required'),
            
            'exam_type_ids.required' => __('label.exam_type.required'),
            'percentages.required'      => __('label.percentage.required'),
        ];
    }
    
    // Create role
    public function store()
    {
        
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
        
        foreach ($this->exam_type_ids as $id) {
            $this->percentages[$id] = is_numeric($this->percentages[$id] ?? null)
                ? $this->percentages[$id]
                : 0;
        }
 
        $totalPercentage = array_sum($this->percentages ?? []);
        if ($totalPercentage != 100) {
            $this->addError('percentages_total',__('label.total_percentage_message'));
            return;
        }

        $this->validate();

    
        DB::beginTransaction();

        try {
            // ذخیره کتاب
            $book = Book::create([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'program_id' => $this->program_id,
                'status' => $this->status,
                'fee' => $this->fee,
                'pass_mark' => $this->pass_mark,
                'makeup_mark' => $this->makeup_mark,
                'total_teaching_days' => $this->total_teaching_days,
                'min_capacity' => $this->min_capacity,
                'max_capacity' => $this->max_capacity,
                'exam_fine_amount' => $this->exam_fine_amount,
                'level_number' => $this->level_number,
                'drop_days' => $this->drop_days,
            ]);

            // ذخیره exam types + درصدها
            $syncData = [];
            foreach ($this->exam_type_ids as $id) {
                $syncData[$id] = ['percentage' => $this->percentages[$id] ?? 0];
            }
            $book->examTypes()->sync($syncData);

            // special discouts------------------

            foreach ($this->book_special_discounts as $type => $data) {

                if (!empty($data['amount'])) {

                    BookSpecialDiscount::updateOrCreate(
                        [
                            'book_id' => $book->id,
                            'type' => $type,
                        ],
                        [
                            'amount' => $data['amount'],
                            'duration_days' => $data['duration_days'] ?? null,
                        ]
                    );

                } else {

                    BookSpecialDiscount::where('book_id', $book->id)
                        ->where('type', $type)
                        ->delete();
                }
            }

            // =--------------end special discounts------------------------

            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book').' ('.$book->name.' ID:'.$book->id.')',
                'type_id' => 2,
            ]);

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
        $this->book_id = $id;    
        $book = Book::find($id);
        $this->name = $book->name;
        $this->abbreviation = $book->abbreviation;
        $this->program_id = $book->program_id;
        $this->status = $book->status;
        $this->fee = $book->fee;
        $this->pass_mark = $book->pass_mark;
        $this->makeup_mark = $book->makeup_mark;
        $this->total_teaching_days = $book->total_teaching_days;
        $this->min_capacity = $book->min_capacity;
        $this->max_capacity = $book->max_capacity;
        $this->exam_fine_amount = $book->exam_fine_amount;
        $this->level_number = $book->level_number;
        $this->drop_days = $book->drop_days;

        $this->exam_type_ids = [];
        $this->percentages = [];
        foreach ($book->examTypes as $type) {
            $this->exam_type_ids[] = $type->id;
            $this->percentages[$type->id] = $type->pivot->percentage;
        }


        $discounts = BookSpecialDiscount::where('book_id', $this->book_id)->get();

        foreach ($this->book_special_discount_types as $type) {

            $existing = $discounts->where('type', $type)->first();

            $this->book_special_discounts[$type] = [
                'amount' => $existing->amount ?? null,
                'duration_days' => $existing->duration_days ?? null,
            ];
        }

        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $totalPercentage = array_sum($this->percentages);
        if ($totalPercentage != 100) {
            $this->addError('percentages_total', __('label.total_percentage_message'));
            return;
        }

        $this->validate();
        DB::beginTransaction();

        try {
            $book = Book::findOrFail($this->book_id);
            $book->update([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'program_id' => $this->program_id,
                'status' => $this->status,
                'fee' => $this->fee,
                'pass_mark' => $this->pass_mark,
                'makeup_mark' => $this->makeup_mark,
                'total_teaching_days' => $this->total_teaching_days,
                'min_capacity' => $this->min_capacity,
                'max_capacity' => $this->max_capacity,
                'exam_fine_amount' => $this->exam_fine_amount,
                'level_number' => $this->level_number,
                'drop_days' => $this->drop_days,
            ]);

            $syncData = [];

            foreach ($this->exam_type_ids as $id) {
                $syncData[$id] = [
                    'percentage' => $this->percentages[$id] ?? 0
                ];
            }

            $book->examTypes()->sync($syncData);


                 // special discouts------------------

            foreach ($this->book_special_discounts as $type => $data) {

                if (!empty($data['amount'])) {

                    BookSpecialDiscount::updateOrCreate(
                        [
                            'book_id' => $this->book_id,
                            'type' => $type,
                        ],
                        [
                            'amount' => $data['amount'],
                            'duration_days' => $data['duration_days'] ?? null,
                        ]
                    );

                } else {

                    BookSpecialDiscount::where('book_id', $this->book_id)
                        ->where('type', $type)
                        ->delete();
                }
            }

            // =--------------end special discounts------------------------
            
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book').' ('.$book->name.' ID:'.$book->id.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------
            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));
        } catch (\Exception $e) {
            DB::rollBack();
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
            
            $book = Book::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book').' ('.$book->name.' ID:'.$book->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $book->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    
}
