<?php

namespace App\Livewire\Academic\Students;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Gender;
use App\Models\CenterSettings\Occupation;
use App\Models\Academic\Student;
use App\Models\Academic\StudentFile;
use App\Models\Academic\Visitor;
use App\Models\CenterSettings\Branch;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class StudentList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $branches=[];
    public $genders=[];
    public $occupations=[];
    public $modalId = 'student-list-addEditModal';
    public $table_name='students';
    public $selectedFields = [
         'no',
            'name',
            'student_code',
            'last_name',
            'father_name',
            'phone_no',
            'tazkira_no',
            'address',
            'registration_date',
            'status',
            'gender_id',
            'occupation_id',
            'branch_id',
    ];
    public $pdfOrientation = 'landscape';
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
 
    public function mount($active_menu_id = null,$student_id = null)
    {
        // if (request()->query('slug')) {

        //     $menu = Menu::with(['parent', 'grandParent', 'subMenu'])
        //         ->where('slug', request()->query('slug'))
        //         ->first();

        //     if ($menu) {
        //         $this->active_menu = $menu;
        //         $this->active_menu_id = $menu->id;

        //         $this->dispatch('setActiveMenuFromPage', $menu->id);
        //     }
        // } else {

            $this->dispatch('setActiveMenuFromPage', $active_menu_id);

            $this->active_menu_id = $active_menu_id;

            $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])
                ->find($active_menu_id);
        // }

        $this->branches =  Branch::all();
        $this->genders =  Gender::all();
        $this->occupations =  Occupation::all();

        if(auth()->user()->branch_id){
                $index = array_search('branch', $this->selectedFields);
                if($index !== false){
                unset($this->selectedFields[$index]);
                $this->selectedFields = array_values($this->selectedFields);
            }
        }

        if ($student_id) {
            $this->search['identity'] = Student::find($student_id)->student_code;

            $this->dispatch('replace-url', menuId: $this->active_menu_id);
        }

    }

    public $name,$last_name,$father_name,
    $phone_no,
    $whats_app,
    $father_whats_app,
    $tazkira_no,$registration_date,$address,
        $st_id, 
        $branch_id,
        $gender_id,
        $occupation_id,
        $photo,
        $status = 'new';
        
    public $student_code;
    public $visitor_id;

    public $name_fa;
    public $name_pa;
    public $last_name_fa;
    public $last_name_pa;
    public $father_name_fa;
    public $father_name_pa;
    public $date_of_birth;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'genders',
            'occupations',
        ]);
    }
    public $search = [
            'identity' => null,
            'branch_id' => null,
            'status' => null,
            'gender_id' => null,
            'occupation_id' => null,
        ];

    public function render()
    {
        $students = Student::with('branch','photo','gender','occupation')
        ->when(!empty($this->search['identity']), function ($q) {
                $search = $this->search['identity'];

                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%")
                    ->orWhere('phone_no', 'like', "%{$search}%");
                });
            })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
 
        ->when(!empty($this->search['gender_id']), function ($query) {
            $query->where('gender_id',$this->search['gender_id']);
        })
        ->when(!empty($this->search['occupation_id']), function ($query) {
            $query->where('occupation_id',$this->search['occupation_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.academic.students.student-list',compact('students'));
    }

    protected function rules()
    {
        $rules = [
            // 'student_code' => 'required|string|unique:students,student_code,' . $this->st_id . ',id',
            'name' => 'required',
            'father_name' => 'required',
            'gender_id' => 'required',
            // 'phone_no' => 'required|string|max:16|unique:students,phone_no,' . $this->st_id . ',id',
            'phone_no' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'
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
            'student_code.required' => __('label.student_code.required'),
            'name.required' => __('label.name.required'),
            'father_name.string'   => __('label.father_name.required'),
            'phone_no.max'   => __('label.phone_no.max'),
            'branch_id.required'   => __('label.branch.required'),
            'gender_id.required'   => __('label.gender.required'),
        ];
    }
    
    public function store()
    {
        if (!add(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $student = Student::create([
                'student_code'      => $this->student_code,
                'name'              => $this->name,
                'last_name'         => $this->last_name,
                'father_name'       => $this->father_name,
                'phone_no'          => $this->phone_no,
                'whats_app'         => $this->whats_app,
                'father_whats_app'  => $this->father_whats_app,
                'phone_no'          => $this->phone_no,
                'tazkira_no'        => $this->tazkira_no,
                'address'           => $this->address,

                'name_fa' => $this->name_fa,
                'name_pa' => $this->name_pa,
                'last_name_fa' => $this->last_name_fa,
                'last_name_pa' => $this->last_name_pa,
                'father_name_fa' => $this->father_name_fa,
                'father_name_pa' => $this->father_name_pa,
                'date_of_birth' => $this->date_of_birth,

                'registration_date' => now(),
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'gender_id'           =>$this->gender_id,
                'occupation_id'           =>$this->occupation_id,
                'user_id'           => Auth::id(),
            ]);


            if ($this->photo) {

                $manager = new ImageManager(new Driver());

                $folder = "students/{$student->id}";
                $disk   = 'public';

                $filename = uniqid('photo_') . '.jpg';
                $thumbnailName = 'thumb_' . $filename;

                $originalImage = $manager->read($this->photo->getRealPath())->orient();

                /*
                |--------------------------------------------------------------------------
                | Main Image (Optimized)
                |--------------------------------------------------------------------------
                */
                $mainImage = $originalImage
                    ->scaleDown(width: 1200) // جلوگیری از تصاویر خیلی بزرگ
                    ->toJpeg(85);

                Storage::disk($disk)->put(
                    "$folder/$filename",
                    (string) $mainImage
                );

                /*
                |--------------------------------------------------------------------------
                | Thumbnail
                |--------------------------------------------------------------------------
                */
                $thumbnail = $originalImage
                    ->cover(300, 300)
                    ->toJpeg(80);

                Storage::disk($disk)->put(
                    "$folder/$thumbnailName",
                    (string) $thumbnail
                );

                /*
                |--------------------------------------------------------------------------
                | Save In student_files Table
                |--------------------------------------------------------------------------
                */
                $student->files()->create([
                    'file_type'      => StudentFile::TYPE_PHOTO,
                    'file_name'      => $filename,
                    'file_path'      => "$folder/$filename",
                    'thumbnail_path' => "$folder/$thumbnailName",
                    'mime_type'      => 'image/jpeg',
                    'file_size'      => Storage::disk($disk)->size("$folder/$filename"),
                    'disk'           => $disk,
                    'uploaded_at'    => now(),
                ]);
            }
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $student->id,
                'section' => __('label.student').' ('.$student->name.' ID:'.$student->student_code.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
            DB::commit();

            $this->closeModal();

            $this->dispatch('alert',
                type: 'success',
                message: __('label.successfully_done')
            );

        } catch (\Exception $e) {

           DB::commit();

            $this->dispatch('alert',
                type: 'error',
                message: __('label.store_error') . ': ' . $e->getMessage()
            );
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->st_id = $id;    
        $student = Student::find($id);
        $this->student_code = $student->student_code;
        $this->name = $student->name;
        $this->last_name = $student->last_name;
        $this->father_name = $student->father_name;
        $this->phone_no = $student->phone_no;
        $this->whats_app = $student->whats_app;
        $this->father_whats_app = $student->father_whats_app;
        $this->address = $student->address;
        $this->branch_id = $student->branch_id;
        $this->gender_id = $student->gender_id;
        $this->occupation_id = $student->occupation_id;

        $this->name_fa = $student->name_fa;
        $this->name_pa = $student->name_pa;
        $this->last_name_fa = $student->last_name_fa;
        $this->last_name_pa = $student->last_name_pa;

        $this->father_name_fa = $student->father_name_fa;
        $this->father_name_pa = $student->father_name_pa;

        $this->date_of_birth = $student->date_of_birth?->format('Y-m-d');
        
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update(Student $student)
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {

            $student = Student::find($this->st_id);

            $student->update([
                'student_code'      => $this->student_code,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'whats_app'         => $this->whats_app,
                'father_whats_app'  => $this->father_whats_app,
                'tazkira_no' => $this->tazkira_no,
                'address' => $this->address,
                'gender_id' => $this->gender_id,
                'occupation_id' => $this->occupation_id,
                
                'name_fa' => $this->name_fa,
                'name_pa' => $this->name_pa,
                'last_name_fa' => $this->last_name_fa,
                'last_name_pa' => $this->last_name_pa,
                'father_name_fa' => $this->father_name_fa,
                'father_name_pa' => $this->father_name_pa,
                'date_of_birth' => $this->date_of_birth,

                'branch_id' =>   $this->branch_id ?:Auth::user()->branch_id,
            ]);

           
           if ($this->photo) {

            $manager = new ImageManager(new Driver());

            $folder = "students/{$student->id}";
            $filename = uniqid() . '.jpg';

            $student->load('photo');

            $oldPhoto = $student->photo;

            if ($oldPhoto) {

                Storage::disk('public')->delete([
                    $oldPhoto->file_path,
                    $oldPhoto->thumbnail_path
                ]);

                $oldPhoto->delete();
            }

            // تصویر اصلی
            $image = $manager->read($this->photo->getRealPath())
                ->orient()
                ->toJpeg(85);

            Storage::disk('public')->put(
                "$folder/$filename",
                (string) $image
            );

            // Thumbnail
            $thumbnailName = 'thumb_' . $filename;

            $thumbnail = $manager->read($this->photo->getRealPath())
                ->orient()
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->toJpeg(80);

            Storage::disk('public')->put(
                "$folder/$thumbnailName",
                (string) $thumbnail
            );

            // ذخیره در student_files
            $student->files()->create([
                'file_type'      => StudentFile::TYPE_PHOTO,
                'file_name'      => $filename,
                'file_path'      => "$folder/$filename",
                'thumbnail_path' => "$folder/$thumbnailName",
                'mime_type'      => $this->photo->getMimeType(),
                'file_size'      => $this->photo->getSize(),
                'disk'           => 'public',
            ]);
        }

             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'st_id' => $student->id,
                'section' => __('label.student').' ('.$student->name.' ID:'.$student->student_code.')',
                'type_id' => 3,
            ]);
            // ---end system log-------------

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.update_error') . ': ' . $e->getMessage());
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

            $student = Student::findOrFail($id);
            foreach ($student->files as $file) {
                Storage::disk($file->disk)->delete([$file->file_path, $file->thumbnail_path]);
            }
            SystemLog::create([
                'st_id' => $student->id,
                'user_id' => Auth::user()->id,
                'section' => 'Student ('.$student->name.' ID:'.$student->student_code.')',
                'type_id' => 4,
            ]);
            $student->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

    public function setInfo($id)
    {
        $visitor= Visitor::find($id);
        $this->name = $visitor->name;
        $this->last_name = $visitor->last_name;
        $this->father_name = $visitor->father_name;
        $this->phone_no = $visitor->phone_no;
    }

   public function exportPdf()
    {
      

         $fields = $this->selectedFields;


        if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
            if (!in_array('branch_id', $fields)) {
                $fields[] = 'branch_id';
            }
        }
        $realColumns = collect($fields)
            ->reject(fn($field) => in_array($field, ['no']))
            ->values()
            ->toArray();

        $query = Student::with('branch','gender','occupation')
            ->when(!empty($this->search['identity']), function ($q) {
                $search = $this->search['identity'];

                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            })
            ->when(!empty($this->search['branch_id']), fn($q) =>
                $q->where('branch_id',$this->search['branch_id'])
            )
            ->when(!empty($this->search['status']), fn($q) =>
                $q->where('status',$this->search['status'])
            )
            ->when(!empty($this->search['gender_id']), fn($q) =>
                $q->where('gender_id',$this->search['gender_id'])
            )
            ->when(!empty($this->search['occupation_id']), fn($q) =>
                $q->where('occupation_id',$this->search['occupation_id'])
            );

        if (in_array('branch_id', $fields)) {
            $query->with('branch');
        }

        if (!empty($realColumns)) {
            $query->select($realColumns);
        }

        $students = $query
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'livewire.academic.students.student-list-pdf',
            [
                'students' => $students,
                'fields' => $fields
            ]
        )->setPaper('a4', $this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'student-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }
}
