<?php

namespace App\Livewire\CenterSettings\GeneralDiscounts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Financial\GeneralDiscount;
use App\Models\CenterSettings\Branch;
use App\Models\CenterSettings\Book;
use Auth;
use Illuminate\Validation\Rule;
class GeneralDiscountList extends Component
{
    // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'fee-type-list-addEditModal';
    public $table_name='fee_types';
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
    public $books,$branches;
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->books = Book::with('program')->orderBy('program_id','ASC')->get();
    }

     public $discount_id,$memo,$discount_amount,$book_id,$branch_id,$status='active';

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'books',
            'branches',
        ]);
    }
    public $search = [
            'memo' => null,
        ];

    public function render()
    {
        $general_discount = GeneralDiscount::with('book','branch')
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->where('branch_id',$this->search['branch_id']);
        })
        ->when(!empty($this->search['book_id']), function ($query) {
            $query->where('book_id',$this->search['book_id']);
        })
        ->paginate($this->perPage);
        return view('livewire.center-settings.general-discounts.general-discount-list',compact('general_discount'));
    }

    protected function rules()
    {
        $rules= [
            'book_id' => [
                'required',
                Rule::unique('general_discounts')
                    ->where(function ($query) {
                        return $query->where('branch_id', Auth::user()->branch_id ?: $this->branch_id);
                    })->ignore($this->discount_id)
            ],
            'memo' => 'required',
            'discount_amount' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->book_id) {
                        $book = Book::find($this->book_id);
                        if ($book && $value > $book->fee) {
                            $fail("Discount amount cannot be greater than the book fee ({$book->fee}).");
                        }
                    }
                }
            ],
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
            'branch_id.required' => __('label.branch.required'),
            'book_id.required' => __('label.book.required'),
            'book_id.unique' =>__('label.book_branch_id.unique'),
            'memo.required' => __('label.memo.required'),
            'discount_amount.required'   => __('label.discount_amount.required'),
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

            $general_discount = GeneralDiscount::create([
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'book_id' => $this->book_id,
                'discount_amount' => $this->discount_amount,
                'memo' => $this->memo,
                'status' => $this->status,
            ]);

            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.general_discount').' ('.$general_discount->memo.' ID:'.$general_discount->id.')',
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
        $this->discount_id = $id;    
        $general_discount = GeneralDiscount::find($id);
         $this->branch_id = $general_discount->branch_id;
         $this->book_id = $general_discount->book_id;
        $this->discount_amount = $general_discount->discount_amount;
        $this->memo = $general_discount->memo;
        $this->status = $general_discount->status;
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
            $general_discount = GeneralDiscount::findOrFail($this->discount_id);
            $general_discount->update([
                'branch_id' =>  Auth::user()->branch_id ?: $this->branch_id,
                'book_id' => $this->book_id,
                'discount_amount' => $this->discount_amount,
                'memo' => $this->memo,
                'status' => $this->status,
            ]);
            // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.general_discount').' ('.$general_discount->memo.' ID:'.$general_discount->id.')',
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
            $fee_type = FeeType::findOrFail($id);
             // ---start system log-----------
            SystemLog::create([
                'user_id' => Auth::user()->id,
                'section' => __('label.fee_type').' ('.$fee_type->name.' ID:'.$fee_type->id.')',
                'type_id' => 4,
            ]);
            // ---end system log-------------
            $fee_type->delete();
            $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
        }
    }
}
