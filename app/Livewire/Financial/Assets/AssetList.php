<?php

namespace App\Livewire\Financial\Assets;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;

use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Section;
use App\Models\CenterSettings\Unit;
use App\Models\Financial\AssetCategory;
use App\Models\Financial\Asset;
use App\Models\Financial\AssetMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Hr\Employee;
use Auth;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;
use Carbon\Carbon;
use DB;
class AssetList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 5;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'assets-list-addEditModal';
    public $assignModalId = 'assing-modal-addEditModal';
    public $table_name='assets';

    public $pdfOrientation = 'landscape';

    public $selectedFields = [
        'no',
        'name',
        'quantity',
        'code',
        'unit_id',
        'category_id',
        'purchase_price',
        'total_amount',
        'purchase_date',
        'note',
        'section_id',
        'status',
    ];
    
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
    public function closeAssignModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('close-modal', id: $this->assignModalId);

    }

    public function openAssignModal(){
        $this->resetInputFields();
        $this->resetValidation();
        $this->dispatch('open-modal', id: $this->assignModalId);
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
    public $branches=[], $categories=[], $sections = [],$units =[];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->categories =  AssetCategory::all();
        $this->sections =  Section::all();
        $this->units =  Unit::all();
        $this->assign_to_employees = Employee::all();
        $this->purchase_date = now()->format('Y-m-d');
    }

    public $name, 
    $asset_id,
    $quantity=1,
    $purchase_price,
    $purchase_date,
    $branch_id,
    $category_id,
    $section_id,
    $unit_id,
    $note;
    
    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'sections',
            'categories',
            'units',
            'purchase_date',
        ]);
    }

    public $search = [
            'name' => null,
            'section_id' => null,
            'category_id' => null,
            'branch_id' => null,
            'from' => null,
            'to' => null,
            'status' => null,
        ];

    public function render()
    {
        $assets = $this->loadAssets();

        return view(
            'livewire.financial.assets.asset-list',
            compact('assets')
        );
    }

    public function loadAssets(){
        $search = $this->search;
        $assets = Asset::with('branch','section','category','unit')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['section_id']), function ($query) {
            $query->where('section_id',$this->search['section_id']);
        })
        ->when(!empty($this->search['category_id']), function ($query) {
            $query->where('asset_category_id',$this->search['category_id']);
        })
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['status']), function ($query) {
            $query->where('status',$this->search['status']);
        })
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
            $from = Carbon::parse($search['from'])->startOfDay();
            $to   = Carbon::parse($search['to'])->endOfDay();

            $q->whereBetween('created_at', [$from, $to]);
        })
        ->orderBy('created_at','desc')
        ->paginate($this->perPage);

        return $assets;
    }

    public function exportPdf()
    {   
        $assets = $this->loadAssets();
        $total = $assets->sum('purchase_price');
        if (!auth()->user()->branch_id && !in_array('branch_id', $this->selectedFields)) {
            $this->selectedFields[] = 'branch_id';
        }
        $pdf = Pdf::loadView(
            'livewire.financial.assets.asset-list-pdf',
            [
                'assets' => $assets,
                'selectedFields' => $this->selectedFields,
                'search' =>$this->search,
                'total' =>$total,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.asset_list').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
            'name' => 'required',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'category_id' => 'required',
            'section_id' => 'required',
            'unit_id' => 'required',
            'purchase_date' => 'required',
            
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
            'quantity.required' => __('label.quantity.required'),
            'purchase_price.required' => __('label.purchase_price.required'),
            'category_id.required'   => __('label.category.required'),
            'section_id.required'   => __('label.section.required'),
            'unit_id.required'   => __('label.unit.required'),
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

            $asset = Asset::create([
                'name' => $this->name,
                'purchase_price' => $this->purchase_price,
                'unit_id' => $this->unit_id,
                'asset_category_id' => $this->category_id,
                'section_id' => $this->section_id,
                'purchase_date' => $this->purchase_date,
                'note' => $this->note,
                'branch_id'=> Auth::user()->branch_id ?: $this->branch_id,
                'user_id'=> Auth::user()->id,
            ]);

            // -----------start transaction-----------------------------
            $account_id = Account::where('branch_id', $asset->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }
            TransactionService::expense(
                $account_id,
                $asset->branch_id,
                $asset->purchase_price,
                TransactionCategory::ASSET,
                'Asset',
                $asset->id,
                $asset->section_id,
                Action::CREATE
            );
            // -----------start transaction-----------------------------

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.asset').' ('.$asset->name.' ID:'.$asset->id.')',
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
        $this->asset_id = $id;    
        $asset = Asset::find($id);
        $this->name = $asset->name;
        $this->quantity = $asset->quantity;
        $this->unit_id = $asset->unit_id;
        $this->category_id = $asset->asset_category_id;
        $this->purchase_price = $asset->purchase_price;
        $this->branch_id = $asset->branch_id;
        $this->section_id = $asset->section_id;
        $this->note = $asset->note;
        $this->purchase_date = $asset->purchase_date->format('Y-m-d');
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
        DB::beginTransaction();
        try {

            $asset = Asset::findOrFail($this->asset_id);
            // -----------start transaction-----------------------------
            $account_id = Account::where('branch_id', $asset->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }
            TransactionService::adjust(
                $account_id,
                'expense',
                $asset->branch_id,
                $asset->purchase_price,
                $this->purchase_price,
                TransactionCategory::CORRECTION,
                'Asset',
                $asset->id,
                $asset->section_id,
                Action::UPDATE
            );
            // -----------start transaction-----------------------------

            $asset->update([
                'name' => $this->name,
                'quantity' => $this->quantity,
                'purchase_price' => $this->purchase_price,
                'unit_id' => $this->unit_id,
                'asset_category_id' => $this->category_id,
                'section_id' => $this->section_id,
                'purchase_date' => $this->purchase_date,
                'note' => $this->note,
                'branch_id'=> Auth::user()->branch_id ?: $this->branch_id,
            ]);

            

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.asset').' ('.$asset->name.' ID:'.$asset->id.')',
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
        DB::beginTransaction();
        try {

            $asset = Asset::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.asset').' ('.$asset->name.' ID:'.$asset->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------

            // -----------start transaction-----------------------------
            $account_id = Account::where('branch_id', $asset->branch_id)
                    ->where('category', 'treasury')
                    ->where('type','branch')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }
            TransactionService::income(
                $account_id,
                $asset->branch_id,
                $asset->purchase_price,
                TransactionCategory::CORRECTION,
                'Asset',
                $asset->id,
                $asset->section_id,
                Action::DELETE
            );
            // -----------start transaction-----------------------------

            $asset->delete();
            
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
             DB::rollBack();
        $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }


    public $assign_date;
    public $assign_note;
    public $assign_to_employees = [];
    public $assign_to_employee_id;
    public function selectToAssign($id)
    {
        $this->resetValidation(); 
        $this->asset_id = $id;    
        $asset = Asset::find($id);
        $this->name = $asset->name;
        $this->assign_date = now()->format('Y-m-d');
        $this->editMode = true;
        $this->dispatch('open-modal', id: $this->assignModalId);
    }

    public function updatedAssignToEmployeeId()
    {
        $this->resetValidation('assign_to_employee_id');
    }
    public function updatedAssignDate()
    {
        $this->resetValidation('assign_to_employee_id');
    }

    public function assign()
    {
        if (!edit(Auth::user()->role_ids, $this->active_menu_id)) {
            return $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }

        $this->validate([
            'assign_to_employee_id'   => 'required|exists:employees,id',
      
            'assign_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {

            $asset = Asset::findOrFail($this->asset_id);

            $last_movement = AssetMovement::where('asset_id', $asset->id)
            ->latest('id')
            ->first();

            if ($last_movement?->type === 'assigned') {

                DB::rollBack();

                return $this->dispatch(
                    'alert',
                    type: 'warning',
                    message: __('label.asset_already_assigned')
                );
            }

            AssetMovement::create([
                'asset_id'      => $asset->id,
                'employee_id'   => $this->assign_to_employee_id,
                'section_id'    => $asset->section_id,
                'branch_id'     => $asset->branch_id,
                'type'          => 'assigned',
                'movement_date' => $this->assign_date,
                'note'          => $this->assign_note,
                'user_id'       => Auth::id(),
            ]);

            // اگر در جدول assets فیلد status داری
            $asset->update([
                'status' => 'assigned'
            ]);

            SystemLog::create([
                's_id' => $this->assign_to_employee_id,
                'user_id' => Auth::id(),
                'section' => __('label.asset_assignment') . ' (' . $asset->name . ' ID:' . $asset->id . ')',
                'type_id' => 2,
            ]);

            DB::commit();

            $this->closeAssignModal();

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



    // ---------asignments

    public $selected_asset_id;

    public function showAssignments($asset_id)
    {

        $this->selected_asset_id = $asset_id;

        $this->dispatch('open-modal', id: 'assignmentModal');
    }

    public function getAssignedAssetProperty()
    {
        $query = AssetMovement::with('asset','employee:id,name,last_name,employee_code')
            ->where('asset_id', $this->selected_asset_id)
            ->orderBy('created_at','desc');

        $paginated = $query->paginate(5, ['*'], 'assignmentPage');

        $lastId = AssetMovement::where('asset_id', $this->selected_asset_id)
            ->latest('id')
            ->value('id');

        foreach ($paginated as $item) {
            $item->is_last = ($item->id == $lastId);
        }

        return $paginated;
    }


    public $movement_id;
    public $return_date;
    public $return_note;
    public function assetReturn($id)
    {
        $this->resetValidation(); 

        $asset_movement = AssetMovement::findOrFail($id);

        $this->movement_id = $asset_movement->id;
        $this->return_date = now()->format('Y-m-d');
      
        $this->editMode = true;

        $this->dispatch('open-modal', id: 'assetReturnModal');
    }

    public function assetReturnStore()
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
                'note'          => $this->return_note,
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

            $this->movement_id = null;
            $this->return_date = null;
            $this->return_note = null;

            $this->dispatch('close-modal', id: 'assetReturnModal');

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
    
}
