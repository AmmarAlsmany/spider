@extends('shared.dashboard')
@section('title', __('alerts.title'))
@section('content')
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-header">
                <h5 class="mb-0">{{ __('alerts.title') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <div>
                        <a href="{{ route('alerts.mark-all-as-read') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-check-double me-1"></i> {{ __('alerts.mark_all_as_read') }}
                        </a>
                    </div>
                </div>

                <ul class="mb-3 nav nav-tabs nav-primary" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-list-ul font-18 me-1'></i></div>
                                <div class="tab-title">{{ __('alerts.all') }}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#alerts" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-bell-plus font-18 me-1'></i></div>
                                <div class="tab-title">{{ __('alerts.system_alerts') }}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#notifications" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-bell font-18 me-1'></i></div>
                                <div class="tab-title">{{ __('alerts.notifications') }}</div>
                            </div>
                        </a>
                    </li>
                </ul>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="py-3 tab-content">
                    <!-- All Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle" id="all-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">{{ __('alerts.table.type') }}</th>
                                        <th width="20%">{{ __('alerts.table.title_source') }}</th>
                                        <th width="40%">{{ __('alerts.table.message') }}</th>
                                        <th width="10%">{{ __('alerts.table.status') }}</th>
                                        <th width="15%">{{ __('alerts.table.date') }}</th>
                                        <th width="10%">{{ __('alerts.table.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alerts as $alert)
                                        <tr>
                                            <td>
                                                @switch($alert->type)
                                                    @case('expired')
                                                        <span class="badge bg-danger">{{ __('alerts.types.expired') }}</span>
                                                    @break

                                                    @case('payment_due')
                                                        <span class="badge bg-warning">{{ __('alerts.types.payment_due') }}</span>
                                                    @break

                                                    @case('renewal_needed')
                                                        <span class="badge bg-info">{{ __('alerts.types.renewal_needed') }}</span>
                                                    @break

                                                    @case('monthly_report')
                                                        <span
                                                            class="badge bg-primary">{{ __('alerts.types.monthly_report') }}</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($alert->type) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <strong>{{ __('alerts.labels.system_alert') }}</strong>
                                                @if ($alert->contract)
                                                    <div class="small text-muted">
                                                        {{ __('alerts.labels.contract') }} #{{ $alert->contract->id }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $alert->message }}</td>
                                            <td>
                                                @if (!$alert->is_read)
                                                    <span class="badge bg-danger">{{ __('alerts.unread') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('alerts.read') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="gap-2 d-flex">
                                                    @if (!$alert->is_read)
                                                        <a href="{{ route('alerts.mark-as-read', $alert->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bx bx-check"></i>
                                                        </a>
                                                    @endif
                                                    @if ($alert->contract)
                                                        <a href="{{ route('contract.show', $alert->contract->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td>
                                                @switch($notification['type'])
                                                    @case('success')
                                                        <span class="badge bg-success">{{ __('alerts.types.success') }}</span>
                                                    @break

                                                    @case('warning')
                                                        <span class="badge bg-warning">{{ __('alerts.types.warning') }}</span>
                                                    @break

                                                    @case('error')
                                                        <span class="badge bg-danger">{{ __('alerts.types.error') }}</span>
                                                    @break

                                                    @case('info')
                                                        <span class="badge bg-info">{{ __('alerts.types.info') }}</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($notification['type']) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <strong>{{ $notification['title'] }}</strong>
                                                <div class="small text-muted">
                                                    {{ __('alerts.labels.user_notification') }}
                                                </div>
                                            </td>
                                            <td>{{ $notification['message'] }}</td>
                                            <td>
                                                @if (!$notification['is_read'])
                                                    <span class="badge bg-danger">{{ __('alerts.unread') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('alerts.read') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $notification['created_at']->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="gap-2 d-flex">
                                                    @if (!$notification['is_read'])
                                                        <a href="{{ route('alerts.mark-as-read', $notification['id']) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bx bx-check"></i>
                                                        </a>
                                                    @endif
                                                    @if ($notification['url'] && $notification['url'] !== '#')
                                                        <a href="{{ $notification['url'] }}" class="btn btn-sm btn-info">
                                                            <i class="bx bx-link-external"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Alerts Tab -->
                    <div class="tab-pane fade" id="alerts" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle" id="alerts-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">{{ __('alerts.table.type') }}</th>
                                        <th width="45%">{{ __('alerts.table.message') }}</th>
                                        <th width="15%">{{ __('alerts.table.status') }}</th>
                                        <th width="15%">{{ __('alerts.table.date') }}</th>
                                        <th width="15%">{{ __('alerts.table.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alerts as $alert)
                                        <tr>
                                            <td>
                                                @switch($alert->type)
                                                    @case('expired')
                                                        <span class="badge bg-danger">{{ __('alerts.types.expired') }}</span>
                                                    @break

                                                    @case('payment_due')
                                                        <span class="badge bg-warning">{{ __('alerts.types.payment_due') }}</span>
                                                    @break

                                                    @case('renewal_needed')
                                                        <span class="badge bg-info">{{ __('alerts.types.renewal_needed') }}</span>
                                                    @break

                                                    @case('monthly_report')
                                                        <span
                                                            class="badge bg-primary">{{ __('alerts.types.monthly_report') }}</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($alert->type) }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $alert->message }}</td>
                                            <td>
                                                @if (!$alert->is_read)
                                                    <span class="badge bg-danger">{{ __('alerts.unread') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('alerts.read') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="gap-2 d-flex">
                                                    @if (!$alert->is_read)
                                                        <a href="{{ route('alerts.mark-as-read', $alert->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bx bx-check"></i>
                                                            {{ __('alerts.buttons.mark_as_read') }}
                                                        </a>
                                                    @endif
                                                    @if ($alert->contract)
                                                        <a href="{{ route('contract.show', $alert->contract->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="bx bx-link-external"></i>
                                                            {{ __('alerts.buttons.view') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $alerts->links('vendor.pagination.custom') }}
                        </div>
                    </div>

                    <!-- Notifications Tab -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle" id="notifications-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">{{ __('alerts.table.type') }}</th>
                                        <th width="20%">{{ __('alerts.table.title') }}</th>
                                        <th width="35%">{{ __('alerts.table.message') }}</th>
                                        <th width="10%">{{ __('alerts.table.priority') }}</th>
                                        <th width="10%">{{ __('alerts.table.status') }}</th>
                                        <th width="15%">{{ __('alerts.table.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td>
                                                @switch($notification['type'])
                                                    @case('success')
                                                        <span class="badge bg-success">{{ __('alerts.types.success') }}</span>
                                                    @break

                                                    @case('warning')
                                                        <span class="badge bg-warning">{{ __('alerts.types.warning') }}</span>
                                                    @break

                                                    @case('error')
                                                        <span class="badge bg-danger">{{ __('alerts.types.error') }}</span>
                                                    @break

                                                    @case('info')
                                                        <span class="badge bg-info">{{ __('alerts.types.info') }}</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-secondary">{{ ucfirst($notification['type']) }}</span>
                                                @endswitch
                                            </td>
                                            <td><strong>{{ $notification['title'] }}</strong></td>
                                            <td>{{ $notification['message'] }}</td>
                                            <td>
                                                @switch($notification['priority'])
                                                    @case('high')
                                                        <span class="badge bg-danger">{{ __('alerts.priority.high') }}</span>
                                                    @break

                                                    @case('medium')
                                                        <span class="badge bg-warning">{{ __('alerts.priority.medium') }}</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-info">{{ __('alerts.priority.normal') }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if (!$notification['is_read'])
                                                    <span class="badge bg-danger">{{ __('alerts.unread') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('alerts.read') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="gap-2 d-flex">
                                                    @if (!$notification['is_read'])
                                                        <a href="{{ route('alerts.mark-as-read', $notification['id']) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bx bx-check"></i>
                                                            {{ __('alerts.buttons.mark_as_read') }}
                                                        </a>
                                                    @endif
                                                    @if ($notification['url'] && $notification['url'] !== '#')
                                                        <a href="{{ $notification['url'] }}" class="btn btn-sm btn-info">
                                                            <i class="bx bx-link-external"></i>
                                                            {{ __('alerts.buttons.view') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#all-table').DataTable({
                order: [
                    [4, 'desc']
                ], // Sort by date column (index 4) in descending order
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#alerts-table').DataTable({
                order: [
                    [3, 'desc']
                ], // Sort by date column (index 3) in descending order
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50]
            });

            $('#notifications-table').DataTable({
                order: [
                    [4, 'desc']
                ], // Sort by status column (index 4) in descending order
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50]
            });
        });
    </script>
@endpush
