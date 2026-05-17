<?php

namespace App\Livewire\Warehouse\BookInventory;
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
use Livewire\Component;

class BookPurchaseList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'purchase-list-addEditModal';
    public $table_name='book_inventory_movements';
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'warehouse_id',
        'book_id',
        'quantity_change',
        'balance_after',
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
    public $warehouses = [];
    public $books = [];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->warehouses =  Warehouse::where('type','central')->get();
        $this->books =  PhysicalBook::all();
        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');
    }

    public $purchase_id;
    public $book_inventory_id;
    public $warehouse_id;
    public $book_id;
    public $quantity;
    public $unit_price;
    public $old_unit_price;
    public $old_quantity;
    public $type = 'purchase';
    public $note;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'warehouses',
            'books',
        ]);
    }
    public $search = [
            'warehouse_id' => null,
            'book_id' => null,
            'from' => null,
            'to' => null,
        ];

    public function render()
    {
        $purchases = $this->loadPurchases();
        return view('livewire.warehouse.book-inventory.book-purchase-list',compact('purchases'));
    }
    public function loadPurchases(){
        $search = $this->search;
        $purchases = BookInventoryMovement::with('inventory')
        ->where('type','purchase')
        ->when(!empty($this->search['book_id']), function ($query) {
            $query->whereHas('inventory', function($q) {
                $q->where('book_id', $this->search['book_id']);
            });
        })
        ->when(!empty($this->search['warehouse_id']), function ($query) {
            $query->whereHas('inventory', function($q) {
                $q->where('warehouse_id', $this->search['warehouse_id']);
            });
        })
        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){
                $from = Carbon::parse($search['from'])->startOfDay();
                $to   = Carbon::parse($search['to'])->endOfDay();

                $q->whereBetween('created_at', [$from, $to]);
            })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return $purchases;
    }
    public function exportPdf()
    {
        $purchases = $this->loadPurchases();
        $pdf = Pdf::loadView(
            'livewire.warehouse.book-inventory.book-purchase-list-pdf',
            [
                'purchases' => $purchases,
                'selectedFields' => $this->selectedFields,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.book_purchase_list').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ];

        if(!$this->editMode){
            $rules['warehouse_id'] = 'required';
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
            'warehouse_id.required' => __('label.warehouse.required'),
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

            $inventory = BookInventory::firstOrCreate(
                [
                    'warehouse_id' => $this->warehouse_id,
                    'book_id' => $this->book_id,
                ],
                [
                    'quantity' => 0
                ]
            );

            $before = $inventory->quantity;

            // فقط افزایش
            $change = $this->quantity;

            $new_quantity = $before + $change;

            $inventory->update([
                'quantity' => $new_quantity
            ]);

            $movement = BookInventoryMovement::create([
                'book_inventory_id' => $inventory->id,
                'quantity_before' => $before,
                'quantity_change' => $change,
                'balance_after' => $new_quantity,
                'unit_price' => $this->unit_price,
                'type' => 'purchase',
                'note' => $this->note,
                'user_id' => auth()->id(),
            ]);
            
            // -----------start transaction-----------------------------
            $account_id = Account::where('type','central')
                    ->where('category', 'treasury')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }

            $total_amount = $this->quantity * $this->unit_price;

            $warehouse = $inventory->warehouse;
            
            TransactionService::expense(
                $account_id,
                null,
                $total_amount,
                TransactionCategory::BOOK_PURCHASE,
                'BookInventoryMovement',
                $movement->id,
                $warehouse->section_id,
                Action::CREATE
            );

            // -----------end transaction-----------------------------

            // // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.inventory').' ('.$inventory->wharehouse?->name.' ID:'.$inventory->id.')',
                'type_id' => 2,
            ]);
            // // ---end system log-------------
            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

        } catch (\Exception $e) {
           
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->resetValidation(); 

        $purchase = BookInventoryMovement::findOrFail($id);

        $this->purchase_id = $id;
        $this->quantity = $purchase->quantity_change;
        $this->old_quantity = $purchase->quantity_change; 
        $this->book_inventory_id = $purchase->book_inventory_id;
        $this->unit_price = $purchase->unit_price;
        $this->old_unit_price = $purchase->unit_price;

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

            // موجودی فعلی گدام
            $inventory = BookInventory::findOrFail($this->book_inventory_id);
            $before = $inventory->quantity;
            $inventory_amount = $inventory->quantity-$this->old_quantity;

           
            $quantity_change = (int) $this->quantity;

            if (
                ($this->quantity == $this->old_quantity) &&
                ($this->unit_price == $this->old_unit_price)
            ) {
                DB::commit();
                $this->closeModal();
                $this->dispatch('alert', type: 'info', message: __('label.no_change_made'));
                return;
            }

            $new_inventory_qty = $inventory_amount + $quantity_change;

            if ($new_inventory_qty < 0) {
                throw new \Exception('موجودی منفی نمی‌شود');
            }

            // آپدیت موجودی
            $inventory->update([
                'quantity' => $new_inventory_qty
            ]);

            $book_movement = BookInventoryMovement::findOrFail($this->purchase_id);
            $book_movement->balance_after = $new_inventory_qty;
            $book_movement->quantity_change = $this->quantity;
            $book_movement->unit_price = $this->unit_price;
            $book_movement->save();

            // -----------start transaction-----------------------------
            $account_id = Account::where('type','central')
                    ->where('category', 'treasury')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }

            $old_total = $this->old_quantity * $this->old_unit_price;
            $new_total = $this->quantity * $this->unit_price;

            $warehouse = $inventory->warehouse;
            
            TransactionService::adjust(
                $account_id,
                'expense',
                null,
                $old_total,
                $new_total,
                TransactionCategory::BOOK_PURCHASE,
                'BookInventoryMovement',
                $this->purchase_id,
                $warehouse->section_id,
                Action::UPDATE
            );

            // -----------end transaction-----------------------------

            
            // ثبت system log
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.inventory').' ('.$inventory->warehouse?->name.' ID:'.$inventory->id.')',
                'type_id' => 3,
            ]);

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
            $purchase = BookInventoryMovement::findOrFail($id);
            $inventory = BookInventory::findOrFail($purchase->book_inventory_id);
            $old_quantity = $inventory->quantity;
            $new_quantity = $old_quantity-$purchase->quantity_change;
            $inventory->quantity = $new_quantity;
            $inventory->save();

             // -----------start transaction-----------------------------
            $account_id = Account::where('type','central')
                    ->where('category', 'treasury')
                    ->value('id');

                if (!$account_id) {

                    return $this->dispatch(
                        'alert',
                        type: 'error',
                        message: __('label.treasury_account_not_found')
                    );
                }

            $total_amount = $purchase->quantity_change * $purchase->unit_price;

            $warehouse = $inventory->warehouse;
            
            TransactionService::income(
                $account_id,
                null,
                $total_amount,
                TransactionCategory::BOOK_PURCHASE,
                'BookInventoryMovement',
                $purchase->id,
                $warehouse->section_id,
                Action::DELETE
            );

            // -----------end transaction-----------------------------

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book_purchase_list').' ('.$inventory?->warehouse?->name.' ID:'.$purchase->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $purchase->delete();
            DB::commit();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
