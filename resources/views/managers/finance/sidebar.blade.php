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
            <a href="{{ route('finance.dashboard') }}" class="nav-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i></div>
                <div class="menu-title">{{ __('messages.dashboard') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.financial_management') }}</li>
        <li class="mm-active">
            <a href="{{ route('finance.invoices') }}" class="nav-link {{ request()->routeIs('finance.invoices*') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-file'></i></div>
                <div class="menu-title">{{ __('messages.invoices') }}</div>
            </a>
        </li>
        <li class="mm-active">
            <a href="{{ route('finance.payments') }}" class="nav-link {{ request()->routeIs('finance.payments') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-money'></i></div>
                <div class="menu-title">{{ __('messages.all_payments') }}</div>
            </a>
        </li>
        <li class="mm-active">
            <a href="{{ route('finance.payments.pending') }}" class="nav-link {{ request()->routeIs('finance.payments.pending') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-clock'></i></div>
                <div class="menu-title">{{ __('messages.pending_payments') }}</div>
            </a>
        </li>
        <li class="mm-active">
            <a href="{{ route('finance.reconciliation.index') }}" class="nav-link {{ request()->routeIs('finance.reconciliation*') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-balance-scale'></i></div>
                <div class="menu-title">{{ __('messages.payment_reconciliation') }}</div>
            </a>
        </li>
        <li class="mm-active has-treeview {{ request()->routeIs('finance.reports*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('finance.reports*') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-chart'></i></div>
                <div class="menu-title">
                    {{ __('messages.reports') }}
                    <i class="right bx bx-angle-left"></i>
                </div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('finance.reports.financial') }}" class="nav-link {{ request()->routeIs('finance.reports.financial') ? 'active' : '' }}">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('messages.financial_reports') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('finance.reports.analytics') }}" class="nav-link {{ request()->routeIs('finance.reports.analytics') ? 'active' : '' }}">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('messages.advanced_analytics') }}
                    </a>
                </li>
            </ul>
        </li>
        <li class="mm-active has-treeview {{ request()->routeIs('finance.exports*') || request()->routeIs('finance.notifications*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('finance.exports*') || request()->routeIs('finance.notifications*') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-tools'></i></div>
                <div class="menu-title">
                    {{ __('messages.tools') }}
                    <i class="right bx bx-angle-left"></i>
                </div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('finance.exports.payments') }}" class="nav-link {{ request()->routeIs('finance.exports.payments') ? 'active' : '' }}">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('messages.export_payments') }}
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('send-reminders-form').submit();">
                        <i class="bx bx-right-arrow-alt"></i>{{ __('messages.send_payment_reminders') }}
                    </a>
                    <form id="send-reminders-form" action="{{ route('finance.notifications.send-reminders') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
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