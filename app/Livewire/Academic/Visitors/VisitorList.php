<?php

namespace App\Livewire\Academic\Visitors;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Academic\Visitor;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\VisitPurpose;
use App\Models\CenterSettings\ReferralSource;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Auth;
class VisitorList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $branches=[];
    public $referral_sources=[];
    public $visit_purposes=[];
    public $modalId = 'visitor-list-addEditModal';
    public $table_name='visitors';
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

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
        $this->branches =  Branch::all();
        $this->visit_purposes = VisitPurpose::all();
        $this->referral_sources = ReferralSource::all();
    }

    public $name,$last_name,$father_name,$phone_no,$visit_date,$memo,
        $visitor_id, 
        $branch_id,
        $referral_source_id,
        $visit_purpose_id;

   
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'visit_purposes',
            'referral_sources',
        ]);
    }
    public $search = [
            'name' => null,
            'branch_id' => null,
            'visit_purpose_id' => null,
            'from' => null,
            'to' => null,
        ];


    public function render()
    {
        $visitors = Visitor::with('branch','referralSource','visitPurpose')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['visit_purpose_id']), function ($query) {
            $query->where('visit_purpose_id',$this->search['visit_purpose_id']);
        })
        ->when(!empty($this->search['from']), function ($query) {
            $query->whereDate('visit_date', '>=', $this->search['from']);
        })
        ->when(!empty($this->search['to']), function ($query) {
            $query->whereDate('visit_date', '<=', $this->search['to']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);
        if(!$this->editMode){
            $this->visit_date = now()->format('Y-m-d\TH:i');
        }
        return view('livewire.academic.visitors.visitor-list',compact('visitors'));
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required',
            'father_name' => 'required',
            'phone_no' => 'nullable|string|max:10',
            'visit_date' => 'required',
            'visit_purpose_id' => 'required',
            'referral_source_id' => 'required',
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
            'visit_date.required'   => __('label.visit_date.required'),
            'visit_purpose_id.required'   => __('label.visit_purpose.required'),
            'referral_source_id.required'   => __('label.referral_source.required'),
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

        try {

            $visitor = Visitor::create([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'visit_date' => $this->visit_date,
                'visit_purpose_id' => $this->visit_purpose_id,
                'referral_source_id' => $this->referral_source_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'user_id' => Auth::Id(),
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.visitor').' ('.$visitor->name.' ID:'.$visitor->id.')',
                'type_id' => 2,
            ]);
            // ---end system log-------------
            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.store_error') . ': ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $this->resetValidation(); 
        $this->visitor_id = $id;    
        $visitor = Visitor::find($id);
        $this->name = $visitor->name;
        $this->last_name = $visitor->last_name;
        $this->father_name = $visitor->father_name;
        $this->phone_no = $visitor->phone_no;
        $this->visit_date = $visitor->visit_date
        ? $visitor->visit_date->format('Y-m-d\TH:i')
        : null;
        $this->visit_purpose_id = $visitor->visit_purpose_id;
        $this->referral_source_id = $visitor->referral_source_id;
        $this->branch_id = $visitor->branch_id;
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
            $visitor = Visitor::findOrFail($this->visitor_id);

            $visitor->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'father_name' => $this->father_name,
                'phone_no' => $this->phone_no,
                'visit_date' => $this->visit_date,
                'visit_purpose_id' => $this->visit_purpose_id,
                'referral_source_id' => $this->referral_source_id,
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                // 'user_id' => Auth::Id(),
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.visitor').' ('.$visitor->name.' ID:'.$visitor->id.')',
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
            $visitor = Visitor::findOrFail($id);
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.visitor').'('.$visitor->name.' ID:'.$visitor->id.')',
                'type_id' => 4,
            ]);
            $visitor->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }


    public function exportPdf()
    {
        $defaultFields = [
            'no',
            'name',
            'last_name',
            'father_name',
            'phone_no',
            'visit_date',
            'visit_purpose_id',
            'referral_source_id',
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

        $query = Visitor::query()
            ->when(!empty($this->search['name']), fn($q) =>
                $q->where('name', 'like', "%{$this->search['name']}%")
            )
            ->when(!empty($this->search['status']), fn($q) =>
                $q->where('status',$this->search['status'])
            )
            ->when(!empty($this->search['branch_id']), fn($q) =>
                $q->where('branch_id',$this->search['branch_id'])
            )
            ->when(!empty($this->search['visit_purpose_id']), fn($q) =>
                $q->where('visit_purpose_id',$this->search['visit_purpose_id'])
            )
            ->when(!empty($this->search['from']), function ($query) {
                $query->whereDate('visit_date', '>=', $this->search['from']);
            })
            ->when(!empty($this->search['to']), function ($query) {
                $query->whereDate('visit_date', '<=', $this->search['to']);
            });

        if (in_array('reference', $fields)) {
            $query->with('reference');
        }

        if (in_array('branch', $fields)) {
            $query->with('branch');
        }
        if (in_array('visit_purpose_id', $fields)) {
            $query->with('visitPurpose');
        }
        if (in_array('referral_source_id', $fields)) {
            $query->with('referralSource');
        }

        if (!empty($realColumns)) {
            $query->select($realColumns);
        }

        $visitors = $query
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'livewire.academic.visitors.visitor-list-pdf',
            [
                'visitors' => $visitors,
                'fields' => $fields
            ]
        )->setPaper('a4', $this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'visitor-list-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }
}
