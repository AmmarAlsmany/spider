@extends('shared.dashboard')
@section('content')
<div class="page-content">
    @if(session('error'))
    <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('clients.dashboard.total_contracts') }}</p>
                            <h4 class="my-1 text-info">{{ $totalContracts }}</h4>
                            <p class="mb-0 font-13">{{ $activeContracts }} {{ __('clients.dashboard.active_contracts') }}</p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-blues ms-auto">
                            <i class='bx bxs-folder'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('clients.dashboard.total_payments') }}</p>
                            <h4 class="my-1 text-danger">${{ number_format($totalRevenue, 2) }}</h4>
                            <p class="mb-0 font-13">
                                <span class="text-warning">{{ $pendingPayments }} {{ __('clients.dashboard.pending') }}</span> /
                                <span class="text-danger">{{ $overduePayments }} {{ __('clients.dashboard.overdue') }}</span>
                            </p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-burning ms-auto">
                            <i class='bx bxs-wallet'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('clients.dashboard.active_contracts') }}</p>
                            <h4 class="my-1 text-success">{{ $activeContracts }}</h4>
                            <p class="mb-0 font-13">{{ round(($activeContracts / ($totalContracts ?: 1)) * 100) }}% of Total</p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-ohhappiness ms-auto">
                            <i class='bx bxs-bar-chart-alt-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="border-0 border-4 card radius-10 border-start border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">{{ __('clients.dashboard.open_tickets') }}</p>
                            <h4 class="my-1 text-warning">{{ $openTickets }}</h4>
                            <p class="mb-0 font-13">{{ __('clients.dashboard.pending_support') }}</p>
                        </div>
                        <div class="text-white widgets-icons-2 rounded-circle bg-gradient-orange ms-auto">
                            <i class='bx bxs-group'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 contracts-info">
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">{{ __('clients.dashboard.your_contracts') }}</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3">
                    @foreach ($contracts as $contract)
                    <div class="col">
                        <div class="border-0 card border-info border-bottom border-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="text-white widget-icon-2 bg-gradient-info">
                                        <i class='bx bx-file'></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ __('clients.dashboard.contract_number') }}{{ $contract->contract_number }}</h6>
                                        <p class="mb-0 text-secondary">
                                            <span class="badge {{ 
                                                $contract->contract_status == 'approved' ? 'bg-success' : 
                                                ($contract->contract_status == 'pending' ? 'bg-warning' : 
                                                ($contract->contract_status == 'Not approved' ? 'bg-danger' : 'bg-secondary')) 
                                            }}">
                                                @php
                                                    $status = $contract->contract_status;
                                                    if (is_array($status)) {
                                                        $status = 'pending';
                                                    }
                                                    $status = strtolower($status);
                                                    if ($status === 'not approved') {
                                                        $status = 'not_approved';
                                                    }

                                                    $statusText = match($status) {
                                                        'pending' => 'Pending',
                                                        'approved' => 'Approved',
                                                        'not_approved' => 'Not Approved',
                                                        'active' => 'Active',
                                                        'completed' => 'Completed',
                                                        default => ucfirst($status)
                                                    };
                                                @endphp
                                                {{ $statusText }}
                                            </span>
                                        </p>
                                        <p class="mb-0 text-secondary">
                                            <strong>{{ __('clients.dashboard.property_type') }}:</strong> {{ $contract->Property_type }}
                                        </p>
                                        <p class="mb-0 text-secondary">
                                            <strong>{{ __('clients.dashboard.start_date') }}:</strong> {{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y') }}
                                        </p>
                                        <div class="mt-2">
                                            <a href="{{ route('client.contract.details', $contract->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-show"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">{{ __('clients.dashboard.recent_payments') }}</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($recentPayments->isEmpty())
            <div class="p-4 text-center">
                <p class="text-muted">{{ __('clients.dashboard.no_recent_payments') }}</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('clients.dashboard.amount') }}</th>
                            <th>{{ __('clients.dashboard.date') }}</th>
                            <th>{{ __('clients.dashboard.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td>${{ number_format($payment->payment_amount, 2) }}</td>
                            <td>{{ $payment->due_date }}</td>
                            <td>
                                <span class="badge {{ 
                                    $payment->payment_status == 'paid' ? 'bg-success' : 
                                    ($payment->payment_status == 'pending' ? 'bg-warning' : 'bg-danger') 
                                }}">
                                    @php
                                        $statusText = match($payment->payment_status) {
                                            'paid' => 'Paid',
                                            'pending' => 'Pending',
                                            'overdue' => 'Overdue',
                                            default => ucfirst($payment->payment_status)
                                        };
                                    @endphp
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0"><i class="bx bx-calendar-check me-2"></i>{{ __('clients.dashboard.service_visits') }}</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                $visitsByBranch = $scheduledVisits->groupBy(function($visit) {
                    return $visit->branch->branch_name ?? 'Main Location';
                });
                @endphp
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                    @forelse($visitsByBranch as $branchName => $visits)
                    <div class="col">
                        <div class="border-0 border-4 card border-start border-primary h-100">
                            <div class="bg-transparent card-header">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-primary">{{ $branchName }}</h6>
                                        <p class="mb-0 text-secondary">Scheduled Visits</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('clients.dashboard.date') }}</th>
                                                <th>{{ __('clients.dashboard.time') }}</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($visits as $visit)
                                            <tr>
                                                <td>{{ $visit->visit_date }}</td>
                                                <td>{{ $visit->visit_time }}</td>
                                                
                                                <td>
                                                    <span class="badge {{ 
                                                        $visit->status == 'scheduled' ? 'bg-info' : 
                                                        ($visit->status == 'completed' ? 'bg-success' : 
                                                        ($visit->status == 'cancelled' ? 'bg-danger' : 'bg-warning')) 
                                                    }}">
                                                        @php
                                                            $statusText = match($visit->status) {
                                                                'scheduled' => 'Scheduled',
                                                                'completed' => 'Completed',
                                                                'cancelled' => 'Cancelled',
                                                                'pending' => 'Pending',
                                                                default => ucfirst($visit->status)
                                                            };
                                                        @endphp
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewVisitDetails{{ $visit->id }}">
                                                        <i class="bx bx-show"></i> View Details
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Visit Details Modal -->
                                            <div class="modal fade" id="viewVisitDetails{{ $visit->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('clients.dashboard.visit_details') }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Visit Date:</strong> {{ $visit->visit_date }}</p>
                                                            <p><strong>Visit Time:</strong> {{ $visit->visit_time }}</p>
                                                            <p><strong>Status:</strong> 
                                                                <span class="badge {{ 
                                                                    $visit->status == 'scheduled' ? 'bg-info' : 
                                                                    ($visit->status == 'completed' ? 'bg-success' : 
                                                                    ($visit->status == 'cancelled' ? 'bg-danger' : 'bg-warning')) 
                                                                }}">
                                                                    {{ $statusText }}
                                                                </span>
                                                            </p>
                                                            <div class="mt-3">
                                                                <h6>Team Notes:</h6>
                                                                <p class="text-muted">{{ $visit->report?->recommendations ?? 'No notes available.' }}</p>
                                                            </div>
                                                            {{-- view the report if it exists --}}
                                                            @if($visit->status == 'completed')
                                                            <div class="mt-3">
                                                                <h6>Visit Report:</h6>
                                                                <p class="text-muted">
                                                                    @if($visit->report?->customer_notes)
                                                                    <strong>Customer Notes:</strong> {{ $visit->report->customer_notes }}<br>
                                                                    @endif
                                                                </p>
                                                                <a href="{{ route('client.visit.details', $visit->id) }}" class="btn btn-primary">View Report</a>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="p-4 text-center">
                            <i class="bx bx-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted">{{ __('clients.dashboard.no_scheduled_visits') }}</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Visit Modal -->
    <div id="editVisitModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Visit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editVisitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="visit_date" class="form-label">Visit Date</label>
                            <input type="date" class="form-control" id="visit_date" name="visit_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="visit_time" class="form-label">Visit Time</label>
                            <input type="time" class="form-control" id="visit_time" name="visit_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Visit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection