<div>
    <!-- title -->
    @section('title',
    (
        ($active_menu?->parent?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} ?? '') 
        ? $active_menu?->parent?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} . '-' 
        : ''
    ) 
    . $active_menu?->{app()->getLocale() === 'fa' ? 'name' : 'name_en'} 
    . ' | ' . __('label.app_name')
    )
    <!-- end title -->
    <!-- start header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
    @if(!empty($active_menu?->grandParent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='fa') {{ $active_menu?->grandParent?->name }} @else {{ $active_menu?->grandParent?->name_en }}  @endif  /</span>
    @endif
    @if(!empty($active_menu?->parent?->name_en))
    <span class="text-muted fw-light"> @if(App::getLocale()=='fa') {{ $active_menu?->parent?->name }} @else {{ $active_menu?->parent?->name_en }}  @endif /</span>
    @endif
    @if(App::getLocale()=='fa') {{ $active_menu?->name }} @else {{ $active_menu?->name_en }}  @endif
    </h4>
    <!-- end header -->
@php 
 use Illuminate\Support\Str;
@endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="card-title mb-0">@if(App::getLocale()=='fa') {{ $active_menu?->name }} @else {{ $active_menu?->name_en }}  @endif</h5>
        </div>
        <hr>
        <div class="table-responsive text-nowrap">
 
            <div class="mb-3 px-3">
                <form wire:submit.prevent="applySearch" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="{{ __('label.title') }}" wire:model="search.identity">
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
                            <th>{{ __('label.view') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($reviews as $i => $review)
                        <tr>
                            <td>{{ ($reviews->currentPage() - 1) * $reviews->perPage() + $i + 1 }}</td>
                            <td>
                                @if(App::getLocale() === 'en')
                                    {{ Str::limit($review->submission?->title_en, 50, '...') }}
                               @else
                                    {{ Str::limit($review->submission?->title_fa, 50, '...') }}
                               @endif
                            </td>
                            <td>
                                {{__('label.round')}} {{ $review->round }}
                            </td>
                            <td>
                                @if($review->status ==='pending')
                                <span class="badge bg-label-warning me-1">{{ __('label.pending') }}</span>
                                @elseif($review->status ==='accepted')
                                    <span class="badge bg-label-success me-1">{{ __('label.accepted') }}</span>
                                @elseif($review->status ==='completed')
                                    <span class="badge bg-label-info me-1">{{ __('label.completed') }}</span>
                                @elseif($review->status ==='declined')
                                    <span class="badge bg-label-danger me-1">{{ __('label.declined') }}</span>
                                @endif
                            </td>
                            <td>
                                @if(edit(Auth::user()->role_ids,$active_menu_id))
                                    <button type="button" class="btn btn-success btn-icon rounded-pill" wire:click="viewAssignment({{ $active_menu_id }},{{ $review->id }})">
                                        <i class="bx bx-show"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 justify-content-end px-3">
                {{ $reviews->links() }}
            </div>

        </div>
    </div>
</div>
