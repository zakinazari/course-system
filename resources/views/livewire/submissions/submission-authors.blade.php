<div>
    <div class="col-sm-12 fv-plugins-icon-container">
        <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('label.contributor') }}</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.adding') }} {{ __('label.contributor') }}
                        </button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>{{ __('label.given_name') }}</th>
                            <th>{{ __('label.family_name') }}</th>
                            <th>{{ __('label.author_type') }}</th>
                            <th>{{ __('label.details') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($authors as $i => $author)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                @if(App::getLocale()==='en')
                                {{ $author->given_name_en }}
                                @else
                                {{ $author->given_name }}
                                @endif
                            </td>
                            <td>
                                @if(App::getLocale()==='en')
                                    {{ $author->family_name_en }}
                                @else
                                    {{ $author->family_name_fa }}
                                @endif
                            </td>
                            
                            <td>
                                @if(App::getLocale()==='en')
                                    {{ $author->type?->type_name_en }}
                                @else
                                    {{ $author->type?->type_name_fa }}
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="showDetails({{ $author->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                           </td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-icon  dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $author->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                    

                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $author->id }},'{{ $table_name }}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        <hr>
        <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-primary btn-prev" wire:click="$dispatch('prevStep')">
                <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                <span class="d-sm-inline-block d-none">{{ __('label.prev') }}</span>
                </button>
                <button class="btn btn-primary btn-next" wire:click="validateStep(3)">
                <span class="d-sm-inline-block d-none me-sm-1">{{ __('label.next') }}</span>
                <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                </button>
            </div>
    </div>

    <div class="modal fade modal-lg" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header ">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ __('label.contributor') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3" dir="rtl">
                                <label for="nameBasic" class="form-label">{{__('label.given_name')}} <span class="required" style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('given_name_fa') is-invalid @enderror" wire:model.lazy="given_name_fa">
                                @error('given_name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3" dir="ltr">
                                <label for="nameBasic" class="form-label" >{{__('label.given_name')}} ({{__('label.en',locale:'en')}})<span class="required" style="color:red;">*</span></label>
                                <input type="text" id="nameBasic" class="form-control @error('given_name_en') is-invalid @enderror" wire:model.lazy="given_name_en">
                                @error('given_name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" dir="rtl">
                                <label for="nameBasic" class="form-label">{{__('label.family_name')}}</label>
                                <input type="text" id="nameBasic" class="form-control @error('family_name_fa') is-invalid @enderror" wire:model.lazy="family_name_fa">
                                @error('family_name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3" dir="ltr">
                                <label for="nameBasic" class="form-label">{{__('label.family_name')}} ({{__('label.en',locale:'en')}})</label>
                                <input type="text" id="nameBasic" class="form-control @error('family_name_en') is-invalid @enderror" wire:model.lazy="family_name_en">
                                @error('family_name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="row">
                           <div class="col mb-3" >
                                <label class="form-label">{{ __('label.education_degree') }}</label>
                                <select id="education_degree_id" class="form-control" wire:model.lazy="education_degree_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($education_degrees as $degree)
                                        <option value="{{ $degree->id }}">
                                            {{ App::getLocale() === 'en' ? $degree->name_en : $degree->name_fa }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_degree_id') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>

                            <div class="col mb-3" >
                                <label class="form-label">{{__('label.academic_rank')}}</label>
                                <select id="academic_rank_id" class="form-control" wire:model.lazy="academic_rank_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($academic_ranks as $rank)
                                        <option value="{{ $rank->id }}">
                                            @if(App::getLocale()==='en')
                                                {{ $rank->name_en }}
                                            @else
                                                {{ $rank->name_fa }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_rank_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" dir="rtl">
                                <label for="nameBasic" class="form-label">{{__('label.department')}}</label>
                                <input type="text" id="nameBasic" class="form-control @error('department_fa') is-invalid @enderror" wire:model.lazy="department_fa">
                                @error('department_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3" dir="ltr">
                                <label for="nameBasic" class="form-label">{{__('label.department')}} ({{__('label.en',locale:'en')}})</label>
                                <input type="text" id="nameBasic" class="form-control @error('department_en') is-invalid @enderror" wire:model.lazy="department_en">
                                @error('department_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" dir="rtl">
                                <label for="nameBasic" class="form-label">{{__('label.preferred_research_area')}}</label>
                                <input type="text" id="nameBasic" class="form-control @error('preferred_research_area_fa') is-invalid @enderror" wire:model.lazy="preferred_research_area_fa">
                                @error('preferred_research_area_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3" dir="ltr">
                                <label for="nameBasic" class="form-label">{{__('label.preferred_research_area')}} ({{__('label.en',locale:'en')}})</label>
                                <input type="text" id="nameBasic" class="form-control @error('preferred_research_area_en') is-invalid @enderror" wire:model.lazy="preferred_research_area_en">
                                @error('preferred_research_area_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{__('label.email')}}</label>
                                <input type="email" id="nameBasic" class="form-control @error('email') is-invalid @enderror" wire:model.lazy="email">
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{__('label.phone')}}</label>
                                <input type="text" id="nameBasic" class="form-control @error('phone_no') is-invalid @enderror" wire:model.lazy="phone_no">
                                @error('phone_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" dir="rtl">
                                <label for="nameBasic" class="form-label">{{__('label.affiliation')}}</label>
                                <input type="text" id="nameBasic" class="form-control @error('affiliation_fa') is-invalid @enderror" wire:model.lazy="affiliation_fa">
                                @error('affiliation_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3" dir="ltr">
                                <label for="nameBasic" class="form-label">{{__('label.affiliation')}} ({{__('label.en',locale:'en')}})</label>
                                <input type="text" id="nameBasic" class="form-control @error('affiliation_en') is-invalid @enderror" wire:model.lazy="affiliation_en">
                                @error('affiliation_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                           <div class="col mb-3" wire:ignore>
                                <label class="form-label">{{ __('label.country') }}</label>
                                <select id="country_id" class="form-control" wire:model.defer="country_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">
                                            {{ App::getLocale() === 'en' ? $country->country_name_en : $country->country_name_fa }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>

                            <div class="col mb-3" >
                                <label class="form-label">{{__('label.province')}}</label>
                                <select id="province_id" class="form-control" wire:model.lazy="province_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">
                                            @if(App::getLocale()==='en')
                                            {{ $province->name_en }}
                                            @else
                                            {{ $province->name_fa }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('province_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" dir="rtl">
                                <label for="nameBasic" class="form-label">{{__('label.city')}} </label>
                                <input type="text" id="nameBasic" class="form-control @error('city_fa') is-invalid @enderror" wire:model.lazy="city_fa">
                                @error('city_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3" dir="ltr">
                                <label for="nameBasic" class="form-label">{{__('label.city')}} ({{__('label.en',locale:'en')}})</label>
                                <input type="text" id="nameBasic" class="form-control @error('city_en') is-invalid @enderror" wire:model.lazy="city_en">
                                @error('city_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3" >
                                <label class="form-label">{{__('label.author_type')}}</label>
                                <select id="author_type_id" class="form-control" wire:model.lazy="author_type_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($author_types as $type)
                                        <option value="{{ $type->id }}">
                                            @if(App::getLocale()==='en')
                                            {{ $type->type_name_en }}
                                            @else
                                            {{ $type->type_name_fa }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_type_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary">@if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalShowDetails" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">{{ __('label.details') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                  <div class="table-responsive text-nowrap">
                    <table class="table table-bordered">
                        @if($is_show_details)
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td style="width:30%;">{{ __('label.given_name') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->given_name_en: $author_details->given_name_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.family_name') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->family_name_en: $author_details->family_name_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.education_degree') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->educationDegree?->name_en: $author_details->educationDegree?->name_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.academic_rank') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->academicRank?->name_en: $author_details->academicRank?->name_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.department') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->department_en: $author_details->department_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.preferred_research_area') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->preferred_research_area_en: $author_details->preferred_research_area_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.email') }}</td>
                                <td>{{  $author_details->email }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.phone') }}</td>
                                <td>{{  $author_details->phone_no }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.affiliation') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->affiliation_en: $author_details->affiliation_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.country') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->country?->country_name_en: $author_details->country?->country_name_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.province') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->province?->name_en: $author_details->province?->name_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.city') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->city_en: $author_details->city_fa }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('label.author_type') }}</td>
                                <td>{{ App::getLocale()==='en' ? $author_details->type?->type_name_en: $author_details->type?->type_name_fa }}</td>
                            </tr>
                        </tbody>
                        @endif
                    </table>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                {{ __('label.close') }}
            </button>
            </div>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:load', function () {
    function initSelect2() {
        const el = $('#country_id');

        // فقط اگر select2 قبلاً init نشده باشد
        if (!el.hasClass("select2-hidden-accessible")) {
            el.select2({
                width: '100%',
                dropdownParent: $('#addEditModal') // اگر داخل modal
            });
        }

        // همگام‌سازی مقدار با Livewire
        el.off('change').on('change', function () {
            @this.set('country_id', $(this).val());
        });
    }

    initSelect2();

    // بعد از هر رندر Livewire دوباره init شود
    Livewire.hook('message.processed', (message, component) => {
        initSelect2();
    });

    // اگر modal باز می‌شود
    $('#addEditModal').on('shown.bs.modal', function () {
        initSelect2();
    });
});

</script>
@endpush









