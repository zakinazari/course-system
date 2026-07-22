<?php

namespace App\Livewire\Logs\SystemLogs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settings\Menu;
use App\Models\Settings\SystemLog;
use App\Models\CenterSettings\Branch;
use App\Models\User;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Auth;
use DB;
class SystemLogList extends Component
{
     // -------start generals--------------------
    use WithPagination;
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';   
    public $editMode = false;
    public $active_menu_id;
    public $active_menu;
    public $modalId = 'system-log-list-addEditModal';
    public $table_name='system_logs';
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'user_id',
        'student_id',
        'employee_id',
        'section',
        'type_id',
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

    public $branches = [];
    public $users = [];

    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->users =  User::where('is_active',true)->get();
        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');
    }

     public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'users',
        ]);
    }
    public $search = [
            'branch_id' => null,
            'user_id' => null,
            'type_id' => null,
            'from' => null,
            'to' => null,
        ];

    public function render()
    {
        $logs = $this->loadLogs();

        return view('livewire.logs.system-logs.system-log-list',compact('logs'));
    }

    public function loadLogs($paginate = true){

        $search = $this->search;

        $query = SystemLog::with('user','student','employee')

        ->when(!empty($this->search['user_id']), function ($query) {
            
            $query->where('user_id',$this->search['user_id']);

        })

        ->when(!empty($this->search['type_id']), function ($query) {
            
            $query->where('type_id',$this->search['type_id']);

        })

        ->when(!empty($search['from']) && !empty($search['to']), function($q) use ($search){

                $from = Carbon::parse($search['from'])->startOfDay();
                $to   = Carbon::parse($search['to'])->endOfDay();
                $q->whereBetween('created_at', [$from, $to]);
            })

        ->orderBy('id','desc');

         return $paginate
        ? $query->paginate($this->perPage)
        : $query->get();
    }

    public function exportPdf()
    {
        
        $logs = $this->loadLogs(false);

        $pdf = Pdf::loadView(
            'livewire.logs.system-logs.system-log-list-pdf',
            [
                'logs' => $logs,
                'selectedFields' => $this->selectedFields,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            __('label.system_log_list').'-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
            
        ];


        return $rules;
    }
}
