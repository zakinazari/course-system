<div>
    <form  wire:submit.prevent="storeComment">
        <!-- comment -->
        <div class="row g-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label" for="">{{ __('label.comments_for_editor') }}</label>
                <textarea  id="comments_to_editor_fa" class="form-control @error('comments_to_editor_fa') is-invalid @enderror" placeholder="" wire:model.lazy="comments_to_editor_fa" rows="8"></textarea>
                    @error('comments_to_editor_fa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-sm-12 fv-plugins-icon-container">
                <label class="form-label" for="">{{ __('label.comments_for_editor') }} ({{ __('label.en',locale:'en') }})</label>
                <textarea dir="ltr" id="comments_to_editor_en" class="form-control @error('comments_to_editor_en') is-invalid @enderror" placeholder="" wire:model.lazy="comments_to_editor_en" rows="8"></textarea>
                    @error('comments_to_editor_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                
            </div>
        </div>
    
        <hr>
        <div class="col-12 d-flex justify-content-between">
            <button type="button" class="btn btn-primary btn-prev" wire:click="$dispatch('prevStep')">
            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
            <span class="d-sm-inline-block d-none">{{ __('label.prev') }}</span>
            </button>
            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-success" wire:click="validateStep(4)">{{ __('label.submit') }}</button>
                <!-- <button type="button" class="btn btn-primary btn-next">
                    <span class="d-sm-inline-block d-none me-sm-1">Next</span>
                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                </button> -->
            </div>
        </div>
    </form>
</div>

