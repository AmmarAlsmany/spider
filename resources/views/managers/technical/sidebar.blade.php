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
            <a href="{{ route('technical.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">{{ __('messages.dashboard') }}</div>
            </a>
        </li>
        <li class="menu-label">{{ __('messages.teams') }}</li>
        <li>
            <a href="{{ route('teams.index') }}">
                <div class="parent-icon"><i class='bx bx-group'></i>
                </div>
                <div class="menu-title">{{ __('messages.manage_teams') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('technical.team.schedules') }}">
                <div class="parent-icon"><i class='bx bx-calendar-check'></i>
                </div>
                <div class="menu-title">{{ __('messages.team_schedules') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('team-leaders.index') }}">
                <div class="parent-icon"><i class='bx bx-user-check'></i>
                </div>
                <div class="menu-title">{{ __('messages.team_leaders') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('workers.index') }}">
                <div class="parent-icon"><i class='bx bx-user-circle'></i>
                </div>
                <div class="menu-title">{{ __('messages.workers') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.inventory') }}</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-vial'></i>
                </div>
                <div class="menu-title">{{ __('messages.pesticide_management') }}</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('technical.pesticides.index') }}">
                        <i class="bx bx-list-ul"></i>{{ __('messages.pesticides') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('technical.pesticides.create') }}">
                        <i class="bx bx-plus"></i>{{ __('messages.add_new') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('technical.pesticides.analytics') }}">
                        <i class="bx bx-bar-chart-alt-2"></i>{{ __('messages.pesticide_consumption_analytics') }}
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-label">{{ __('messages.tickets') }}</li>
        <li>
            <a href="{{ route('technical.client_tickets') }}">
                <div class="parent-icon"><i class='bx bx-message-square-detail'></i>
                </div>
                <div class="menu-title">{{ __('messages.client_tickets') }}</div>
            </a>
        </li>

        <li class="menu-label">{{ __('messages.appointments') }}</li>
        <li>
            <a href="{{ route('technical.scheduled-appointments') }}">
                <div class="parent-icon"><i class='bx bx-calendar-check'></i>
                </div>
                <div class="menu-title">{{ __('messages.scheduled_visits') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('technical.completed-visits') }}">
                <div class="parent-icon"><i class='bx bx-check-circle'></i>
                </div>
                <div class="menu-title">{{ __('messages.completed_visits') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('technical.cancelled-visits') }}">
                <div class="parent-icon"><i class='bx bx-x-circle'></i>
                </div>
                <div class="menu-title">{{ __('messages.cancelled_visits') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ route('technical.visit.requests') }}">
                <div class="parent-icon"><i class='bx bx-calendar-edit'></i>
                </div>
                <div class="menu-title">{{ __('messages.visit_change_requests') }}</div>
                @php
                    $pendingCount = \App\Models\VisitSchedule::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $pendingCount }}</span>
                @endif
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