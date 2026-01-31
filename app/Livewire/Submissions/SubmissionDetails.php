<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Submissions\Submission;
use App\Models\Submissions\AcceptedAbstract;
use App\Models\Submissions\Keyword;
use App\Models\Submissions\SubmissionKeyword;
use App\Models\Submissions\MainAxis;
use App\Models\Submissions\SubAxis;
use Auth;
use DB;
use App;
class SubmissionDetails extends Component
{
    public $submission_id;
    public $title_fa;
    public $title_en;
    public $keywords_fa = [];
    public $keywords_en = [];
    public $abstract_fa;
    public $abstract_en;

    public $main_axes = [];
    public $sub_axes = [];
    public $main_axis_id;
    public $sub_axis_id;

    public $accepted_abstracts = [];
    public $accepted_abstract_id;
    
    public function mount($menu_id = null,$submission_id = null)
    {
       
        $this->active_menu_id = $menu_id;
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
     
        $this->submission_id = $submission_id;
        $sumbission = Submission::findOrFail($this->submission_id);
        $this->main_axes = MainAxis::all();
        $this->sub_axes = SubAxis::where('main_axis_id',$sumbission->main_axis_id)->get();

        if ($this->submission_id) {
           
            $submission = Submission::find($this->submission_id);
            
            if ($submission) {
                $this->title_fa = $submission->title_fa;
                $this->abstract_fa = $submission->abstract_fa;
                $this->title_en = $submission->title_en;
                $this->abstract_en = $submission->abstract_en;
                $this->main_axis_id = $submission->main_axis_id;
                $this->sub_axis_id = $submission->sub_axis_id;

                $this->keywords_fa = $submission->keywords
                    ->whereNotNull('keyword_fa')
                    ->pluck('keyword_fa')
                    ->toArray();

                $this->keywords_en = $submission->keywords
                    ->whereNotNull('keyword_en')
                    ->pluck('keyword_en')
                    ->toArray();
            }
        }
    }
  
    public string $newKeyword_fa = '';
    public string $newKeyword_en = '';

    public function addKeywordFa()
    {
        $keyword = trim($this->newKeyword_fa);

        if ($keyword) {
            $this->keywords_fa[] = $keyword; 
        }

        $this->newKeyword_fa = ''; 
    }

    public function removeKeywordFa($index)
    {
        unset($this->keywords_fa[$index]);
        $this->keywords_fa = array_values($this->keywords_fa); 
    }

    public function addKeywordEn()
    {
        $keyword = trim($this->newKeyword_en);

        if ($keyword) {
            $this->keywords_en[] = $keyword; 
        }

        $this->newKeyword_en = ''; 
    }
    
    public function removeKeywordEn($index)
    {
        unset($this->keywords_en[$index]);
        $this->keywords_en = array_values($this->keywords_en); 
    }
    
  
    public function render()
    {
        return view('livewire.submissions.submission-details');
    }

