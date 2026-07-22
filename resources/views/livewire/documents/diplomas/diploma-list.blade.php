
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
       
       <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $active_menu?->name }}
                </h5>
            
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <!-- Export Button -->
                    

                    <!-- Add New Record Button -->
                    @if(add(Auth::user()->role_ids,$active_menu_id))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#{{$modalId}}" wire:click="openModal">
                            <i class="bi bi-plus-lg"></i> {{ __('label.add_new_record') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.name') }} / {{ __('label.student_code') }}" wire:model="search.identity">
                    </div>
                     @if(!auth()->user()->branch_id)
                    <div class="col-md-2">
                        <label class="form-label">{{ __('label.branch') }}</label>
                        <select class="form-select" wire:model.defer="search.branch_id" id ="">
                           <option value="">{{ __('label.all') }}</option>
                           @foreach($branches as $branch)
                                 <option value="{{ $branch->id }}"  wire:key="branch-search-{{ $branch->id }}">
                                    {{ $branch->name }}
                                 </option>
                           @endforeach
                        </select>
                     </div>
                     @endif
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('label.search') }}
                        </button>
                    </div>
                </form>

                <!-- perPage -->
                <div class="d-flex align-items-center gap-1 mt-3 justify-content-end">
                    <span>{{ __('label.show') }}</span>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>{{ __('label.entries') }}</span>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('label.NO') }}</th>
                            <th>
                                
                                {{ __('label.photo') }}
                            </th>
                            <th>{{ __('label.student_code') }}</th>
                            <th>{{ __('label.name') }}</th>

                            <th>{{ __('label.serial_number') }}</th>

                            <th>{{ __('label.graduation_date') }}</th>
                            <th>{{ __('label.status') }}</th>
                            @if(!auth()->user()->branch_id)
                            <th>{{ __('label.branch') }}</td>
                            @endif
                            <th>{{ __('label.view_diploma') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($diplomas as $i => $diploma)
                        <tr>
                            <td>{{ ($diplomas->currentPage() - 1) * $diplomas->perPage() + $i + 1 }}</td>
                           <td>
                                <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" class="avatar avatar-xs pull-up" aria-label="" data-bs-original-title="{{ $diploma->student->name }} {{ $diploma->student->last_name }}">
                                    <img src="{{ $diploma->student->photo?->thumbnail_url ?? asset('default.png') }}"
                                         class="rounded-circle">
                                    </li>
                                </ul>
                            </td>
                            <td>{{ $diploma->student?->student_code }}</td>

                            <td>{{ $diploma->student?->name }}</td>
                            <td>{{ $diploma->serial_number }}</td>
                            <td>{{ $diploma->graduated_at->format('Y/m/d') }}</td>

                            <td>
                                @if($diploma->is_revoked)
                                <span class="badge bg-label-danger me-1" style="font-size:10px;">{{ __('label.revoked') }}</span>
                                @else
                                <span class="badge bg-label-success me-1" style="font-size:10px;">{{ __('label.verified') }}</span>
                                @endif
                            </td>

                            @if(!auth()->user()->branch_id) 
                            <td>{{ $diploma->branch?->name }}</td>
                            @endif 

                            <td>

                            @php
                                $params = [
                                    'code' => $diploma->verification_code,
                                    'slug' => 'diploma_print'
                                ];
                            @endphp

                            <a class="btn btn-info btn-icon rounded-pill"
                                href="{{ route('diploma-print', [
                                'menu_id'   => $this->active_menu_id,
            
                                 ]) }}?{{ http_build_query($params) }}">
                                <i class="bx bx-file-blank text-white"></i> </a>
                                
                            </td>


                            <td>
                        
                                <div class="dropdown position-static">
                                    
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(edit(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit({{ $diploma->id }})"
                                            ><i class="bx bx-edit-alt me-1 text-success"></i>{{ __('label.edit') }}</a>
                                        @endif
                                   
                                        @if(delete(Auth::user()->role_ids,$active_menu_id))
                                            <a class="dropdown-item " href="javascript:void(0);"  onclick="confirmDelete({{ $diploma->id }},'{{$table_name}}')"
                                            ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $diplomas->links() }}
            </div>
        </div>
    </div>
    
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog modal-lg" branch="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif @if(App::getLocale() =='en') {{ $active_menu?->name_en }} @elseif(App::getLocale()=='fa') {{ $active_menu?->name }} @endif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                       
                        @if(!$editMode)
                            <div class="row">
                                <div class="col mb-3">
                                    <div  wire:ignore>
                                    <label for="nameBasic" class="form-label">{{ __('label.student') }} </label>
                                        <select id="student_id" class="form-select select2 @error('student_id') is-invalid @enderror" wire:model.lazy ="student_id">
                                            <option value="">{{ __('label.select') }}</option>
                                        </select>
                                    
                                    </div>
                                @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.graduation_date') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('graduated_at') is-invalid @enderror" wire:model.lazy="graduated_at">
                                @error('graduated_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.average') }} </label>
                                <input type="number" id="nameBasic" class="form-control @error('average') is-invalid @enderror" wire:model.lazy="average" min ="0" max="100" step ="any" >
                                @error('average') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
           

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.name_fa') }} </label>
                                <input type="text" id="nameBasic" class="form-control @error('name_fa') is-invalid @enderror" wire:model.lazy="name_fa" >
                                @error('name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.last_name_fa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('last_name_fa') is-invalid @enderror" wire:model.lazy="last_name_fa" >
                                @error('last_name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.name_pa') }} </label>
                                <input type="text" id="nameBasic" class="form-control @error('name_fa') is-invalid @enderror" wire:model.lazy="name_pa" >
                                @error('name_pa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.last_name_pa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('last_name_pa') is-invalid @enderror" wire:model.lazy="last_name_pa" >
                                @error('last_name_pa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.father_name_fa') }} </label>
                                <input type="text" id="nameBasic" class="form-control @error('father_name_fa') is-invalid @enderror" wire:model.lazy="father_name_fa" >
                                @error('father_name_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.father_name_pa') }}</label>
                                <input type="text" id="nameBasic" class="form-control @error('father_name_pa') is-invalid @enderror" wire:model.lazy="father_name_pa" >
                                @error('father_name_pa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.date_of_birth') }} <span style="color:red;">*</span></label>
                                <input type="date" id="nameBasic" class="form-control @error('date_of_birth') is-invalid @enderror" wire:model.lazy="date_of_birth">
                                @error('date_of_birth') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @if($editMode)
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label d-block">{{ __('label.status') }}</label>

                                <div class="form-check form-check-inline">
                                    <input type="radio"
                                        class="form-check-input"
                                        value="1"
                                        wire:model="is_revoked">
                                    <label class="form-check-label">
                                        {{ __('label.revoke') }}
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="radio"
                                        class="form-check-input"
                                        value="0"
                                        wire:model="is_revoked">
                                    <label class="form-check-label">
                                        {{ __('label.verify') }}
                                    </label>
                                </div>

                                @error('is_revoked')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >{{ __('label.close') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"  >
                            @if($editMode) {{ __('label.update') }}  @else {{ __('label.save') }} @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   

</div>

@script
<script>
document.addEventListener("livewire:initialized", function () {

    function initStudentSelect() {

        $('.select2').each(function () {
            const $select = $(this);
            const $modal  = $select.closest('.modal');

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                dropdownParent: $modal.length ? $modal : $(document.body)
            });
        });

        $('#promote_target_course_id').off('change').on('change', function () {
            $wire.set('target_course_id', $(this).val());
        });


        // -----------search student------------------
        let $student = $('#student_id');

        if (!$student.length) return;

        if ($student.hasClass('select2-hidden-accessible')) {
            $student.select2('destroy');
        }

        let modalId = @js($modalId);
        let menuId = @json($active_menu_id);
        $student.select2({
            dropdownParent: $('#' + modalId),
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: '/search-students',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $student.off('select2:select').on('select2:select', function (e) {
            let data = e.params.data;
            $wire.set('student_id', data.id);
        });

    }


    initStudentSelect();

    Livewire.hook('morphed', () => {
        initStudentSelect();
    });


    $(document).on('shown.bs.modal', function () {
        initStudentSelect();
    });

});

</script>
@endscript
