<?php

namespace App\Livewire\Submissions;

use Livewire\Component;
use App\Models\Settings\Menu;
use App\Models\Submissions\Submission;
use App\Models\Submissions\Keyword;
use App\Models\Submissions\SubmissionKeyword;
use Auth;
use DB;
class SubmissionDetails extends Component
{
    public $submission_id;
    public $title_fa;
    public $title_en;
    public $keywords_fa = [];
    public $keywords_en = [];
    public $abstract_fa;
    public $abstract_en;
    public function mount($menu_id = null,$submission_id = null)
    {
       
        $this->active_menu_id = $menu_id;
        // -------------start for activing menu in sidebar ----------------------
        $this->dispatch('setActiveMenuFromPage', $this->active_menu_id);
        
        $this->active_menu = Menu::with(['parent', 'grandParent', 'subMenu'])->find($this->active_menu_id);
        // -------------start for activing menu in sidebar ----------------------
     
        $this->submission_id = $submission_id;

        if ($this->submission_id) {
           
            $submission = Submission::find($this->submission_id);
            
            if ($submission) {
                $this->title_fa = $submission->title_fa;
                $this->abstract_fa = $submission->abstract_fa;
                $this->title_en = $submission->title_en;
                $this->abstract_en = $submission->abstract_en;

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
            'title_fa' => 'required|string|max:255|unique:submissions,title_fa,' . $this->submission_id . ',id',
            'title_en' => 'required|string|max:255|unique:submissions,title_en,' . $this->submission_id . ',id',
            'abstract_fa' => ['required', 'string', function ($attribute, $value, $fail) {
                $wordCount = $this->word_count_unicode($value);
                if ($wordCount < 180) {
                    $fail(__('label.abstract_wordcount_min', ['min' => 180]));
                } elseif ($wordCount > 200) {
                    $fail(__('label.abstract_wordcount_max', ['max' => 200]));
                }
            }],
            'abstract_en' => ['required', 'string', function ($attribute, $value, $fail) {
                $wordCount = $this->word_count_unicode($value);
                if ($wordCount < 180) {
                    $fail(__('label.abstract_wordcount_min', ['min' => 180]));
                } elseif ($wordCount > 200) {
                    $fail(__('label.abstract_wordcount_max', ['max' => 200]));
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

            // ---------- کلیدواژه انگلیسی ----------
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
            
            $submission->update([
                'title_fa'    => $this->title_fa,
                'title_en'    => $this->title_en,
                'abstract_fa' => $this->abstract_fa,
                'abstract_en' => $this->abstract_en,
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
}
