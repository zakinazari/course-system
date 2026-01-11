<div>
    <h5 class="mb-4">{{ __('label.reviewer_declined_reasons') }}</h5>
    <div>
    @if(!empty($reviews))
        @foreach($reviews as $review)
            <label class="form-check-label pt-1" for="companyName" style="width:100%;">    
                <span class="mb-1 h6">
                <div class="form-check mb-3">

                    <i class="fa fa-user mb-2"></i>
                    
                    {{ $review?->reviewer?->name }} &nbsp;
        
                    <span class="badge rounded-2 badge-warning bg-label-danger fs-tiny py-1">{{ $review->status }}</span>
                    </span><br>
                    <small class="text-muted">
                        @if(App::getLocale()=='en')
                            {{ $review->decline_reason_en }}
                        @else
                            {{ $review->decline_reason_fa }}
                        @endif
                    </small>
                    </label>
                </div>
            @endforeach
        @endif
    </div>
</div>

