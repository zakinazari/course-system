<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\Website\ScientificBoard;
use App\Models\Website\ScientificBoardMember;
use Storage;
use Auth;
use Carbon\Carbon;
use DB;
class ScientificBoardList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    use WithFileUploads;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'scientific-board-list-addEditModal';
    public $table_name='scientific_board';
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
         $this->dispatch('loadEditors'); 
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

         $this->members = [
            [
                'id' => '',
                'name_fa' => '',
                'name_en' => '',
            ]
        ];
    }

     
    public $board_id;
    public $name_fa;
    public $name_en;

    public $members = [];
    public $existing_members;

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
        ]);
    }

    public $search = [
            'identity' => null,
        ];

    public function render()
    {
         $scientific_board = ScientificBoard::query()
            ->when(!empty($this->search['identity']), function ($query) {
                $searchTerm = $this->search['identity'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_fa', 'like', "%{$searchTerm}%")
                    ->orWhere('name_en', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        return view('livewire.website.scientific-board-list',compact('scientific_board'));
        // return view('livewire.website.gazettes.gazette-list',compact('gazettes'));
    }

    public function addMember()
    {
        $this->members[] = [
            'id' => '',
            'name_fa' => '',
            'name_en' => '',
        ];
    }

    public function removeMember($index)
    {
        unset($this->members[$index]);
        $this->members = array_values($this->members);
    }

    protected function rules()
    {
        return [
            'name_fa' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',

            'members.*.name_fa' => 'required|string|max:255',
            'members.*.name_en' => 'required|string|max:255',
        ];
    }
    
    protected function messages()
    {
        return [
            'title_fa.required' => __('label.title.required'),
        ];
    }


    public function store()
    {
        
        if (add(Auth::user()->role_ids, $this->active_menu_id)) {
            
            $this->validate();
            DB::beginTransaction();
            try {

                $board = ScientificBoard::create([
                    'name_fa' => $this->name_fa,
                    'name_en' => $this->name_en,
                ]);

                if ($this->members) {
                    foreach ($this->members as $member) {
    
                        ScientificBoardMember::create([
                            'board_id' => $board->id,
                            'name_fa'  => $member['name_fa'],
                            'name_en'  => $member['name_en'],
                        ]);
                    }
                }
                DB::commit();
                $this->closeModal();
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));

            } catch (\Exception $e) {
                 DB::rollBack();
                $this->dispatch('alert', type: 'error', message: __('label.store_error').' : '. $e->getMessage());
            }

        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

    public function showMembers($id)
    {
       
        $this->existing_members =  ScientificBoardMember::where('board_id',$id)->get();

        $this->dispatch('open-modal', id: 'modalShowFiles');
    }


    public function edit($id)
    {
        $this->resetValidation();
        $board = ScientificBoard::with('members')->findOrFail($id);
        $this->board_id = $board->id;
        $this->name_fa  = $board->name_fa;
        $this->name_en  = $board->name_en;

        $this->members = $board->members
            ->map(function ($member) {
                return [
                    'id'      => $member->id,   
                    'name_fa' => $member->name_fa,
                    'name_en' => $member->name_en,
                ];
            })
            ->toArray();

        if (empty($this->members)) {
            $this->members[] = [
                'id'      => '',
                'name_fa' => '',
                'name_en' => '',
            ];
        }
        $this->editMode = true;

        $this->dispatch('open-modal', id: $this->modalId);
        $this->dispatch('loadEditors'); 
    }

   
    public function update()
    {
        if(!edit(Auth::user()->role_ids, $this->active_menu_id)){
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
            return;
        }

        $this->validate();

        DB::beginTransaction();
        try {

            $board = ScientificBoard::findOrFail($this->board_id);

            $board->update([
                'name_fa' => $this->name_fa,
                'name_en' => $this->name_en,
            ]);

             // گرفتن آی‌دی‌های اعضای فعلی فرم
            $submitted_ids = collect($this->members)
                ->pluck('id')
                ->filter()
                ->toArray();

            // حذف اعضایی که کاربر پاک کرده
            $board->members()
                ->whereNotIn('id', $submitted_ids)
                ->delete();

            if ($this->members) {

                foreach ($this->members as $member) {
                     if (!empty($member['id'])) {
                           
                            ScientificBoardMember::where('id', $member['id'])
                                ->update([
                                    'name_fa' => $member['name_fa'],
                                    'name_en' => $member['name_en'],
                                ]);

                        } else {

                            ScientificBoardMember::create([
                                'board_id' => $board->id,
                                'name_fa'  => $member['name_fa'],
                                'name_en'  => $member['name_en'],
                            ]);
                        }
                }
            }

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

        if (!isset($payload['table']) || $payload['table'] != $this->table_name) {
            return;
        }

        $this->delete($payload['id']);
    }

   public function delete($id)
    {
        if(delete(Auth::user()->role_ids, $this->active_menu_id)){
            try {
                $board = ScientificBoard::with('members')->findOrFail($id);
                $members= ScientificBoardMember::where('board_id',$id)->delete();
                $board->delete();
                SystemLog::create([
                    'user_id' => Auth::user()->id,
                    'section' => 'این بورد علمی توسط این کاربر حذف شده است. (ID: '.$id.')',
                    'type_id' => 4,
                ]);
                $this->dispatch('alert', type: 'success', message: __('label.successfully_deleted'));
            } catch (\Exception $e) {
                $this->dispatch('alert', type: 'error', message: __('label.delete_error').' : ' . $e->getMessage());
            }
        } else {
            $this->dispatch('alert', type: 'error', message: __('label.permission_message'));
        }
    }

}