    public function updated($propertyName)
    {
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    function word_count_unicode(string $text): int
    {
        $text = trim(strip_tags($text));

        if ($text === '') {
            return 0;
        }

        preg_match_all('/\p{L}+/u', $text, $matches);

        return count($matches[0]);
    }

    protected function rules()
    {
        $rules = [
            'main_axis_id' => 'required',
            'sub_axis_id' => 'required',
            
            'title_fa' => 'required|string|max:255|unique:submissions,title_fa,' . $this->submission_id . ',id',
            'title_en' => 'required|string|max:255|unique:submissions,title_en,' . $this->submission_id . ',id',
            
            'abstract_fa' => ['required', 'string', function ($attribute, $value, $fail) {
                $wordCount = $this->word_count_unicode($value);
                if ($wordCount < 150) {
                    $fail(__('label.abstract_wordcount_min', ['min' => 150]));
                } elseif ($wordCount > 300) {
                    $fail(__('label.abstract_wordcount_max', ['max' => 300]));
                }
            }],
            'abstract_en' => ['required', 'string', function ($attribute, $value, $fail) {
                $wordCount = $this->word_count_unicode($value);
                if ($wordCount < 150) {
                    $fail(__('label.abstract_wordcount_min', ['min' => 150]));
                } elseif ($wordCount > 300) {
                    $fail(__('label.abstract_wordcount_max', ['max' => 300]));
                }
            }],
            'keywords_fa' => 'nullable|array',
            'keywords_en' => 'nullable|array',

            'keywords_fa' => ['nullable', 'array', function ($attribute, $value, $fail) {

                $count = collect($value)
                    ->map(fn ($k) => is_array($k) ? trim($k['value'] ?? '') : trim($k))
                    ->filter()
                    ->unique()
                    ->count();

                if ($count < 4) {
                    $fail(__('label.keywords_min', ['min' => 4]));
                }

                if ($count > 7) {
                    $fail(__('label.keywords_max', ['max' => 7]));
                }
            }],

            
            'keywords_en' => ['nullable', 'array', function ($attribute, $value, $fail) {

                $count = collect($value)
                    ->map(fn ($k) => is_array($k) ? trim($k['value'] ?? '') : trim($k))
                    ->filter()
                    ->unique()
                    ->count();

                if ($count < 4) {
                    $fail(__('label.keywords_min', ['min' => 4]));
                }

                if ($count > 7) {
                    $fail(__('label.keywords_max', ['max' => 7]));
                }
            }],
        ];


        return $rules;
    }
    // Localized messages
    protected function messages()
    {
        return [
            'title_fa.required' => __('label.title.required'),
            'title_en.required' => __('label.title.required'),
            'title_fa.max' => __('label.title_fa.max'),
            'title_en.max' => __('label.title_en.max'),
            'main_axis_id.required' => __('label.main_axis.required'),
            'sub_axis_id.required' => __('label.sub_axis.required'),

            'abstract_fa.required' => __('label.abstract_required'),
            'abstract_en.required' => __('label.abstract_required'),
        ];
    }

    public function updateDetails($type=null)
    {
        
        $this->validate();

        DB::beginTransaction();

        try {

        $submission = Submission::findOrFail($this->submission_id);

            if ($this->accepted_abstract_id) {
                $abs = AcceptedAbstract::findOrFail($this->accepted_abstract_id);

                $this->title_fa = app()->getLocale() == 'fa'
                    ? $abs->title_fa
                    : $abs->title_en;
            }else{
                $abs = AcceptedAbstract::findOrFail($submission->accepted_abstract_id);
                $this->title_fa = app()->getLocale() == 'fa'
                    ? $abs->title_fa
                    : $abs->title_en;
            }
            
            $submission->update([
                'title_fa'    => $this->title_fa,
                'title_en'    => $this->title_en,
                'abstract_fa' => $this->abstract_fa,
                'abstract_en' => $this->abstract_en,
                'main_axis_id' => $this->main_axis_id,
                'sub_axis_id' => $this->sub_axis_id,
            ]);

            // -------- کلیدواژه‌ها ----------------
           $items_fa = collect($this->keywords_fa)
                ->map(fn($k) => is_array($k) ? trim($k['value'] ?? '') : trim($k))
                ->filter()
                ->unique();

            $keyword_ids_fa = $items_fa->map(function ($kw) {
                return Keyword::firstOrCreate(
                    ['keyword_fa' => $kw],
                    ['keyword_en' => null]
                )->id;
            })->toArray();

            // ----- انگلیسی -----
            $items_en = collect($this->keywords_en)
                ->map(fn($k) => is_array($k) ? trim($k['value'] ?? '') : trim($k))
                ->filter()
                ->unique();

            $keyword_ids_en = $items_en->map(function ($kw) {
                return Keyword::firstOrCreate(
                    ['keyword_en' => $kw],
                    ['keyword_fa' => null]
                )->id;
            })->toArray();

            // ----- merge و sync نهایی -----
            $submission->keywords()->sync(array_unique(array_merge($keyword_ids_fa, $keyword_ids_en)));

        
            DB::commit();

            $this->title_fa = $submission->title_fa;
            $this->abstract_fa = $submission->abstract_fa;

            $this->title_en = $submission->title_en;
            $this->abstract_en = $submission->abstract_en;

            $this->comment = $submission->comments_to_editor;
            $this->main_axis_id = $submission->main_axis_id;
            $this->sub_axis_id = $submission->sub_axis_id;

            $this->keywords_fa = $submission->keywords->whereNotNull('keyword_fa')->pluck('keyword_fa')->toArray();
            $this->keywords_en = $submission->keywords->whereNotNull('keyword_en')->pluck('keyword_en')->toArray();
            if($type ==='save'){
                $this->dispatch('alert', type: 'success', message: __('label.successfully_done'));
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: __('label.store_error').' : ' . $e->getMessage());
        }
    }


   protected $listeners = ['validate-step' => 'validateStep'];
    public function validateStep($step)
    {
        if ($step != 1) return;

        try {
            
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->dispatch('stepValidated', is_valid: false);
            return;
        }

        $this->dispatch('stepValidated', is_valid: true);
    }

    public function loadSubAxes($main_axis_id)
    {
        $this->sub_axes = SubAxis::where('main_axis_id', $main_axis_id)->get();
    }
    
    public function setTitle($id)
    {
        $abs= AcceptedAbstract::findOrFail($id);
        $this->title_fa = (App::getlocale() == 'fa') ? $abs->title_fa: $abs->title_en;
    }
}
