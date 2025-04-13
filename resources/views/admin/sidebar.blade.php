<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('backend/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">{{ __('admin.sidebar.logo_text') }}</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.dashboard') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('admin.sidebar.staff_management') }}</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.managers') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.managers.index') }}"><i class="bx bx-right-arrow-alt"></i>{{ __('admin.sidebar.all_managers') }}</a>
                </li>
                <li>
                    <a href="{{ route('admin.managers.create') }}"><i class="bx bx-right-arrow-alt"></i>{{ __('admin.sidebar.add_manager') }}</a>
                </li>
            </ul>
        </li>

        <li class="menu-label">{{ __('admin.sidebar.contract_management') }}</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-file'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.contracts') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.contracts.index') }}">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('admin.sidebar.all_contracts') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contracts.reports') }}">
                        <i class="bx bx-chart"></i>{{ __('admin.sidebar.contract_reports') }}
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-label">{{ __('admin.sidebar.support_management') }}</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-support'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.tickets') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.tickets.index') }}"><i class="bx bx-right-arrow-alt"></i>{{ __('admin.sidebar.all_tickets') }}</a>
                </li>
                <li>
                    <a href="{{ route('admin.tickets.reports') }}"><i class="bx bx-chart"></i>{{ __('admin.sidebar.ticket_reports') }}</a>
                </li>
            </ul>
        </li>

        <li class="menu-label">{{ __('admin.sidebar.financial_management') }}</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-money'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.payments') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin.payments.index') }}"><i class="bx bx-right-arrow-alt"></i>{{ __('admin.sidebar.all_payments') }}</a>
                </li>
                <li>
                    <a href="{{ route('admin.payments.reports') }}"><i class="bx bx-right-arrow-alt"></i>{{ __('admin.sidebar.payment_reports') }}</a>
                </li>
            </ul>
        </li>

        <li class="menu-label">{{ __('admin.sidebar.my_account') }}</li>
        <li>
            <a href="{{ route('change.user.profile') }}">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.my_profile') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('change.user.password') }}">
                <div class="parent-icon"><i class='bx bx-lock'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.change_password') }}</div>
            </a>
        </li>

        <li>
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
                <div class="menu-title">{{ __('admin.sidebar.alerts') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="parent-icon"><i class='bx bx-log-out'></i></div>
                <div class="menu-title">{{ __('admin.sidebar.logout') }}</div>
            </a>
        </li>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    <!--end navigation-->
</div>