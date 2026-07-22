<?php

namespace App\Livewire\Hr\Employees\AssignedAssets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\Hr\Position;
use App\Models\Hr\Employee;
use App\Models\Financial\Asset;
use App\Models\Financial\AssetMovement;
use Carbon\Carbon;
use Auth;
use DB;

class AssignedAssetList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'assigned-asset-list-addEditModal';
    public $table_name='asset_movements';
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
    public $branches = [];
    public $sections = [];

    public function mount($active_menu_id = null,$employee_id)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->employee_id = $employee_id;
    }

    public $employee_id;

    public $movement_id;

    public $return_date;
    public $note;

    public function resetInputFields(){
        
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'employees',
            'sections',
            'employee_id',
            'branches',
        ]);
    }
    public $search = [
            'employee_id' => null,
            'status' => null,
            'branch_id' => null,
        ];

    public function render()
    {
        $search = $this->search;
        $assigned_asset = AssetMovement::with('asset')
        ->where('employee_id',$this->employee_id)

        ->when(!empty($this->search['status']), function($q){

            $q->where('status', $this->search['status']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.hr.employees.assigned-assets.assigned-asset-list',compact('assigned_asset'));
    }

    protected function rules()
    {
        $rules = [
            'movement_id'   => 'required|exists:asset_movements,id',
            'return_date'   => 'required',
        ];

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'movement_id.required' => __('label.asset.required'),
            'return_date.required'   => __('label.date.required'),
        ];
    }


    public function edit($id)
    {
        $this->resetValidation(); 

        $asset_movement = AssetMovement::findOrFail($id);

        $this->movement_id = $asset_movement->id;
        $this->return_date = now()->format('Y-m-d');
      
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
    }

    public function update()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        DB::beginTransaction();

        try {

            $assigned = AssetMovement::findOrFail($this->movement_id);

            $last_movement = AssetMovement::where('asset_id', $assigned->asset_id)
                ->latest('id')
                ->first();

            if ($last_movement && $last_movement->type === 'returned') {

                DB::rollBack();

                return $this->dispatch(
                    'alert',
                    type: 'warning',
                    message: __('label.asset_already_returned')
                );
            }

            AssetMovement::create([
                'asset_id'      => $assigned->asset_id,
                'employee_id'   => $assigned->employee_id, 
                'section_id'    => $assigned->section_id,
                'branch_id'     => $assigned->branch_id,
                'type'          => 'returned',
                'movement_date' => $this->return_date,
                'note'          => $this->note,
                'user_id'       => Auth::id(),
            ]);

            $asset = Asset::findOrFail($assigned->asset_id);

            $asset->update([
                'status' => 'warehouse'
            ]);

            SystemLog::create([
                's_id' => $assigned->employee_id,
                'user_id' => Auth::id(),
                'section' => __('label.asset_returned') . ' (' . $asset->name . ' ID:' . $asset->id . ')',
                'type_id' => 2,
            ]);

            DB::commit();

            $this->closeModal();

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_done')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(    
                'alert',
                type: 'error',
                message: $e->getMessage()
            );
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
         DB::beginTransaction();
        try {
            
           
            // -----------start transaction-----------------------------

            // ---start system log-----------
            
            // ---end system log-------------
            
         

            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }

}
