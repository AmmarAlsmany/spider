@extends('shared.dashboard')

@section('content')
<div class="page-content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0">Scheduled Appointments</h6>
                            </div>
                            <div class="gap-2 d-flex align-items-center">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#createVisitModal">
                                    <i class="bx bx-plus me-1"></i> Create New Visit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('technical.scheduled-appointments') }}" class="mb-4"
                            id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request('start_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request('end_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="team_id">Team</label>
                                        <select class="form-control" id="team_id" name="team_id">
                                            <option value="">All Teams</option>
                                            @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ request('team_id')==$team->id ?
                                                'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="contract_number">Contract Number</label>
                                        <input type="text" class="form-control" id="contract_number"
                                            name="contract_number" value="{{ request('contract_number') }}"
                                            placeholder="Enter contract #">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="mb-0 form-group">
                                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                                        <a href="{{ route('technical.scheduled-appointments') }}"
                                            class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Statistics Cards -->
                        <div class="mb-4 row">
                            <div class="col-md-3">
                                <div class="text-white card bg-primary">
                                    <div class="card-body">
                                        <h5 class="card-title">Today's Visits</h5>
                                        <h3 class="card-text">
                                            {{ $todayVisits }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-white card bg-success">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Visits</h5>
                                        <h3 class="card-text">
                                            {{ $totalVisits }}
                                        </h3>
                                        @if($filteredVisitsCount != $totalVisits)
                                            <small>Showing {{ $filteredVisitsCount }} filtered visits</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appointments Grouped by Contract -->
                        <div class="accordion" id="appointmentsAccordion">
                            @foreach($visits as $contractId => $contractVisits)
                            @php
                            $contract = $contractVisits->first()->contract;
                            $branchCount = $contract->is_multi_branch ? $contract->branchs->count() : 1;
                            $totalVisits = $contractVisits->count();
                            $completedVisits = $contractVisits->where('status', 'completed')->count();
                            $visitsPerBranch = $contract->is_multi_branch ? ceil($totalVisits / $branchCount) : $totalVisits;
                            @endphp
                            <div class="mb-2 accordion-item">
                                <h2 class="accordion-header" id="heading{{ $contractId }}">
                                    <button
                                        class="accordion-button {{ !request('contract_number') ? 'collapsed' : '' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $contractId }}">
                                        <div class="d-flex justify-content-between w-100 align-items-center">
                                            <div>
                                                <strong>
                                                    Contract #{{ $contract->contract_number }}
                                                    <a href="{{ route('technical.contract.show', $contract->id) }}"
                                                        class="text-primary ms-2">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $contract->customer->name }} - 
                                                    Completed: {{ $completedVisits }}/{{ $totalVisits }} visits
                                                    ({{ $totalVisits - $completedVisits }} pending)
                                                    @if($contract->is_multi_branch)
                                                    <br>
                                                    <span class="text-info">{{ $visitsPerBranch }} visits × {{ $branchCount }} branches</span>
                                                    @endif
                                                </small>
                                            </div>
                                            <div>
                                                <span class="badge bg-info me-2">{{ $completedVisits }}/{{ $totalVisits }} Visits</span>
                                                @if($contract->contract_status === 'approved')
                                                <span class="badge bg-success">Active</span>
                                                @else
                                                <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $contractId }}"
                                    class="accordion-collapse collapse {{ request('contract_number') ? 'show' : '' }}"
                                    data-bs-parent="#appointmentsAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr class="text-center">
                                                        <th style="width: 120px;">Visit Date</th>
                                                        <th style="width: 120px;">Visit Time</th>
                                                        <th style="width: 150px;">Team</th>
                                                        <th>Location</th>
                                                        <th style="width: 100px;">Status</th>
                                                        <th style="width: 120px;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($contractVisits as $visit)
                                                    <tr>
                                                        <td class="text-center">{{ \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d') }}</td>
                                                        <td class="text-center">{{ $visit->visit_time }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary">
                                                                {{ $visit->team->name }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $visit->branch->branch_address ?? $contract->customer->address }}</td>
                                                        <td class="text-center">
                                                            @if($visit->status === 'scheduled')
                                                            <span class="badge bg-warning">Scheduled</span>
                                                            @elseif($visit->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                            @else
                                                            <span class="badge bg-danger">{{ ucfirst($visit->status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="action-buttons">
                                                                @if($visit->status === 'scheduled')
                                                                <button type="button" class="btn btn-primary btn-sm"
                                                                    onclick="editAppointment({{ $visit->id }}, '{{ $visit->visit_date }}',
                                                                                '{{ $visit->visit_time }}',
                                                                                {{ $visit->team_id }})">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <a href="{{ route('technical.appointment.cancel', $visit->id) }}"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                                @endif
                                                                @if($visit->status === 'completed')
                                                                <a href="{{ route('technical.visit.report.view', $visit->id) }}"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-file"></i>
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
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $visits->appends(request()->except('page'))->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Visit Modal -->
    <div class="modal fade" id="createVisitModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-calendar-plus me-1"></i>
                        Create New Visit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('technical.schedule_visit') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Client Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Client</label>
                                <select class="form-select" name="client_id" id="client_select" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Contract Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Contract</label>
                                <select class="form-select" name="contract_id" id="contract_select" required disabled>
                                    <option value="">Select Contract</option>
                                </select>
                            </div>

                            <!-- Branch Selection (if applicable) -->
                            <div class="col-md-6">
                                <label class="form-label">Branch (Optional)</label>
                                <select class="form-select" name="branch_id" id="branch_select" disabled>
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <!-- Visit Type -->
                            <div class="col-md-6">
                                <label class="form-label">Visit Type</label>
                                <select class="form-select" name="visit_type" required>
                                    <option value="regular">Regular Visit</option>
                                    <option value="regular">Regular</option>
                                    <option value="complementary">Complementary</option>
                                    <option value="free">Free</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>

                            <!-- Date Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Visit Date</label>
                                <input type="date" class="form-control" name="visit_date" required
                                    min="{{ date('Y-m-d') }}">
                                <small class="text-muted">Select any date from today onwards</small>
                            </div>

                            <!-- Time Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Visit Time</label>
                                <input type="time" class="form-control" name="visit_time" required>
                                <small class="text-muted">Regular working hours are 8:00 AM to 2:00 PM</small>
                            </div>

                            <!-- Team Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Assign Team</label>
                                <select class="form-select" name="team_id" required>
                                    <option value="">Select Team</option>
                                    @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Warning Messages -->
                        <div id="schedule_warnings" class="mt-3 alert alert-warning d-none">
                            <ul class="mb-0" id="warning_list"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i>Create Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Appointment Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="edit_visit_date">Visit Date</label>
                            <input type="date" class="form-control" id="edit_visit_date" name="visit_date" required
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                        <div class="mb-3 form-group">
                            <label for="edit_visit_time">Visit Time</label>
                            <input type="time" class="form-control" id="edit_visit_time" name="visit_time" required>
                            <small class="form-text text-muted">Select a time between 8:00 AM and 4:00 PM (visits take 2
                                hours)</small>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="edit_team_id">Assign Team</label>
                            <select class="form-control" id="edit_team_id" name="team_id" required>
                                <option value="">Select Team</option>
                                @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pagination {
            margin-bottom: 0;
        }
        .page-item.active .page-link {
            background-color: #556ee6;
            border-color: #556ee6;
        }
        .page-link {
            color: #556ee6;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        .pagination .page-link span {
            font-size: 0.75rem;
        }
        .pagination svg {
            width: 12px;
            height: 12px;
            vertical-align: -2px;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-group-sm .btn,
        .btn-sm {
            padding: 0.25rem 0.5rem;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        .badge {
            padding: 0.5em 0.8em;
        }
    </style>
    @endpush

    @push('css')
    <style>
        /* Remove previous pagination styles */
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Client selection change handler
            document.getElementById('client_select').addEventListener('change', function() {
                const clientId = this.value;
                const contractSelect = document.getElementById('contract_select');
                const branchSelect = document.getElementById('branch_select');

                // Reset and disable branch dropdown
                branchSelect.innerHTML = '<option value="">Select Branch</option>';
                branchSelect.disabled = true;

                if (clientId) {
                    // Enable contract dropdown and fetch contracts
                    contractSelect.disabled = false;
                    fetch(`/api/clients/${clientId}/contracts`)
                        .then(response => response.json())
                        .then(contracts => {
                            let options = '<option value="">Select Contract</option>';
                            contracts.forEach(contract => {
                                options += `<option value="${contract.id}">${contract.contract_number}</option>`;
                            });
                            contractSelect.innerHTML = options;
                            contractSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching contracts:', error);
                            contractSelect.innerHTML = '<option value="">Error loading contracts</option>';
                            contractSelect.disabled = true;
                        });
                } else {
                    // Reset and disable contract dropdown
                    contractSelect.innerHTML = '<option value="">Select Contract</option>';
                    contractSelect.disabled = true;
                }
            });

            // Contract selection change handler
            document.getElementById('contract_select').addEventListener('change', function() {
                const contractId = this.value;
                const branchSelect = document.getElementById('branch_select');

                if (contractId) {
                    // Enable branch dropdown and fetch branches
                    branchSelect.disabled = false;
                    fetch(`/api/contracts/${contractId}/branches`)
                        .then(response => response.json())
                        .then(branches => {
                            let options = '<option value="">Select Branch</option>';
                            if (branches && branches.length > 0) {
                                branches.forEach(branch => {
                                    options += `<option value="${branch.id}">${branch.name}</option>`;
                                });
                            }
                            branchSelect.innerHTML = options;
                            branchSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching branches:', error);
                            branchSelect.innerHTML = '<option value="">Error loading branches</option>';
                            branchSelect.disabled = true;
                        });
                } else {
                    // Reset and disable branch dropdown
                    branchSelect.innerHTML = '<option value="">Select Branch</option>';
                    branchSelect.disabled = true;
                }
            });

            // Visit time and date change handlers for warnings
            document.querySelector('#createVisitModal input[name="visit_time"]').addEventListener('change', checkScheduleWarnings);
            document.querySelector('#createVisitModal input[name="visit_date"]').addEventListener('change', checkScheduleWarnings);
        });

        // Schedule warnings check
        function checkScheduleWarnings() {
            const visitDate = document.querySelector('#createVisitModal input[name="visit_date"]').value;
            const visitTime = document.querySelector('#createVisitModal input[name="visit_time"]').value;
            const warningsDiv = document.getElementById('schedule_warnings');
            const warningsList = document.getElementById('warning_list');
            const warnings = [];

            if (visitDate && visitTime) {
                const date = new Date(visitDate);
                const time = new Date(`2000-01-01T${visitTime}`);
                const hours = time.getHours();

                // Check if it's Friday
                if (date.getDay() === 5) {
                    warnings.push('This visit is scheduled for a Friday. Please confirm if this is intentional.');
                }

                // Check if it's outside regular hours (8 AM - 2 PM)
                if (hours < 8 || hours >= 14) {
                    warnings.push('This visit is scheduled outside regular working hours (8:00 AM - 2:00 PM).');
                }
            }

            if (warnings.length > 0) {
                warningsList.innerHTML = warnings.map(warning => `<li>${warning}</li>`).join('');
                warningsDiv.classList.remove('d-none');
            } else {
                warningsDiv.classList.add('d-none');
            }
        }

        //call edit modal
        function openEditModal(visitId, visitDate, visitTime, teamId) {
            const form = document.getElementById('editForm');
            form.action = `/technical/visit/${visitId}/update`;
            document.getElementById('edit_visit_date').value = visitDate;
            document.getElementById('edit_visit_time').value = visitTime;
            document.getElementById('edit_team_id').value = teamId;
            $('#editModal').modal('show');
        }
    </script>
    @endpush
    @endsection