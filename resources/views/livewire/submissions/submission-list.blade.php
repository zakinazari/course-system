<div>
    <!-- title -->
    @section('title',
    (
        ($active_menu?->parent?->name ?? '') 
        ? $active_menu?->parent?->name . '-' 
        : ''
    ) 
    . $active_menu?->name
    . ' | ' . __('label.app_name')
    )
    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name))
        <span class="text-muted fw-light"> {{ $active_menu?->grandParent?->name }} /</span>
    @endif
    @if(!empty($active_menu?->parent?->name))
        <span class="text-muted fw-light"> {{ $active_menu?->parent?->name }} /</span>
    @endif
    {{ $active_menu?->name }}  
    </h4>
    <!-- end header -->
@php 
 use Illuminate\Support\Str;
@endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">{{ $active_menu?->name }}</h5>

        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.title') }}" wire:model="search.identity">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('label.status') }}</label>
                        <select class="form-control" wire:model.live="search.status">
                            <option value="">{{ __('label.all') }}</option>
                                <option value="submitted">{{ __('label.submitted') }}</option>
                                <option value="screening">{{ __('label.screening') }}</option>
                                <option value="under_review">{{ __('label.under_review') }}</option>
                                <option value="revision_required">{{ __('label.revision_required') }}</option>
                                <option value="accepted">{{ __('label.accepted') }}</option>
                                <option value="rejected">{{ __('label.rejected') }}</option>
                                <option value="published">{{ __('label.published') }}</option>
                        </select>
                    </div>
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
                            <th>{{ __('label.title') }}</th>
                            <th>{{ __('label.round') }}</th>
                            <th>{{ __('label.status') }}</th>
                            <th>{{ __('label.date_published') }}</th>
                            @if(auth()->user()->isAdmin())
                            <th>{{ __('label.publish') }} / {{ __('label.unpublish') }}</th>
                            @endif
                            <th>{{ __('label.view') }}</th>
                            <th>{{ __('label.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($submissions as $i => $s)
                        <tr>
                            <td>{{ ($submissions->currentPage() - 1) * $submissions->perPage() + $i + 1 }}</td>
                            <td>
                                @if(App::getLocale() === 'en')
                                    {{ $s->title_en }}
                                    {{ Str::limit($s->title_en, 50, '...') }}
                                @else
                                    {{ Str::limit($s->title_fa, 50, '...') }}
                                @endif
                            </td>
                            <td>
                                {{ __('label.round') }} {{ $s->round }}
                            </td>
                            
                            <td>
                                 @switch($s->status)
                
                                    @case('submitted')
                                        <span class="badge bg-label-primary me-1" style="font-size:10px;">
                                            {{ __('label.submitted') }}
                                        </span>
                                        @break

                                    @case('screening')
                                        <span class="badge bg-label-secondary me-1" style="font-size:10px;">
                                            {{ __('label.screening') }}
                                        </span>
                                        @break

                                    @case('under_review')
                                        <span class="badge bg-label-info me-1" style="font-size:10px;">
                                            {{ __('label.under_review') }}
                                        </span>
                                        @break
                                    @case('revision_required')
                                        <span class="badge bg-label-warning me-1" style="font-size:10px;">
                                            {{ __('label.revision_required') }}
                                        </span>
                                        @break

                                        @case('accepted')
                                            <span class="badge bg-label-success me-1" style="font-size:10px;">
                                                {{ __('label.accepted') }}
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-label-danger me-1" style="font-size:10px;">
                                                {{ __('label.rejected') }}
                                            </span>
                                            @break

                                        @case('published')
                                            <span class="badge bg-label-success me-1" style="font-size:10px;">
                                                {{ __('label.published') }}
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-label-primary me-1" style="font-size:10px;">
                                                {{ $s->status }}
                                            </span>
                                        @endswitch
                            </td>
                            
                            <td>
                                {{ $s->published_at }}
                            </td>
                            @if(auth()->user()->isAdmin())
                            <td>
                                @if(edit(Auth::user()->role_ids,$active_menu_id))
                                    @if($s->status === 'accepted')
                                        <button wire:click="publish({{ $s->id }})" class="btn btn-success btn-sm">{{ __('label.publish') }}</button>
                                    @endif

                                    @if($s->status === 'published')
                                        <button wire:click="unpublish({{ $s->id }})" class="btn btn-warning btn-sm">{{ __('label.unpublish') }}</button>
                                    @endif
                                @endif
                            </td>
                            @endif
                            <td>
                                @if(edit(Auth::user()->role_ids,$active_menu_id))
                                    <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="viewSubmission({{ $s->id }})">
                                        <i class="bx bx-show"></i>
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if(delete(Auth::user()->role_ids,$active_menu_id) && $s->status != 'published' &&  auth()->user()->isAdmin())
                                <div class="dropdown position-static">
                                    <button type="button" class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                       
                                        <a class="dropdown-item " href="#"  onclick="confirmDelete({{ $s->id }},'{{$table_name}}')"
                                        ><i class="bx bx-trash me-1 text-danger"></i>{{ __('label.delete') }}</a>
                                       
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $submissions->links() }}
            </div>

        </div>
    </div>
</div>