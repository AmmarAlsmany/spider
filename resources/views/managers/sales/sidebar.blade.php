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
        <li class="mm-active">
            <a href={{ route('sales.dashboard') }}>
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">{{ __('messages.dashboard') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.contracts_elements') }}</li>
        <li class="mm-active">
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bxs-report'></i>
                </div>
                <div class="menu-title">{{ __('messages.contracts') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('sales.contract.type.cards') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.create_new_contract') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('contract.show') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.current_contracts') }}
                    </a>
                </li>
            </ul>
        </li>

        <li class="mm-active">
            <a class="has-arrow" href="javascript:;">
                <div class="parent-icon"><i class='bx bx-archive-in'></i>
                </div>
                <div class="menu-title">{{ __('messages.archives') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('completed.show.all') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.completed_contracts') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('stopped.show.all') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.stopped_contracts') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('canceled.show.all') }}">
                        <i class='bx bx-radio-circle'></i>{{ __('messages.canceled_contracts') }}
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-label">{{ __('messages.customers_support') }}</li>
        <li class="mm-active">
            <a href="{{ route('sales.todo') }}">
                <div class="parent-icon"><i class="bx bx-task"></i>
                </div>
                <div class="menu-title">{{ __('messages.to_do_list') }}</div>
            </a>
        </li>
        <li class="mm-active">
            <a href="{{ route('view.my.clients') }}">
                <div class="parent-icon"><i class="bx bx-user"></i>
                </div>
                <div class="menu-title">{{ __('messages.my_clients') }}</div>
            </a>
        </li>
        <li class="mm-active">
            <a href="{{ route('sales.tikets') }}">
                <div class="parent-icon"><i class="bx bx-support"></i>
                </div>
                <div class="menu-title">{{ __('messages.customer_suggestions') }}</div>
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
    </ul>
    <!--end navigation-->
</div>
