<div>
    <div class="row mt-3">
        <div class="col-sm-12 fv-plugins-icon-container">
            <form wire:submit.prevent="uploadFile">
                <label for="formFileMultiple" class="form-label">{{ __('label.file_upload') }}</label>
                <div class="input-group mb-2">
                    <input type="file"  
                     id="fileInput"
                     
                    class="form-control @error('file.*') is-invalid @enderror"  multiple wire:model.lazy="file" multiple>
                    <button type="submit" class="btn btn-success">{{ __('label.upload') }}</button>
                </div>
                    @error('file.*')
                        <span class="text-danger" id ="fileErrors">{{ $message }}</span>
                    @enderror
                    @error('files')<span class="text-danger">{{ $message }}</span>@enderror
                <div id="fileError" class="invalid-feedback d-block" style="display:none;"></div>
               
            </form>

        @if($uploaded_files->count())
            <div class="mt-3">
                <span>{{ __('label.uploaded_files') }}</span>
                <ul class="list-group">
                    @foreach($uploaded_files as $f)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @php
                                $ext = strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION));
                                switch ($ext) {
                                    case 'pdf':
                                        $icon = '📄'; 
                                        break;
                                    case 'doc':
                                    case 'docx':
                                        $icon = '📝'; 
                                        break;
                                    default:
                                        $icon = '📁'; 
                                        break;
                                }
                            @endphp
                            <span>
                                <span class="me-2">{{ $icon }}</span>
                                <span>{{ $f->original_name }}</span>&nbsp;&nbsp;&nbsp;<small>({{ __('label.round') }} {{ $f->round }})</small>
                            </span>
                            
                            <div class="d-flex gap-2">
                                <a wire:click.prevent="downloadFile({{ $f->id }})" class="btn btn-xs btn-secondary text-white">
                                      <i class="bx bx-download me-1"></i> {{ __('label.download') }}
                                </a>
                                <button wire:click="deleteFile({{ $f->id }})" type="button" class="btn btn-icon btn-label-danger">
                                <i class="bx bx-trash-alt"></i>
                                </button>
                                <!-- <button wire:click="deleteFile({{ $f->id }})" class="btn btn-sm btn-danger">
                                    {{ __('label.delete') }}
                                </button> -->
                            </div>

                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <hr>
        <div class="col-12 d-flex justify-content-between">
            <button  wire:click="$dispatch('prevStep')"  class="btn btn-primary btn-prev">
            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
            <span class="d-sm-inline-block d-none">{{ __('label.prev') }}</span>
            </button>
            <button class="btn btn-primary btn-next" wire:click="validateStep(2)">
            <span class="d-sm-inline-block d-none me-sm-1">{{ __('label.next') }}</span>
            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
            </button>
        </div>
        </div>
    </div>
</div>


<script>
 const input = document.getElementById('fileInput');
    const errorDiv = document.getElementById('fileError');
    const maxLengthEnglish = 120;
    const maxLengthPersian = 60;

    input.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            const isPersian = /[\u0600-\u06FF]/.test(file.name); 
            const maxLength = isPersian ? maxLengthPersian : maxLengthEnglish;

            if(file.name.length > maxLength){
                errorDiv.textContent = "{{ __('label.file_name_too_long') }}".replace(':max', maxLength);
                errorDiv.style.display = 'block';
                this.value = ''; 
            } else {
                errorDiv.style.display = 'none';
            }
        }
    });

</script>
