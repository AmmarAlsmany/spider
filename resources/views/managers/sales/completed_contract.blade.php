@extends('shared.dashboard')
@push('style')
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
@section('content')
    <div class="page-content">
        @if (session('error'))
            <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
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
                <h4 class="mb-0 text-success"><i class="bx bx-check-circle"></i> Completed Contracts</h4>
            </div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i
                                    class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">Completed Contract</li>
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
                                <th>Id</th>
                                <th>Contract Number</th>
                                <th>Client Name</th>
                                <th>Client Email</th>
                                <th>Client Phone</th>
                                <th>Contract Type</th>
                                <th>Contract amount</th>
                                <th>Start Date</th>
                                <th>Contract Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contracts as $contract)
                                <tr>
                                    <td>{{ $contract->id }}</td>
                                    <td>
                                        <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                            class="text-primary text-decoration-none">
                                            <i class="bi bi-file-text me-2"></i>{{ $contract->contract_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('view.my.clients.details', ['id' => $contract->customer->id]) }}"
                                            class="text-primary text-decoration-none">
                                            <i class="bi bi-person me-2"></i>{{ $contract->customer->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <i class="bi bi-envelope me-2 text-muted"></i>{{ $contract->customer->email }}
                                    </td>
                                    <td>
                                        <i class="bi bi-phone me-2 text-muted"></i>{{ $contract->customer->phone }}
                                    </td>
                                    <td>
                                        <i class="bi bi-tag me-2 text-muted"></i>{{ $contract->type->name }}
                                        {{ $contract->type->type }}
                                    </td>
                                    <td>
                                        <i
                                            class="bi bi-currency-dollar me-2 text-muted"></i>{{ number_format($contract->contract_price, 2) }}
                                        SAR
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-event me-2 text-muted"></i>
                                        {{ $contract->contract_start_date ? date('M d, Y', strtotime($contract->contract_start_date)) : 'Not set' }}
                                    </td>
                                    <td>
                                        @if ($contract->contract_status == 'pending')
                                            <span class="px-3 py-1 badge rounded-pill bg-warning-subtle text-warning">
                                                <i class="bi bi-clock me-1"></i>{{ ucfirst($contract->contract_status) }}
                                            </span>
                                        @elseif ($contract->contract_status == 'Not approved')
                                            <span class="px-3 py-1 badge rounded-pill bg-success-subtle text-success">
                                                <i
                                                    class="bi bi-check-circle me-1"></i>{{ ucfirst($contract->contract_status) }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1 badge rounded-pill bg-info-subtle text-info">
                                                <i
                                                    class="bi bi-info-circle me-1"></i>{{ ucfirst($contract->contract_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- renew the contract --}}
                                        <a href="{{ route('contract.renewal.form', ['id' => $contract->id]) }}"
                                            class="shadow-sm btn btn-primary btn-sm">
                                            <i class="bi bi-calendar-plus me-1"></i>Renew
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $contracts->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#example2').DataTable({
                lengthChange: false,
                paging: false,
                info: false,
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm'
                    }
                ]
            });
        });
    </script>
@endpush
