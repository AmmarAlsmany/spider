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
            <a href="{{ route('finance.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i></div>
                <div class="menu-title">{{ __('messages.dashboard') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.financial_management') }}</li>
        <li class="mm-active">
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-money'></i></div>
                <div class="menu-title">{{ __('messages.payments') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('finance.payments') }}">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('messages.all_payments') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('finance.payments.pending') }}">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('messages.pending_payments') }}
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('finance.reports.financial') }}">
                <div class="parent-icon"><i class='bx bx-chart'></i></div>
                <div class="menu-title">{{ __('messages.financial_reports') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.account_settings') }}</li>
        <li>
            <a href="{{ route('change.user.profile') }}">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">{{ __('messages.my_profile') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('change.user.password') }}">
                <div class="parent-icon"><i class='bx bx-lock'></i></div>
                <div class="menu-title">{{ __('messages.change_password') }}</div>
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

        <li>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="parent-icon"><i class='bx bx-log-out'></i></div>
                <div class="menu-title">{{ __('messages.logout') }}</div>
            </a>
        </li>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    <!--end navigation-->
</div>