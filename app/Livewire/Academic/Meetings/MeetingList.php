<?php

namespace App\Livewire\Academic\Meetings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Meeting;
use App\Models\Academic\Visitor;
use App\Models\CenterSettings\Branch;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use DB;
use App\Notifications\NewMeetingNotification;
class MeetingList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $branches=[];
    public $references=[];
    public $modalId = 'meeting-list-addEditModal';
    public $table_name='meetings';
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

    public $highlightMeetingId;
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
        $this->references = User::with('role')
        ->whereHas('role',function($query){
           $query->where('is_system', false);
        })
        ->get();

        $this->highlightMeetingId = request()->query('meeting');
    }

    public $name,$last_name,$father_name,$phone_no,$date,$subject,
        $meeting_id, 
        $branch_id,
        $reference_id,
        $status = 'pending';
    public $visitor_id;
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'references',
        ]);
    }
    public $search = [
            'name' => null,
            'branch_id' => null,
            'status' => null,
            'reference_id' => null,
        ];


    public function render()
    {
        $user = auth()->user();

        $canManageMeetings =
            add($user->role_ids, $this->active_menu_id) ||
            edit($user->role_ids, $this->active_menu_id) || 
            delete($user->role_ids, $this->active_menu_id);

        $meetings = Meeting::with('branch','reference')

            ->when(!$canManageMeetings, function ($query) use ($user) {
                
                $query->where('reference_id', $user->id);
            })

            ->when(!empty($this->search['name']), function ($query) {
                $query->where('name', 'like', '%' . $this->search['name'] . '%');
            })

            ->when(!empty($this->search['branch_id']), function ($query) {
                $query->where('branch_id',$this->search['branch_id']);
            })

            ->when(!empty($this->search['reference_id']), function ($query) {
                $query->where('reference_id',$this->search['reference_id']);
            })

            ->when(!empty($this->search['status']), function ($query) {
                $query->where('status',$this->search['status']);
            })

            ->orderBy('id','desc')
            ->paginate($this->perPage);

        if(!$this->editMode){
            $this->date = now()->format('Y-m-d\TH:i');
        }

        return view('livewire.academic.meetings.meeting-list', compact('meetings'));
    }

    protected function rules()
    {
        $rules= [
            'name' => 'required',
            'father_name' => 'required',
            // 'phone_no' => 'nullable|string|max:16|unique:visitors,phone_no,' . $this->meeting_id,
            'phone_no' => 'nullable|string|max:10',
            'subject' => 'required',
            'date' => 'required',
            'reference_id' => 'required',
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
            'phone_no.unique'   => __('label.phone_no.unique'),
            'phone_no.max'   => __('label.phone_no.max'),
            'phone_no.unique'   => __('label.phone_no.unique'),
            'subject.required'   => __('label.subject.required'),
            'date.required'   => __('label.date.required'),
            'reference_id.required'   => __('label.meeting_reference.required'),
            'branch_id.required'   => __('label.branch.required'),
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

            $meeting = Meeting::create([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'subject' => $this->subject,
                'date' => $this->date,
                'reference_id' => $this->reference_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'user_id' => Auth::Id(),
            ]);

            $meeting->reference?->notify(
                new NewMeetingNotification($meeting,$this->active_menu_id)
            );
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.meeting').' ('.$meeting->name.' ID:'.$meeting->id.')',
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
        $this->meeting_id = $id;    
        $meeting = Meeting::find($id);
        $this->name = $meeting->name;
        $this->last_name = $meeting->last_name;
        $this->father_name = $meeting->father_name;
        $this->phone_no = $meeting->phone_no;
        $this->subject = $meeting->subject;
        $this->date = $meeting->date
        ? $meeting->date->format('Y-m-d\TH:i')
        : null;
        $this->reference_id = $meeting->reference_id;
        $this->branch_id = $meeting->branch_id;
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

            $meeting = Meeting::findOrFail($this->meeting_id);
            $meeting->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'subject' => $this->subject,
                'date' => $this->date,
                'reference_id' => $this->reference_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                // 'user_id' => Auth::Id(),
            ]);
            
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.meeting').' ('.$meeting->name.' ID:'.$meeting->id.')',
                'type_id' => 3,
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
            $meeting = Meeting::findOrFail($id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => 'Meeting ('.$meeting->name.' ID:'.$meeting->id.')',
                'type_id' => 4,
            ]);
            $meeting->delete();
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
            'last_name',
            'father_name',
            'phone_no',
            'subject',
            'date',
            'status',
            'reference_id',
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

        $query = Meeting::query()
            ->when(!empty($this->search['name']), fn($q) =>
                $q->where('name', 'like', "%{$this->search['name']}%")
            )
            ->when(!empty($this->search['status']), fn($q) =>
                $q->where('status',$this->search['status'])
            )
            ->when(!empty($this->search['branch_id']), fn($q) =>
                $q->where('branch_id',$this->search['branch_id'])
            );

        if (in_array('reference', $fields)) {
            $query->with('reference');
        }

        if (in_array('branch', $fields)) {
            $query->with('branch');
        }

        if (!empty($realColumns)) {
            $query->select($realColumns);
        }

        $meetings = $query
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'livewire.academic.meetings.meeting-list-pdf',
            [
                'meetings' => $meetings,
                'fields' => $fields
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'meeting-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    public $showStatusModal = false;
    public $selectedMeetingId;
    public $newStatus;

    public function openStatusModal($meetingId, $status)
    {
        $this->selectedMeetingId = $meetingId;
        $this->newStatus = $status;
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        $meeting = Meeting::find($this->selectedMeetingId);

        if ($meeting) {
            $meeting->update([
                'status' => $this->newStatus
            ]);
        }

        $this->showStatusModal = false;
        $this->highlightMeetingId = null;
        session()->flash('message', 'Status updated successfully.');
    }
}
