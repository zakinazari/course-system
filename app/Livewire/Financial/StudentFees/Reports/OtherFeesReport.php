<?php

namespace App\Livewire\Financial\StudentFees\Reports;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\CenterSettings\Branch; 
use App\Models\Financial\StudentOtherFee;
use App\Models\Financial\FeeType;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Auth;
use Carbon\Carbon;
use DB;
class OtherFeesReport extends Component
{
    // -------start generals--------------------

    protected $paginationTheme = 'bootstrap';   
    public $active_menu_id;
    public $active_menu;
    public $pdfOrientation = 'landscape';
    public $selectedFields = [
        'no',
        'branch',
        'student',
        'amount',
        'payment_date',
    ];

     // Hook for real time error message
    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function applySearch()
    {
        $this->fees = [];
        $this->dispatch('$refresh');
        $this->loadFeesPayment();
    }
    
    // ---------------------------------end generals-------------

    public $branches=[];
    public $fee_types=[];
    public function mount($active_menu_id = null)
    {
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $active_menu_id);
        $this->active_menu_id = $active_menu_id;
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($active_menu_id);
        // -------------start for activing menu in sidebar ----------------------

        $this->branches =  Branch::all();
        $this->fee_types = FeeType::all();
        $this->search['from'] = now()->format('Y-m-d');
        $this->search['to'] = now()->format('Y-m-d');

        if(auth()->user()->branch_id){
                $index = array_search('branch', $this->selectedFields);
                if($index !== false){
                unset($this->selectedFields[$index]);
                $this->selectedFields = array_values($this->selectedFields);
            }
        }
    }

    public function resetInputFields(){
        $this->resetExcept([
            'active_menu_id',
            'active_menu',
            'table_name',
            'modalId',
            'search',
            'branches',
            'fee_types',
        ]);
    }

    public $search = [
            'branch' => null,
            'fee_type_id' => null,
            'from' => null,
            'to' => null,
        ];

    public $fees=[];

    public function render()
    {
        return view('livewire.financial.student-fees.reports.other-fees-report');
    }

     public $total_payments=0;
     public $fee_type_name=null;
    public function loadFeesPayment(){

        $this->fees= StudentOtherFee::with('branch','student:id,student_code,name,last_name,father_name')
        ->when(!empty($this->search['branch_id']), function ($query) {
            $query->whereHas('branch',function($q){
                $q->where('branch_id',$this->search['branch_id']);
            });
        })
        ->when(!empty($this->search['fee_type_id']), function ($query) {
            $query->where('fee_type_id',$this->search['fee_type_id']);
        })
        ->whereBetween('payment_date',[$this->search['from'],$this->search['to']])
        ->get();
        $this->total_payments=$this->fees->sum('amount');

        $this->fee_type_name = $this->fee_types->where('id',$this->search['fee_type_id'])->pluck('name')->first();
    }

    public function print()
    {
        
        $this->dispatch('show-print-preview');
    }

    public function exportPdf()
    {
        
        $pdf = Pdf::loadView(
            'livewire.financial.student-fees.reports.other-fees-report-pdf',
            [
                'fees' => $this->fees,
                'selectedFields' => $this->selectedFields,
                'total_payments' => $this->total_payments,
                'fee_type_name' => $this->fee_type_name,
                'search' =>$this->search,
            ]
        )->setPaper('a4',$this->pdfOrientation)
        ->setOption('defaultFont', 'dejavusans');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'student-other-fees-' . Carbon::now()->format('Y-m-d -H-i-A') . '.pdf'
        );
    }

    protected function rules()
    {
        $rules =  [
           

        ];
        return $rules;
    }
}
