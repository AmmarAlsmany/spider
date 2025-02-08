<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('backend/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Spider Web</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('team-leader.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">{{ __('messages.dashboard') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.visits_management') }}</li>
        <li>
            <a href="{{ route('team-leader.visits') }}">
                <div class="parent-icon"><i class='bx bx-calendar'></i>
                </div>
                <div class="menu-title">{{ __('messages.all_visits') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('team-leader.visits') }}?status=pending">
                <div class="parent-icon"><i class='bx bx-time'></i>
                </div>
                <div class="menu-title">{{ __('messages.pending_visits') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('team-leader.visits') }}?status=completed">
                <div class="parent-icon"><i class='bx bx-check-circle'></i>
                </div>
                <div class="menu-title">{{ __('messages.completed_visits') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.reports') }}</li>
        <li>
            <a href="{{ route('team-leader.visits') }}?date={{ date('Y-m-d') }}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">{{ __('messages.todays_reports') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('team-leader.visits') }}?status=completed">
                <div class="parent-icon"><i class='bx bx-folder'></i>
                </div>
                <div class="menu-title">{{ __('messages.all_reports') }}</div>
            </a>
        </li>

        <li class="mm-active">
            <a href="{{ route('alerts.index') }}">
                <div class="parent-icon">
                    <i class='bx bx-bell'></i>
                    @php
                    $alertCount = app(App\Http\Controllers\AlertController::class)->getUnreadCount();
                    @endphp
                    @if($alertCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $alertCount }}</span>
                    @endif
                </div>
                <div class="menu-title">{{ __('messages.alerts') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.profile_section') }}</li>
        <li>
            <a href="{{ route('change.user.profile') }}">
                <div class="parent-icon"><i class='bx bx-user'></i>
                </div>
                <div class="menu-title">{{ __('messages.my_profile') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('change.user.password') }}">
                <div class="parent-icon"><i class='bx bx-lock'></i>
                </div>
                <div class="menu-title">{{ __('messages.change_password') }}</div>
            </a>
        </li>
        <li>
            <a href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="parent-icon"><i class='bx bx-log-out'></i>
                </div>
                <div class="menu-title">{{ __('messages.logout') }}</div>
            </a>
        </li>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>