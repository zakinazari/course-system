<div>
<li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
    
    <a class="nav-link dropdown-toggle hide-arrow" 
       href="javascript:void(0);" 
       data-bs-toggle="dropdown" 
       data-bs-auto-close="outside">

        <i class="bx bx-bell bx-sm"></i>

        @if($notifications->count())
            <span class="badge bg-danger rounded-pill badge-notifications">
                {{ $notifications->count() }}
            </span>
        @endif
    </a>

    <ul class="dropdown-menu dropdown-menu-end py-0">
        
        <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Notifications</h5>

                <a href="#" 
                   wire:click.prevent="markAllAsRead"
                   class="dropdown-notifications-all text-body">
                    <i class="bx fs-4 bx-envelope-open"></i>
                </a>
            </div>
        </li>

        <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">

                @forelse($notifications as $notification)
                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">

                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="flex-grow-1"
                                 wire:click="readNotification('{{ $notification->id }}')"
                                 style="cursor:pointer">

                                <h6 class="mb-1">
                                    {{ $notification->data['title'] }}
                                </h6>

                                <p class="mb-0">
                                    {{ $notification->data['message'] }}
                                </p>

                                <small class="text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>

                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted p-3">
                        No new notifications
                    </li>
                @endforelse

            </ul>
        </li>

        <li class="dropdown-menu-footer border-top">
            <a href="#" 
               class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40">
                View all notifications
            </a>
        </li>

    </ul>
</li>
</div>
