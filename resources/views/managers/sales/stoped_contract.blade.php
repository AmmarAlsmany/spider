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
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-danger"><i class="bx bx-x-circle"></i> Stopped Contracts</h4>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stopped Contract</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="shadow-sm card">
        <div class="card-body">
            @include('shared.contract_search')
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Client Name</th>
                            <th>Client Email</th>
                            <th>Client Phone</th>
                            <th>Contract Number</th>
                            <th>Contract Type</th>
                            <th>Contract amount</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Contract Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contracts as $contract)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="client-icon">
                                        <i class="bx bx-user-circle"></i>
                                    </div>
                                    <div class="ms-2">
                                        <a href="{{ route('view.my.clients.details', ['id' => $contract->customer->id]) }}"
                                            class="mb-0 text-primary fw-semibold text-decoration-none">
                                            {{ $contract->customer->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-envelope me-2 text-muted"></i>
                                <a href="mailto:{{ $contract->customer->email }}" class="text-primary text-decoration-none">
                                    {{ $contract->customer->email }}
                                </a>
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-phone me-2 text-muted"></i>
                                <a href="tel:{{ $contract->customer->phone }}" class="text-primary text-decoration-none">
                                    {{ $contract->customer->phone }}
                                </a>
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                    class="text-primary text-decoration-none">
                                    <i class="bi bi-file-text me-2"></i>
                                    {{ $contract->contract_number }}
                                </a>
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-tag me-2 text-muted"></i>
                                {{ $contract->type->name }} {{ $contract->type->description }} {{ $contract->type->type }}
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-currency-dollar me-2 text-muted"></i>
                                {{ $contract->contract_price }} SAR
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ $contract->contract_start_date ? date('M d, Y',
                                strtotime($contract->contract_start_date)) : 'Not set' }}
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ $contract->contract_end_date ? date('M d, Y',
                                strtotime($contract->contract_end_date)) : 'Not set' }}
                            </td>
                            <td class="align-middle">
                                <span class="px-3 py-1 badge rounded-pill bg-danger-subtle text-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    {{ ucfirst($contract->contract_status) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                    class="btn btn-sm btn-primary me-2">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <button type="button" class="btn btn-sm btn-success" 
                                    onclick="setReturnContractId('{{ $contract->id }}')" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#returnContractModal">
                                    <i class="bi bi-arrow-clockwise"></i> Return Contract
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Return Contract Modal -->
<div class="modal fade" id="returnContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>How would you like to return this contract?</p>
                <form id="returnContractForm" action="{{ route('contract.return', ['id' => ':id']) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="pending">Return as Pending</option>
                            <option value="approved">Return as Approved</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="returnContractForm" class="btn btn-primary">Return Contract</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setReturnContractId(id) {
        document.getElementById('returnContractForm').action = "{{ route('contract.return', ['id' => ':id']) }}".replace(':id', id);
    }
</script>
<script>
    $(document).ready(function() {
        $('#example2').DataTable({
            lengthChange: false,
            pageLength: 10,
            dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-light shadow-sm'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-light shadow-sm'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-light shadow-sm'
                },
                {
                    extend: 'print',
                    className: 'btn btn-light shadow-sm'
                }
            ]
        });
    });
</script>
@endpush

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }

    .btn-sm {
        padding: 5px 10px;
    }
</style>
@endpush