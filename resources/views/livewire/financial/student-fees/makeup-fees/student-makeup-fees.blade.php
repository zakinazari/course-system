
<div>
    @php
    $printId = 'printArea' . $fee_type_id;
    @endphp
<style>
    @media print {
        body, html {
            background: #fff !important;
            -webkit-print-color-adjust: exact;
        }

        body * {
            visibility: hidden;
        }

        [id^="printArea"], [id^="printArea"] * {
            visibility: visible;
        }

        [id^="printArea"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: block !important;
            direction: ltr !important;
        }
    }
</style>
    <!-- title -->
      @section('title',(($active_menu?->parent?->name_en ?? '') ? $active_menu?->parent?->name_en . '-' : ''
        ) . $active_menu?->name_en . ' | '.__('label.app_name'))
    <!-- end title -->
        <div class="card-header">
      
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-2 mb-md-0">
                    {{ $fee_type_name }}
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
                            <th>{{ __('label.course') }}</th>
                            <th>{{ __('label.amount') }}</th>
                            <th>{{ __('label.payment_date') }}</th>
                            <th>{{ __('label.note') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($makeup_fees as $i => $fee)
                        <tr>
                            <td>{{ ($makeup_fees->currentPage() - 1) * $makeup_fees->perPage() + $i + 1 }}</td>
                            <td>{{ $fee->course?->name }}</td>
                            <td>{{ $fee->amount }}</td>
                           
                            
                            <td>{{ $fee->payment_date?->format('Y/m/d') }}</td>
    
                            <td>{{ $fee->note }}</td>
                            <td>
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                              
                                    @if(delete(Auth::user()->role_ids,$active_menu_id))

                                        @if(Auth::user()->isAdmin() || Auth::user()->isDeveloper())

                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                onclick="confirmDelete({{ $fee->id }},'{{$table_name}}')">

                                                <i class="bx bx-trash me-1 text-danger"></i>
                                                {{ __('label.delete') }}

                                            </a>

                                        @endif

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
                {{ $makeup_fees->links() }}
            </div>
        </div>
    <!--/ Bootstrap Table with Header Dark -->
    <div class="modal fade" id="{{$modalId}}" tabindex="-1" aria-hidden="true" wire:ignore.self> 
        <div class="modal-dialog" fee="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if($editMode) {{ __('label.editing') }}  @else {{ __('label.adding') }} @endif {{ $fee_type_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <form @if($editMode) wire:submit.prevent="update" @else wire:submit.prevent="store" @endif>
                    <div class="modal-body">
                     
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label">{{ __('label.makeup_student_course') }}</label>
                                <select  class="form-select @error('course_id') is-invalid @enderror" wire:model.lazy ="course_id">
                                    <option value="">{{ __('label.select') }}</option>
                                    @foreach($student_courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <div wire.ignore>
                                    <label for="nameBasic" class="form-label">{{ __('label.amount') }} <span style="color:red;">*</span></label>
                                    <div wire:ignore>
                                    <select  class="form-select @error('makeup_setting_id') is-invalid @enderror" wire:model.lazy ="makeup_setting_id" >
                                        <option value="">{{ __('label.select') }}</option>
                                        @foreach($makeup_settings as $makeup_setting)
                                        <option value="{{ $makeup_setting->id }}">{{ $makeup_setting->name }} ({{ __('label.fee_amount') }}: {{ $makeup_setting->fee_amount }})</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                @error('makeup_setting_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBasic" class="form-label" >{{ __('label.note') }}</label>
                                <textarea type="text" id="nameBasic" class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note"></textarea>
                                @error('note') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
   
   
</div>

@script

<script>
document.addEventListener("livewire:initialized", function () {
    function initSelect2() {
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
    }

    
    initSelect2();

    
    Livewire.hook('morphed', () => {
        initSelect2();
    });

   
    Livewire.hook('message.processed', function (message, component) {
        const $modal = $('#{{$modalId}}');
        if ($modal.is(':visible')) {
            initSelect2();
        }
    });


    $(document).on('shown.bs.modal', function () {
        initSelect2();
    });
});
</script>
  
@endscript


