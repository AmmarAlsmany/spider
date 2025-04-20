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
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('clients.contract_visits.scheduled_visits') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.show') }}">{{ __('clients.contracts.my_contracts') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('clients.contract_details.contract_number') }} {{ $contract->contract_number }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bx bx-calendar-check me-2"></i>{{ __('clients.contract_details.contract_details') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 card-title">{{ __('clients.contract_details.contract_details') }}</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('clients.contract_details.contract_number') }}</span>
                                    <strong>{{ $contract->contract_number }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.start_date') }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y')
                                        }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.end_date') }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($contract->contract_end_date)->format('M d, Y')
                                        }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.type') }}</span>
                                    <strong>{{ $contract->type->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.warranty') }}</span>
                                    <strong>{{ __('visits.details.warranty_months', ['months' => $contract->warranty])
                                        }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 card-title">{{ __('visits.details.visit_summary') }}</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.status') }}</span>
                                    <span
                                        class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($contract->contract_status) }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.total_visits') }}</span>
                                    <strong>{{ $contract->visitSchedules->count() }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.completed_visits') }}</span>
                                    <strong>{{ $contract->visitSchedules->where('status', 'completed')->count()
                                        }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bx bx-list-check me-2"></i>{{ __('clients.contract_visits.scheduled_visits') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @php
                $visitsByLocation = $contract->visitSchedules->groupBy('branch.branch_name');
                @endphp

                @foreach($visitsByLocation as $location => $visits)
                <div class="col-lg-6">
                    <div class="border card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-map-pin me-2"></i>{{ $location }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('visits.table.number') }}</th>
                                            <th>{{ __('visits.table.date') }}</th>
                                            <th>{{ __('visits.table.time') }}</th>
                                            <th>{{ __('visits.table.status') }}</th>
                                            <th>{{ __('visits.table.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visits as $visit)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $visit->status == 'completed' ? 'success' : ($visit->status == 'scheduled' ? 'primary' : 'warning') }}">
                                                    {{ ucfirst($visit->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($visit->status == 'scheduled')
                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-visit"
                                                        data-bs-toggle="modal" data-bs-target="#editVisitModal"
                                                        data-visit-id="{{ $visit->id }}"
                                                        data-visit-date="{{ $visit->visit_date }}"
                                                        data-visit-time="{{ $visit->visit_time }}">
                                                        <i class="bx bx-edit-alt me-1"></i>{{ __('visits.actions.edit') }}
                                                    </button>
                                                @elseif($visit->status == 'completed')
                                                    <a href="{{ route('client.visit.details', $visit->id) }}" class="btn btn-sm btn-info">
                                                        <i class="bx bx-show me-1"></i>View
                                                    </a>
                                                @endif
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
        </div>
    </div>
</div>

<!-- Edit Visit Modal -->
<div id="editVisitModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('visits.edit_visit.title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('client.visit.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="visit_id" id="visit_id">
                    <div class="mb-3">
                        <label for="visit_date" class="form-label">{{ __('visits.edit_visit.new_date') }}</label>
                        <input type="date" name="visit_date" id="visit_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="visit_time" class="form-label">{{ __('visits.edit_visit.new_time') }}</label>
                        <input type="time" name="visit_time" id="visit_time" class="form-control" required>
                    </div>
                    <div class="px-0 pb-0 modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                            __('visits.edit_visit.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('visits.edit_visit.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.edit-visit').on('click', function() {
            var visitId = $(this).data('visit-id');
            var visitDate = $(this).data('visit-date');
            var visitTime = $(this).data('visit-time');

            $('#visit_id').val(visitId);
            $('#visit_date').val(visitDate);
            $('#visit_time').val(visitTime);
        });
    });
</script>
@endpush
@endsection