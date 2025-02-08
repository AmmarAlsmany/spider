<header>
    <div class="topbar d-flex align-items-center">
        <nav class="gap-3 navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>

            <div class="top-menu ms-auto">
                <ul class="gap-1 navbar-nav align-items-center">
                    <li class="nav-item dropdown dropdown-laungauge d-none d-sm-flex">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            data-bs-toggle="dropdown" title="{{ __('messages.language') }}">
                            <img src="{{ asset('backend/assets/images/county/' . (app()->getLocale() == 'ar' ? '02.png' : '01.png')) }}"
                                width="22" alt="{{ __('messages.language') }}">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="py-2 dropdown-item d-flex align-items-center" href="{{ route('switch.language', 'en') }}">
                                    <img src="{{ asset('backend/assets/images/county/01.png') }}" width="20" alt="English">
                                    <span class="ms-2">{{ __('messages.english') }}</span>
                                </a>
                            </li>
                            <li>
                                <a class="py-2 dropdown-item d-flex align-items-center" href="{{ route('switch.language', 'ar') }}">
                                    <img src="{{ asset('backend/assets/images/county/02.png') }}" width="20" alt="Arabic">
                                    <span class="ms-2">{{ __('messages.arabic') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;" id="darkModeToggle"><i class='bx bx-moon'></i>
                        </a>
                    </li>

                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="alert-count" id="notification-count">0</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>
                                    <p class="msg-header-badge" id="unread-count">0 New</p>
                                </div>
                            </a>
                            <div class="header-notifications-list" id="notifications-container">
                                <div class="text-center p-3">
                                    <p class="text-muted">Loading notifications...</p>
                                </div>
                            </div>
                            <a href="javascript:;" onclick="markAllAsRead()" id="mark-all-read" style="display: none;">
                                <div class="text-center msg-footer">Mark All As Read</div>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            @php
            $profile_data = null;
            if (Auth::check()) {
            $id = Auth::id();
            $profile_data = App\Models\User::find($id);
            } elseif (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            if ($client) {
            $profile_data = App\Models\client::find($client->id);
            }
            }
            @endphp
            <div class="px-3 user-box dropdown">
                <a class="gap-3 d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ !empty($profile_data?->avatar) ? url('upload/profile_images/' . $profile_data->avatar) : url('upload/no_image.jpg') }}"
                        class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="mb-0 user-name">{{ $profile_data?->name ?? 'Guest' }}</p>
                        <p class="mb-0 designattion">{{ $profile_data?->email ?? '' }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('change.user.profile') }}"><i
                                class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('change.user.password') }}"><i
                                class="bx bx-cog fs-5"></i><span>Change
                                Password</span></a>
                    </li>
                    <li>
                        @if (Auth::check())
                        @if (Auth::user()->role === 'admin')
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('admin.dashboard') }}">
                            <i class="bx bx-home-circle fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                        @elseif(Auth::user()->role === 'sales')
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('sales.dashboard') }}">
                            <i class="bx bx-home-circle fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                        @elseif(Auth::user()->role === 'technical')
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('technical.dashboard') }}">
                            <i class="bx bx-home-circle fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                        @elseif(Auth::user()->role === 'sales_manager')
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('sales_manager.dashboard') }}">
                            <i class="bx bx-home-circle fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                        @endif
                        @else
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('client.dashboard') }}">
                            <i class="bx bx-home-circle fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                        @endif
                    </li>
                    <li>
                        <div class="mb-0 dropdown-divider"></div>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button class="dropdown-item d-flex align-items-center"><i
                                    class="bx bx-log-out-circle"></i><span>Logout</span></button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/notifications.js') }}"></script>
</header>

@push('scripts')
<script>
    // Dark mode functionality
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        // Check if dark mode was previously enabled
        if (localStorage.getItem('darkMode') === 'enabled') {
            html.classList.add('dark-theme');
            darkModeToggle.querySelector('i').classList.remove('bx-moon');
            darkModeToggle.querySelector('i').classList.add('bx-sun');
        }

        // Toggle dark mode
        darkModeToggle.addEventListener('click', () => {
            if (html.classList.contains('dark-theme')) {
                html.classList.remove('dark-theme');
                localStorage.setItem('darkMode', 'disabled');
                darkModeToggle.querySelector('i').classList.remove('bx-sun');
                darkModeToggle.querySelector('i').classList.add('bx-moon');
            } else {
                html.classList.add('dark-theme');
                localStorage.setItem('darkMode', 'enabled');
                darkModeToggle.querySelector('i').classList.remove('bx-moon');
                darkModeToggle.querySelector('i').classList.add('bx-sun');
            }
        });
    });

    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }

    function markAllAsRead() {
        fetch('/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
</script>
@endpush