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
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
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
        ];

    public function render()
    {
        $assets = $this->loadAssets();
        return view('livewire.financial.assets.asset-list',compact('assets'));
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
                'quantity' => $this->quantity,
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
}
