<?php

namespace App\Livewire\Warehouse\BookInventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\BookInventory;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Auth;
class BookInventoryList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'book-inventory-list-addEditModal';
    public $table_name='book_inventories';
     public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'warehouse_id',
        'book_id',
        'quantity',
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
    public $warehouses, $books;
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

         $this->warehouses =  Warehouse::all();
        $this->books =  PhysicalBook::all();
    }

     public $warehouse_id,$name,$branch_id,$category_id;

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
        ];

    public function render()
    {
        $book_inventories = $this->loadInventory();
        return view('livewire.warehouse.book-inventory.book-inventory-list',compact('book_inventories'));
    }
    public function loadInventory(){
        $book_inventories = BookInventory::with('book','warehouse')
         ->when(!empty($this->search['book_id']), function ($query) {
            $query->whereHas('book', function($q) {
                $q->where('id', $this->search['book_id']);
            });
        })
        ->when(!empty($this->search['warehouse_id']), function ($query) {
            $query->whereHas('warehouse', function($q) {
                $q->where('warehouse_id', $this->search['warehouse_id']);
            });
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return $book_inventories;
    }
    public function exportPdf()
    {
        $book_inventories = $this->loadInventory();
        $pdf = Pdf::loadView(
            'livewire.warehouse.book-inventory.book-inventory-list-pdf',
            [
                'book_inventories' => $book_inventories,
                'selectedFields' => $this->selectedFields,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.book_inventory_list').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
            'name' => 'required|string|max:255|unique:warehouses,name,' . ($this->editMode ? $this->warehouse_id : 'NULL') . ',id',
            'category_id'=>'required',
            
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
            'branch_id.required' => __('label.branch.required'),
            'category_id.required' => __('label.category.required'),
        ];
    }
}
