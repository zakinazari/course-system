<?php

namespace App\Livewire\CenterSettings\PhysicalBooks;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Book;
use App\Models\CenterSettings\PhysicalBook;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
class PhysicalBookList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $books=[];
    public $modalId = 'physical-book-list-addEditModal';
    public $table_name='physical_books';
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

        $this->books = Book::all();
    }

    public $name,$physical_book_id, $book_id,$price;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'books',
        ]);
    }
    public $search = [
            'name' => null,
            'book_id' => null,
        ];

    public function render()
    {
        $physical_books = PhysicalBook::with('book')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['book_id']), function ($query) {
            $query->where('book_id',$this->search['book_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);

        return view('livewire.center-settings.physical-books.physical-book-list',compact('physical_books'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:physical_books,name,' . $this->physical_book_id.',id',
            'book_id' => 'required',
            'price' => 'required',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.name.required'),
            'name.unique'   => __('label.name.unique'),
            'book_id.required'   => __('label.book.required'),
            'price.required'   => __('label.book.required'),
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

            $physical_book = PhysicalBook::create([
                'name' => $this->name,
                'book_id' => $this->book_id,
                'price' => $this->price,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.physical_book').' ('.$physical_book->name.' ID:'.$physical_book->id.')',
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
        $this->physical_book_id = $id;    
        $physical_book = PhysicalBook::find($id);
        $this->name = $physical_book->name;
        $this->book_id = $physical_book->book_id;
        $this->price = $physical_book->price;
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
            $physical_book = PhysicalBook::findOrFail($this->physical_book_id);
            $physical_book->update([
                'name' => $this->name,
                'book_id' => $this->book_id,
                'price' => $this->price,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.physical_book').' ('.$physical_book->name.' ID:'.$physical_book->id.')',
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
            
            $physical_book = PhysicalBook::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.physical_book').' ('.$physical_book->name.' ID:'.$physical_book->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $physical_book->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
