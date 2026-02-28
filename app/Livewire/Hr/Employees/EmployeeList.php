<?php

namespace App\Livewire\Hr\Employees;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeFile;
use App\Models\Hr\EmployeeRole;
use App\Models\CenterSettings\Branch;

class EmployeeList extends Component
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
    public $employee_roles=[];
    public $modalId = 'employee-list-addEditModal';
    public $table_name='employees';
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
        $this->employee_roles =  EmployeeRole::all();
    }

     public $name,$last_name,$father_name,$phone_no,$email,$tazkira_no,$hire_date,$address,
        $employee_id, 
        $branch_id,
        $photo,
        $role_ids=[],
        $status = 'new';

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'employee_roles',
        ]);
    }
    public $search = [
            'identity' => null,
            'role' => null,
            'branch_id' => null,
            'status' => null,
        ];


    public function render()
    {
        $employees = Employee::with('branch','photo','employeeRoles')
        ->when(!empty($this->search['identity']), function ($query) {
            $search = $this->search['identity'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('employee_code', 'like', "%{$search}%");
            });
        })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['role']), function ($query) {
                $query->whereHas('employeeRoles', function ($q) {
                        $searchTerm = $this->search['role'];
                        $q->where('employee_roles.id',$searchTerm);
                    });
            })
        ->orderBy('id','desc')
        ->paginate($this->perPage);
        if(!$this->editMode){
            $this->hir_date = now()->format('Y-m-d');
        }
        return view('livewire.hr.employees.employee-list',compact('employees'));
    }

     protected function rules()
    {
       $rules =  [
            'name' => 'required',
            'father_name' => 'required',
            'phone_no' => 'nullable|string|max:16|unique:visitors,phone_no,' . $this->employee_id,
            'email' => 'nullable|email|unique:employees,email,' . $this->employee_id,
            'tazkira_no' => 'nullable|email|unique:employees,tazkira_no,' . $this->employee_id,
            'role_ids' => 'required|array',
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
            'phone_no.unique'   => __('label.phone_no.unique'),
            'tazkira_no.unique'   => __('label.tazkira_no.unique'),
            'branch_id.required'   => __('label.branch.required'),
            'role_ids.required' => __('label.roles_required'),
            'role_ids.array'    => __('label.roles_array'),
            'email.required' => __('label.email_required'),
            'email.email'    => __('label.email_invalid'),
            'email.unique'   => __('label.email_unique'),
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

            $employee = Employee::create([
                'name'              => $this->name,
                'last_name'         => $this->last_name,
                'father_name'       => $this->father_name,
                'phone_no'          => $this->phone_no,
                'email'          => $this->email,
                'tazkira_no'        => $this->tazkira_no,
                'address'           => $this->address,
                'hire_date' => now(),
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'user_id'           => Auth::id(),
            ]);

            if(!empty($this->role_ids)){
                $employee->employeeRoles()->sync($this->role_ids);
            }

            if ($this->photo) {

                $manager = new ImageManager(new Driver());

                $folder = "employees/{$employee->id}";
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
                | Save In employee_files Table
                |--------------------------------------------------------------------------
                */
                $employee->files()->create([
                    'file_type'      => EmployeeFile::TYPE_PHOTO,
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
                's_id' => $employee->id,
                'section' => __('label.employee').' ('.$employee->name.' ID:'.$employee->employee_code.')',
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
        $this->employee_id = $id;    
        $employee = Employee::find($id);
        $this->name = $employee->name;
        $this->last_name = $employee->last_name;
        $this->father_name = $employee->father_name;
        $this->phone_no = $employee->phone_no;
        $this->email = $employee->email;
        $this->address = $employee->address;
        $this->branch_id = $employee->branch_id;
        $this->role_ids = $employee->employeeRoles?->pluck('id')->toArray();
    
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->modalId);
    }
    // Update role
    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate();

        DB::beginTransaction();

        try {
            $employee = Employee::findOrFail($this->employee_id);
            $employee->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'email' => $this->email,
                'tazkira_no' => $this->tazkira_no,
                'address' => $this->address,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
            ]);

            if(!empty($this->role_ids)){
                $employee->employeeRoles()->sync($this->role_ids);
            }
           
            if ($this->photo) {

                $manager = new ImageManager(new Driver());

                $folder = "employees/{$employee->id}";
                $filename = uniqid() . '.jpg';

                $oldPhoto = $employee->photo?->first();
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
                $employee->files()->create([
                    'file_type' => EmployeeFile::TYPE_PHOTO,
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
                's_id' => $employee->id,
                'section' => __('label.employee').' ('.$employee->name.' ID:'.$employee->employee_code.')',
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

            $employee = Employee::findOrFail($id);
            foreach ($employee->files as $file) {
                Storage::disk($employee->disk)->delete([$file->file_path, $file->thumbnail_path]);
            }
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                's_id' => $employee->id,
                'section' => __('label.employee').' ('.$employee->name.' ID:'.$employee->employee_code.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $employee->delete();
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
            'employee_code',
            'name',
            'last_name',
            'father_name',
            'phone_no',
            'email',
            'tazkira_no',
            'address',
            'hire_date',
            'status',
            'role',
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
            ->reject(fn($field) => in_array($field, ['no','role']))
            ->values()
            ->toArray();

        $query = Employee::query('employeeRoles', 'branch')
            ->when(!empty($this->search['identity']), function ($q) {
                $search = $this->search['identity'];

                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })
            ->when(!empty($this->search['branch_id']), fn($q) =>
                $q->where('branch_id',$this->search['branch_id'])
            )
            ->when(!empty($this->search['status']), fn($q) =>
                $q->where('status',$this->search['status'])
            )
            ->when(!empty($this->search['role']), function ($q) {
                $roleId = $this->search['role'];
                $q->whereHas('employeeRoles', function ($qq) use ($roleId) {
                    $qq->where('employee_roles.id', $roleId);
                });
            });

        if (in_array('branch_id', $fields)) {
            $query->with('branch');
        }

        if (in_array('role', $fields)) {
            $query->with('employeeRoles');
        }

        // if (!empty($realColumns)) {
        //     $query->select($realColumns);
        // }

        $employees = $query
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'livewire.hr.employees.employee-list-pdf',
            [
                'employees' => $employees,
                'fields' => $fields
            ]
        )->setPaper('a4', $this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'employee-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

}
