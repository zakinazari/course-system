<div>
    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='en') {{ $active_menu?->grandParent?->name_en }} @else {{ $active_menu?->grandParent?->name }}  @endif  /</span>
    @endif
    @if(!empty($active_menu?->parent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='en') {{ $active_menu?->parent?->name_en }} @else {{ $active_menu?->parent?->name }}  @endif /</span>
    @endif
    @if(App::getLocale()=='en') {{ $active_menu?->name_en }} @else {{ $active_menu?->name }}  @endif 
    </h4>
    <!-- end header -->

    <div class="card">
       
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">{{ __('label.search') }}</h5>

            <div class="d-flex align-items-center gap-2">

            </div>

        </div>
        <hr>
        <div class="table-responsive text-nowrap ">
 
            <div class="mb-3 px-3 mb-5">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                  
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.name') }} / {{ __('label.student_code') }} </label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.identity">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"> {{ __('label.father_name') }} </label>
                        <input type="text" class="form-control" placeholder="" wire:model="search.father_name">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                
                </form>
            </div>
            <div class="table-responsive text-nowrap">
                @if(count($students) > 0)
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:40px;">{{ __('label.NO') }}</th>
                            <th>{{ __('label.photo') }}</th>
                            <th> {{ __('label.student_code') }}</th>
                            <th>{{ __('label.name') }}</th>
                            <th>{{ __('label.last_name') }}</th>
                            <th>{{ __('label.father_name') }}</th>
                            <th>{{ __('label.financial_profile') }}</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                         @foreach($students as $i => $student)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up" aria-label="" data-bs-original-title="{{ $student->name }} {{ $student->last_name }}">
                                    <img src="{{ $student->photo?->thumbnail_url ?? asset('default.png') }}"
                                         class="rounded-circle">
                                    </li>
                                </ul>
                            </td>
                            <td>{{ $student->student_code }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>{{ $student->father_name }}</td>
                            <td>
                                <a class="btn btn-success"
                                    href="{{ route('student-financial-profile', [
                                            'menu_id'   => $this->active_menu_id,
                                            'student_id' => encrypt($student->id),
                                        ]) }}">
                                    {{ __('label.financial_profile') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
