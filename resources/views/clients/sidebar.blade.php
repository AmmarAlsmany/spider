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
            <a href="{{ url('client/dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i></div>
                <div class="menu-title">{{ __('clients.sidebar.dashboard') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('clients.sidebar.contracts') }}</li>
        <li>
            <a href="{{ route('client.show') }}">
                <div class="parent-icon"><i class='bx bx-file'></i></div>
                <div class="menu-title">{{ __('clients.sidebar.all_contracts') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('client.contract.pending_annexes') }}">
                <div class="parent-icon"><i class='bx bx-folder-plus'></i>
                </div>
                <div class="menu-title">{{ __('clients.sidebar.pending_annexes') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('clients.sidebar.support') }}</li>
        <li>
            <a href="{{ route('client.tikets') }}">
                <div class="parent-icon"><i class='bx bx-message-square-detail'></i></div>
                <div class="menu-title">{{ __('clients.sidebar.my_tickets') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('clients.sidebar.account') }}</li>
        <li>
            <a href="{{ route('change.user.profile') }}">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">{{ __('clients.sidebar.my_profile') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="parent-icon"><i class='bx bx-log-out'></i></div>
                <div class="menu-title">{{ __('clients.sidebar.logout') }}</div>
            </a>
        </li>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>