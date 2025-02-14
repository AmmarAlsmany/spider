@extends('shared.dashboard')
@section('content')
<div class="page-content">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('visits.contract_visits') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.show') }}">{{ __('visits.contracts') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('visits.contract_number', ['number' => $contract->contract_number]) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bx bx-calendar-check me-2"></i>{{ __('visits.contract_details') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 card-title">{{ __('visits.details.contract_info') }}</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.number') }}</span>
                                    <strong>{{ $contract->contract_number }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.start_date') }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($contract->contract_start_date)->format('M d, Y') }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.end_date') }}</span>
                                    <strong>{{ \Carbon\Carbon::parse($contract->contract_end_date)->format('M d, Y') }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.type') }}</span>
                                    <strong>{{ $contract->type->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.warranty') }}</span>
                                    <strong>{{ __('visits.details.warranty_months', ['months' => $contract->warranty]) }}</strong>
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
                                    <span class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($contract->contract_status) }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.total_visits') }}</span>
                                    <strong>{{ $contract->visitSchedules->count() }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ __('visits.details.completed_visits') }}</span>
                                    <strong>{{ $contract->visitSchedules->where('status', 'completed')->count() }}</strong>
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
                    <h5 class="mb-0"><i class="bx bx-list-check me-2"></i>{{ __('visits.visit_schedule') }}</h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('visits.table.number') }}</th>
                            <th>{{ __('visits.table.date') }}</th>
                            <th>{{ __('visits.table.time') }}</th>
                            <th>{{ __('visits.table.location') }}</th>
                            <th>{{ __('visits.table.address') }}</th>
                            <th>{{ __('visits.table.status') }}</th>
                            <th>{{ __('visits.table.team') }}</th>
                            <th>{{ __('visits.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->visitSchedules as $index => $visit)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($visit->visit_time)->format('h:i A') }}</td>
                            <td>
                                <span class="d-flex align-items-center">
                                    <i class="bx bx-map-pin me-1"></i>
                                    {{ $visit->branch->branch_name ??__('visits.location.not_specified') }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $visit->branch->branch_address ?? $visit->contract->customer->address }}
                                </small>
                            </td>
                            <td>
                                @if($visit->status == 'completed')
                                    <span class="badge bg-success">{{ __('visits.status.completed') }}</span>
                                @elseif($visit->status == 'scheduled')
                                    <span class="badge bg-info">{{ __('visits.status.scheduled') }}</span>
                                @elseif($visit->status == 'cancelled')
                                    <span class="badge bg-danger">{{ __('visits.status.cancelled') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($visit->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($visit->team)
                                    <span class="d-flex align-items-center">
                                        <i class="bx bx-group me-1"></i>
                                        {{ $visit->team->name }}
                                    </span>
                                @else
                                    <span class="text-muted">{{ __('visits.team.not_assigned') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="gap-2 d-flex">
                                    @if($visit->status == 'completed')
                                        <a href="{{ route('client.visit.details', $visit->id) }}" 
                                           class="gap-1 btn btn-sm btn-primary d-flex align-items-center">
                                            <i class="bx bx-file"></i>
                                            {{ __('visits.actions.view_report') }}
                                        </a>
                                    @else
                                        <button type="button" 
                                                class="gap-1 btn btn-sm btn-secondary d-flex align-items-center"
                                                onclick="editVisit('{{ $visit->id }}', '{{ $visit->visit_date }}', '{{ $visit->visit_time }}')">
                                            <i class="bx bx-edit"></i>
                                            {{ __('visits.actions.reschedule') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center">
                                <div class="text-muted">
                                    <i class="bx bx-calendar-x fs-1"></i>
                                    <p class="mt-2">{{ __('visits.no_visits') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('visits.edit_visit.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('visits.edit_visit.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editVisit(visitId, visitDate, visitTime) {
        document.getElementById('visit_id').value = visitId;
        document.getElementById('visit_date').value = visitDate;
        document.getElementById('visit_time').value = visitTime;
        
        const modal = new bootstrap.Modal(document.getElementById('editVisitModal'));
        modal.show();
    }

    // Handle modal close properly
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editVisitModal'));
            if (modal) {
                modal.hide();
                // Remove backdrop after modal is hidden
                setTimeout(() => {
                    document.querySelector('.modal-backdrop').remove();
                }, 200);
            }
        });
    });
</script>
@endpush
@endsection