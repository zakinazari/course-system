<?php

namespace App\Livewire\CenterSettings\Books;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CenterSettings\Program;
use App\Models\CenterSettings\Book;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use Auth;
class BookList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $programs=[];
    public $modalId = 'book-list-addEditModal';
    public $table_name='books';
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

        $this->programs = Program::all();
    }

    public $name,$abbreviation,$book_id, $program_id,$status = 'active',$fee,$pass_mark,$excellent_mark;

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'programs',
        ]);
    }
    public $search = [
            'name' => null,
            'program_id' => null,
        ];
    
    public function render()
    {
    
        $books = Book::with('program')
        ->when(!empty($this->search['name']), function ($query) {
            $query->where('name', 'like', '%' . $this->search['name'] . '%');
        })
        ->when(!empty($this->search['program_id']), function ($query) {
            $query->where('program_id',$this->search['program_id']);
        })
        ->orderBy('id','desc')
        ->paginate($this->perPage);
        return view('livewire.center-settings.books.book-list',compact('books'));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:books,name,' . $this->book_id,
            'abbreviation' => 'required|string|max:255|unique:books,abbreviation,' . $this->book_id,
            'program_id' => 'required|',
        ];
    }
    // Localized messages
    protected function messages()
    {
        return [
            'name.required' => __('label.book_name.required'),
            'abbreviation.required' => __('label.abbreviation.required'),
            'name.string'   => __('label.book_name.string'),
            'name.max'      => __('label.book_name.max'),
            'name.unique'   => __('label.book_name.unique'),
            'program_id.required'   => __('label.program.required'),
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

            $book = Book::create([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'program_id' => $this->program_id,
                'status' => $this->status,
                'fee' => $this->fee,
                'pass_mark' => $this->pass_mark,
                'excellent_mark' => $this->excellent_mark,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book').' ('.$book->name.' ID:'.$book->id.')',
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
        $this->book_id = $id;    
        $book = Book::find($id);
        $this->name = $book->name;
        $this->abbreviation = $book->abbreviation;
        $this->program_id = $book->program_id;
        $this->status = $book->status;
        $this->fee = $book->fee;
        $this->pass_mark = $book->pass_mark;
        $this->excellent_mark = $book->excellent_mark;
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
            $book = Book::findOrFail($this->book_id);
            $book->update([
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'program_id' => $this->program_id,
                'status' => $this->status,
                'fee' => $this->fee,
                'pass_mark' => $this->pass_mark,
                'excellent_mark' => $this->excellent_mark,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book').' ('.$book->name.' ID:'.$book->id.')',
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
            
            $book = Book::findOrFail($id);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.book').' ('.$book->name.' ID:'.$book->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $book->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
