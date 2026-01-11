<div>
   <div class="row">
        <div class="col-md-12">
    
            <div class="card mb-4">
            <h5 class="card-header">{{ __('label.profile') }}</h5>
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                <img
                    src="{{ asset('storage/' . $profile_photo_exists) }}"
                    alt="user-avatar"
                    class="d-block rounded"
                    height="100"
                    width="100"
                    id="uploadedAvatar" />
                    <div class="button-wrapper">

                        <ul class="list-unstyled">
                          <li class="mb-2">
                            <span class="fw-bold me-2">{{__('label.user_name')}}:</span>
                            <span class="text-muted">{{ Auth::user()->name }}</span>
                          </li>
                          <li class="mb-2">
                            <span class="fw-bold me-2">{{__('label.email')}}:</span>
                            <span class="text-muted">{{ Auth::user()->email }}</span>
                          </li>
                          <li class="mb-2">
                            <span class="fw-bold me-2">{{__('label.access_role')}}:</span>
                            <span class="text-muted">{{ Auth::user()->roles->pluck('role_name')->join(', ') }}</span>
                          </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <form id="formAccountSettings" wire:submit.prevent="update">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="name_fa" class="form-label">{{__('label.name')}}</label>
                        <input class="form-control" type="text"id="name_fa"  wire:model.lazy ='name_fa'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="name_en" class="form-label">{{__('label.name')}} ({{__('label.en',locale:'en')}})</label>
                        <input class="form-control" type="text"id="name_en" wire:model.lazy ='name_en'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="family_name_fa" class="form-label">{{__('label.family_name')}}</label>
                        <input class="form-control" type="text"  id="family_name_fa" wire:model.lazy ='family_name_fa'/>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="family_name_en" class="form-label">{{__('label.family_name')}} ({{__('label.en',locale:'en')}})</label>
                        <input class="form-control" type="text"  id="family_name_en" wire:model.lazy ='family_name_en'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="">{{ __('label.education_degree') }}</label>
                        <select id="" class=" form-select" wire:model.lazy = "education_degree_id">
                            <option value="">{{__('label.select')}}</option>
                            @foreach($education_degrees as $degree)
                            <option value="{{ $degree->id }}">@if(App::getLocale()==='en') {{ $degree->name_en }} @else {{ $degree->name_fa }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="">{{ __('label.academic_rank') }}</label>
                        <select id="" class=" form-select" wire:model.lazy = "academic_rank_id">
                            <option value="">{{__('label.select')}}</option>
                            @foreach($academic_ranks as $rank)
                            <option value="{{ $rank->id }}">@if(App::getLocale()==='en') {{ $rank->name_en }} @else {{ $rank->name_fa }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="department_fa" class="form-label">{{ __('label.department') }}</label>
                        <input type="text" class="form-control" id="department_fa" wire:model.lazy = 'department_fa'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="department_en" class="form-label">{{ __('label.department') }} ({{__('label.en',locale:'en')}})</label>
                        <input type="text" class="form-control" id="department_en" wire:model.lazy = 'department_en'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="preferred_research_area_fa" class="form-label">{{ __('label.preferred_research_area') }}</label>
                        <input type="text" class="form-control" id="preferred_research_area_fa" wire:model.lazy = 'preferred_research_area_fa'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="preferred_research_area_en" class="form-label">{{ __('label.preferred_research_area') }} ({{__('label.en',locale:'en')}})</label>
                        <input type="text" class="form-control" id="preferred_research_area_en" wire:model.lazy = 'preferred_research_area_en'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="affiliation_fa" class="form-label">{{ __('label.affiliation') }}</label>
                        <input type="text" class="form-control" id="affiliation_fa" wire:model.lazy = 'affiliation_fa'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="affiliation_en" class="form-label">{{ __('label.affiliation') }} ({{__('label.en',locale:'en')}})</label>
                        <input type="text" class="form-control" id="affiliation_en" wire:model.lazy = 'affiliation_en'/>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="country_id">{{ __('label.country') }}</label>
                        <select id="country_id" class=" form-select" wire:model.lazy = "country_id">
                            <option value="">{{__('label.select')}}</option>
                            @foreach($countries as $country)
                            <option value="{{ $country->id }}">@if(App::getLocale()==='en') {{ $country->country_name_en }} @else {{ $country->country_name_fa }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="province_id">{{ __('label.province') }}</label>
                        <select id="province_id" class=" form-select" wire:model.lazy = "province_id">
                            <option value="">{{__('label.select')}}</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province->id }}">@if(App::getLocale()==='en') {{ $province->name_en }} @else {{ $province->name_fa }} @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="city_fa" class="form-label">{{ __('label.city') }}</label>
                        <input type="text" class="form-control" id="city_fa" wire:model.lazy = 'city_fa'/>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="city_en" class="form-label">{{ __('label.city') }} ({{__('label.en',locale:'en')}})</label>
                        <input type="text" class="form-control" id="city_en" wire:model.lazy = 'city_en'/>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone_no">{{ __('label.phone') }}</label>
                        <input type="text"id="phone_no" class="form-control @error('phone_no') is-invalid @enderror" wire:model.lazy ='phone_no'/>
                        @error('phone_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                     <div class="mb-3 col-md-6">
                        <label for="formFile" class="form-label">{{ __('label.image') }}</label>
                        <input class="form-control @error('profile_photo') is-invalid @enderror" 
                            type="file" id="formFile" wire:model.lazy="profile_photo">
                        @error('profile_photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    
                </div>
                <div class="mt-2">
                  <button type="reset" class="btn btn-label-secondary">{{ __('label.cancel') }}</button>
                    <button type="submit" class="btn btn-primary me-2">{{ __('label.update') }}</button>
                </div>
                </form>
            </div>
            <!-- /Account -->
            </div>
            <div class="card mb-4">
              <h5 class="card-header">{{ __('label.change_password') }}</h5>
              <div class="card-body">
                <form wire:submit.prevent="updatePassword" >
                  <div class="row">
                    <div class="mb-3 col-md-6 form-password-toggle fv-plugins-icon-container">
                      <label class="form-label" for="currentPassword">{{ __('label.current_password') }}</label>
                      <div class="input-group input-group-merge has-validation">
                        <input wire:model.lazy = "current_password" class="form-control @error('current_password') is-invalid @enderror" type="password" name="currentPassword" id="currentPassword" placeholder="············">
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                      </div><div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                      @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                  </div>
                  <div class="row">
                    <div class="mb-3 col-md-6 form-password-toggle fv-plugins-icon-container">
                      <label class="form-label" for="newPassword">{{ __('label.new_password') }}</label>
                      <div class="input-group input-group-merge has-validation">
                        <input wire:model.lazy = "new_password" class="form-control @error('new_password') is-invalid @enderror" type="password" id="newPassword" name="newPassword" placeholder="············">
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                      </div><div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                       @error('new_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6 form-password-toggle fv-plugins-icon-container">
                      <label class="form-label" for="confirmPassword">{{ __('label.confirm_password') }}</label>
                      <div class="input-group input-group-merge has-validation">
                        <input wire:model.lazy = "new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" type="password" name="confirmPassword" id="confirmPassword" placeholder="············">
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                      </div><div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                       @error('new_password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 mb-4">
                      <p class="fw-medium mt-2">{{ __('label.password_requirements') }} :</p>
                      <ul class="ps-3 mb-0">
                        <li class="mb-1 small">{{ __('label.new_password') . ' ' . __('label.must_be_8') }}</li>
                        <li class="mb-1 small">{{  __('label.password_symbol') }} (!,@,#,$...)</li>
                        <li class="mb-1 small">{{ __('label.password_uppercase') }} (A,B,C...)</li>
                      </ul>
                    </div>
                    <div class="col-12 mt-1">
                      <button type="reset" class="btn btn-label-secondary">{{ __('label.cancel') }}</button>
                      <button type="submit" class="btn btn-primary me-2">{{ __('label.update') }}</button>
                    </div>
                  </div>
                  <input type="hidden">
              </form>
              </div>
            </div>
        </div>
    </div>
</div>
