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
            <a href="{{ route('sales_manager.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">{{ __('messages.dashboard') }}</div>
            </a>
        </li>
        <li class="menu-label">{{ __('messages.team_management') }}</li>
        <li>
            <a href="{{ route('sales_manager.manage_agents') }}">
                <div class="parent-icon"><i class='bx bx-user-plus'></i>
                </div>
                <div class="menu-title">{{ __('messages.manage_sales_agents') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('sales_manager.manage_contracts') }}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">{{ __('messages.manage_contracts') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('sales_manager.pending_annexes') }}">
                <div class="parent-icon"><i class='bx bx-folder-plus'></i>
                </div>
                <div class="menu-title">{{ __('messages.pending_annexes') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('sales_manager.manage_clients') }}">
                <div class="parent-icon"><i class='bx bx-group'></i>
                </div>
                <div class="menu-title">{{ __('messages.manage_clients') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('sales_manager.postponement_requests') }}">
                <div class="parent-icon"><i class='bx bx-time'></i>
                </div>
                <div class="menu-title">{{ __('messages.postponement_requests') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('sales_manager.performance') }}">
                <div class="parent-icon"><i class='bx bx-line-chart'></i>
                </div>
                <div class="menu-title">{{ __('messages.agents_performance') }}</div>
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
        <li class="menu-label">{{ __('messages.reports') }}</li>
        <li class="mm-active">
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-line-chart'></i>
                </div>
                <div class="menu-title">{{ __('messages.sales_reports') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('sales_manager.reports.contracts') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.new_contracts') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales_manager.reports.contacts') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.customer_contacts') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales_manager.reports.collections') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.collections') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales_manager.reports.payments') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.remaining_payments') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales_manager.reports.invoices') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.invoices') }}
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <!--end navigation-->
</div>