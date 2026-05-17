<?php

namespace App\Livewire\Warehouse\BookInventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\Warehouse\WarehouseCategory;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\BookInventory;
use App\Models\Warehouse\BookInventoryMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Enums\TransactionCategory;
use App\Enums\Action;
use App\Services\TransactionService;
use App\Models\Financial\Account;

use Carbon\Carbon;
use Auth;
use DB;
class BookTransferList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'book-transfer-list-addEditModal';
    public $table_name='book_inventory_movements';
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'warehouse_id',
        'book_id',
        'quantity_change',
        'balance_after',
        'unit_price',
        'created_at',
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
    public $central_warehouses = [];
    public $branch_warehouses = [];
    public $books = [];
    public $branches=[];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->books =  PhysicalBook::all();
        $this->branches =  Branch::all();
        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');

        $this->central_warehouses=Warehouse::where('type','central')->get();
        $this->branch_warehouses =  Warehouse::where('type','branch')
        ->where('branch_id',Auth::user()->branch_id)->get();
    }

    public $transfer_id;
    public $book_inventory_id;
    public $from_warehouse_id;
    public $to_warehouse_id;
    public $branch_id;
    public $book_id;
    public $quantity;
    public $unit_price;
    public $old_unit_price;
    public $old_quantity;
    public $type = 'transfer';
    public $transfer_group_id;
    public $note;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'central_warehouses',
            'branch_warehouses',
            'books',
            'branches',
        ]);
    }
    public $search = [
            'from_warehouse_id' => null,
            'to_warehouse_id' => null,
            'book_id' => null,
            'from' => null,
            'to' => null,
        ];
    public function loadBranchWarehouse($value){
        $this->branch_warehouses =  Warehouse::where('type','branch')
        ->where('branch_id',$value)->get();
    }
    
    public $available_quantity = null;

    public function updatedBookId($value)
    {
        $this->available_quantity = null;

        if (!$this->from_warehouse_id || !$value) {
            return;
        }

        $this->available_quantity = BookInventory::where('book_id', $value)

            ->whereHas('warehouse', function ($q) {

                $q->where('type', 'central')
                ->where('id', $this->from_warehouse_id);

            })

            ->value('quantity') ?? 0;
    }

    public function updatedFromWarehouseId($value)
    {
        $this->available_quantity = null;

        if (!$this->book_id || !$value) {
            return;
        }

        $this->available_quantity = BookInventory::where('book_id', $this->book_id)

            ->whereHas('warehouse', function ($q) use ($value) {

                $q->where('type', 'central')
                ->where('id', $value);

            })

            ->value('quantity') ?? 0;
    }


    public function render()
    {
        $transfers = $this->loadTransfers();
        return view('livewire.warehouse.book-inventory.book-transfer-list',compact('transfers'));
    }

    public function loadTransfers(){
        $search = $this->search;
        $transfers = BookInventoryMovement::with('inventory')
        ->when(!empty(Auth::user()->branch_id), function ($query) {

            $query->where('type', 'transfer_in');

        }, function ($query) {

            $query->where('type', 'transfer_out');
        })

        ->when(!empty($this->search['book_id']), function ($query) {
            $query->whereHas('inventory', function($q) {
                $q->where('book_id', $this->search['book_id']);
            });
        })
        ->when(!empty($this->search['from_warehouse_id']), function ($query) {
            $query->whereHas('inventory', function($q) {
                $q->where('warehouse_id', $this->search['from_warehouse_id']);
            });
        })
        ->when(!empty($this->search['to_warehouse_id']), function ($query) {
            $query->whereHas('inventory', function($q) {
                $q->where('warehouse_id', $this->search['to_warehouse_id']);
            });
        })
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
                $from = Carbon::parse($search['from'])->startOfDay();
                $to   = Carbon::parse($search['to'])->endOfDay();

                $q->whereBetween('created_at', [$from, $to]);
            })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return $transfers;
    }

    public function exportPdf()
    {
        $transfers = $this->loadTransfers();
        $pdf = Pdf::loadView(
            'livewire.warehouse.book-inventory.book-transfer-list-pdf',
            [
                'transfers' => $transfers,
                'selectedFields' => $this->selectedFields,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.book_transfer_list').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    

    protected function rules()
    {
        $rules =  [
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ];

        if(!$this->editMode){
            $rules['from_warehouse_id'] = 'required';
            $rules['to_warehouse_id'] = 'required';
            $rules['book_id'] = 'required';
        }

        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'quantity.required' => __('label.quantity.required'),
            'unit_price.required' => __('label.unit_price.required'),
            'from_warehouse_id.required' => __('label.central_warehouse.required'),
            'to_warehouse_id.required' => __('label.branch_warehouse.required'),
            'book_id.required' => __('label.book.required'),
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

            $transfer_group_id = \Str::uuid();

            /*
            |--------------------------------------------------------------------------
            | FROM inventory
            |--------------------------------------------------------------------------
            */
            $from_inventory = BookInventory::where('warehouse_id', $this->from_warehouse_id)
                ->where('book_id', $this->book_id)
                ->first();

            if (!$from_inventory) {
                return $this->dispatch('alert', type: 'error', message: __('label.inventory_not_found'));
            }

            if ($from_inventory->quantity < $this->quantity) {
                return $this->dispatch('alert', type: 'error', message: __('label.insufficient_stock'));
            }

            /*
            |--------------------------------------------------------------------------
            | TO inventory
            |--------------------------------------------------------------------------
            */
            $to_inventory = BookInventory::firstOrCreate(
                [
                    'warehouse_id' => $this->to_warehouse_id,
                    'book_id' => $this->book_id,
                ],
                [
                    'quantity' => 0
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | update inventories
            |--------------------------------------------------------------------------
            */
            $from_before = $from_inventory->quantity;
            $from_after = $from_before - $this->quantity;
            $from_inventory->update(['quantity' => $from_after]);

            $to_before = $to_inventory->quantity;
            $to_after = $to_before + $this->quantity;
            $to_inventory->update(['quantity' => $to_after]);

            /*
            |--------------------------------------------------------------------------
            | movement OUT
            |--------------------------------------------------------------------------
            */
            BookInventoryMovement::create([
                'book_inventory_id' => $from_inventory->id,
                'quantity_before' => $from_before,
                'quantity_change' => -$this->quantity,
                'balance_after' => $from_after,
                'unit_price' => $this->unit_price,
                'type' => 'transfer_out',
                'transfer_group_id' => $transfer_group_id,
                'note' => $this->note,
                'user_id' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | movement IN
            |--------------------------------------------------------------------------
            */
            BookInventoryMovement::create([
                'book_inventory_id' => $to_inventory->id,
                'quantity_before' => $to_before,
                'quantity_change' => $this->quantity,
                'balance_after' => $to_after,
                'unit_price' => $this->unit_price,
                'type' => 'transfer_in',
                'transfer_group_id' => $transfer_group_id,
                'note' => $this->note,
                'user_id' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([
                'user_id' => auth()->id(),
                'section' => __('label.book_transfer'),
                'type_id' => 2,
            ]);

            DB::commit();
            $this->closeModal();
            return $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 

        $purchase = BookInventoryMovement::findOrFail($id);

        $this->transfer_id = $id;
        $this->quantity = $purchase->quantity_change;
        $this->old_quantity = $purchase->quantity_change; 
        $this->book_inventory_id = $purchase->book_inventory_id;
        $this->unit_price = $purchase->unit_price;
        $this->old_unit_price = $purchase->unit_price;
        $this->transfer_group_id = $purchase->transfer_group_id;

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

            /*
            |--------------------------------------------------------------------------
            | find both movements by group
            |--------------------------------------------------------------------------
            */
            $out = BookInventoryMovement::where('transfer_group_id', $this->transfer_group_id)
                ->where('type', 'transfer_out')
                ->first();

            $in = BookInventoryMovement::where('transfer_group_id', $this->transfer_group_id)
                ->where('type', 'transfer_in')
                ->first();

            if (!$out || !$in) {
                throw new \Exception('Transfer records not found');
            }

            $from_inventory = BookInventory::findOrFail($out->book_inventory_id);
            $to_inventory = BookInventory::findOrFail($in->book_inventory_id);

            /*
            |--------------------------------------------------------------------------
            | no change check
            |--------------------------------------------------------------------------
            */
            if ($this->quantity == $this->old_quantity && $this->unit_price == $this->old_unit_price) {
                DB::commit();

                return $this->dispatch('alert', type: 'info', message: __('label.no_change_made'));
            }

            $diff = $this->quantity - $this->old_quantity;

            /*
            |--------------------------------------------------------------------------
            | adjust FROM inventory
            |--------------------------------------------------------------------------
            */
            $from_inventory->quantity -= $diff;

            if ($from_inventory->quantity < 0) {
                throw new \Exception(__('label.insufficient_stock'));
            }

            $from_inventory->save();

            /*
            |--------------------------------------------------------------------------
            | adjust TO inventory
            |--------------------------------------------------------------------------
            */
            $to_inventory->quantity += $diff;
            $to_inventory->save();

            /*
            |--------------------------------------------------------------------------
            | update movements
            |--------------------------------------------------------------------------
            */
            $out->update([
                'quantity_change' => -$this->quantity,
                'balance_after' => $from_inventory->quantity,
                'unit_price' => $this->unit_price,
            ]);

            $in->update([
                'quantity_change' => $this->quantity,
                'balance_after' => $to_inventory->quantity,
                'unit_price' => $this->unit_price,
            ]);

            /*
            |--------------------------------------------------------------------------
            | log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([
                'user_id' => auth()->id(),
                'section' => __('label.book_transfer'),
                'type_id' => 3,
            ]);

            DB::commit();
            $this->closeModal();
            return $this->dispatch('alert', type: 'success', message: __('label.successfully_updated'));

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->dispatch('alert', type: 'error', message: $e->getMessage());
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
        if (!delete(Auth::user()->role_ids, $this->active_menu_id)) {

            return $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.permission_message')
            );
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | transfer out movement
            |--------------------------------------------------------------------------
            */
            $out = BookInventoryMovement::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | group id
            |--------------------------------------------------------------------------
            */
            $group_id = $out->transfer_group_id;

            if (!$group_id) {
                throw new \Exception('Transfer group not found');
            }

            /*
            |--------------------------------------------------------------------------
            | movements
            |--------------------------------------------------------------------------
            */
            $out_movement = BookInventoryMovement::where('transfer_group_id', $group_id)
                ->where('type', 'transfer_out')
                ->first();

            $in_movement = BookInventoryMovement::where('transfer_group_id', $group_id)
                ->where('type', 'transfer_in')
                ->first();

            if (!$out_movement || !$in_movement) {
                throw new \Exception('Transfer movements not found');
            }

            /*
            |--------------------------------------------------------------------------
            | inventories
            |--------------------------------------------------------------------------
            */
            $from_inventory = BookInventory::findOrFail($out_movement->book_inventory_id);

            $to_inventory = BookInventory::findOrFail($in_movement->book_inventory_id);

            $qty = abs($out_movement->quantity_change);

            /*
            |--------------------------------------------------------------------------
            | stock validation
            |--------------------------------------------------------------------------
            */
            if ($to_inventory->quantity < $qty) {

                throw new \Exception(__('label.insufficient_stock'));
            }

            /*
            |--------------------------------------------------------------------------
            | revert source inventory
            |--------------------------------------------------------------------------
            */
            $from_inventory->quantity += $qty;
            $from_inventory->save();

            /*
            |--------------------------------------------------------------------------
            | revert destination inventory
            |--------------------------------------------------------------------------
            */
            $to_inventory->quantity -= $qty;
            $to_inventory->save();

            /*
            |--------------------------------------------------------------------------
            | delete movements
            |--------------------------------------------------------------------------
            */
            $out_movement->delete();

            $in_movement->delete();

            /*
            |--------------------------------------------------------------------------
            | log
            |--------------------------------------------------------------------------
            */
            SystemLog::create([
                'user_id' => Auth::id(),
                'section' => __('label.book_transfer') .
                    ' (' .
                    $from_inventory?->warehouse?->name .
                    ' -> ' .
                    $to_inventory?->warehouse?->name .
                    ')',
                'type_id' => 4,
            ]);

            DB::commit();

            $this->dispatch(
                'alert',
                type: 'success',
                message: __('label.successfully_deleted')
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch(
                'alert',
                type: 'error',
                message: __('label.delete_error') . ' : ' . $e->getMessage()
            );
        }
    }
}
