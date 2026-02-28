<?php

namespace App\Livewire\Academic\Students;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
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
    public $modalId = 'student-list-addEditModal';
    public $table_name='students';
    public $selectedFields = [];
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

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
    }

     public $name,$last_name,$father_name,$phone_no,$tazkira_no,$registration_date,$address,
        $st_id, 
        $branch_id,
        $photo,
        $status = 'new';
    public $visitor_id;
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
        ]);
    }
    public $search = [
            'identity' => null,
            'branch_id' => null,
            'status' => null,
        ];

    public function render()
    {
        $students = Student::with('branch','photo')
        ->when(!empty($this->search['identity']), function ($q) {
                $search = $this->search['identity'];

                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
                });
            })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);
        if(!$this->editMode){
            $this->registration_date = now()->format('Y-m-d\TH:i');
        }

        return view('livewire.academic.students.student-list',compact('students'));
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required',
            'father_name' => 'required',
            // 'phone_no' => 'nullable|string|max:16|unique:visitors,phone_no,' . $this->meeting_id,
            'phone_no' => 'nullable|string|max:10',
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
            'name.required' => __('label.name.required'),
            'father_name.string'   => __('label.father_name.required'),
            'phone_no.max'   => __('label.phone_no.max'),
            'branch_id.required'   => __('label.branch.required'),
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
                'name'              => $this->name,
                'last_name'         => $this->last_name,
                'father_name'       => $this->father_name,
                'phone_no'          => $this->phone_no,
                'tazkira_no'        => $this->tazkira_no,
                'address'           => $this->address,
                'registration_date' => now(),
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
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

            DB::rollBack();

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
        $this->name = $student->name;
        $this->last_name = $student->last_name;
        $this->father_name = $student->father_name;
        $this->phone_no = $student->phone_no;
        $this->address = $student->address;
        $this->branch_id = $student->branch_id;
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
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'tazkira_no' => $this->tazkira_no,
                'address' => $this->address,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
            ]);

           
            if ($this->photo) {

                $manager = new ImageManager(new Driver());

                $folder = "students/{$student->id}";
                $filename = uniqid() . '.jpg';

                $oldPhoto = $student->photo?->first();
                if ($oldPhoto) {
                    Storage::disk('public')->delete([$oldPhoto->file_path, $oldPhoto->thumbnail_path]);
                    $oldPhoto->delete();
                }

                //  تصویر اصلی
                $image = $manager->read($this->photo->getRealPath())
                    ->orient()
                    ->toJpeg(85);

                Storage::disk('public')->put("$folder/$filename", (string) $image);

                //  Thumbnail
                $thumbnailName = 'thumb_' . $filename;
                $thumbnail = $manager->read($this->photo->getRealPath())
                    ->orient()
                    ->cover(300, 300)
                    ->toJpeg(80);

                Storage::disk('public')->put("$folder/$thumbnailName", (string) $thumbnail);

                // ذخیره در student_files
                $student->files()->create([
                    'file_type' => StudentFile::TYPE_PHOTO,
                    'file_name' => $filename,
                    'file_path' => "$folder/$filename",
                    'thumbnail_path' => "$folder/$thumbnailName",
                    'mime_type' => $this->photo->getMimeType(),
                    'file_size' => $this->photo->getSize(),
                    'disk' => 'public',
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
                'user_id' => Auth::user()->id,
                'section' => 'Student ('.$student->name.' ID:'.$student->id.')',
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
        $defaultFields = [
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
        ];

         $fields = !empty($this->selectedFields)
            ? $this->selectedFields
            : $defaultFields;

        if (auth()->user()->isDeveloper() || auth()->user()->isAdmin()) {
            if (!in_array('branch_id', $fields)) {
                $fields[] = 'branch_id';
            }
        }
        $realColumns = collect($fields)
            ->reject(fn($field) => in_array($field, ['no']))
            ->values()
            ->toArray();

        $query = Student::query()
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
}
