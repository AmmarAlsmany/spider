@extends('shared.dashboard')
@push('style')
<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #eee;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table thead th {
        border-bottom: none;
        font-weight: 600;
        color: #444;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .text-decoration-none:hover {
        color: #0056b3 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate-button {
        border-radius: 8px;
        margin: 0 2px;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #eee;
        padding: 5px 10px;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #eee;
        padding: 5px 10px;
    }

    .dt-buttons .btn {
        border-radius: 8px;
        margin-right: 5px;
        padding: 5px 15px;
        font-size: 0.875rem;
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
            <h4 class="mb-0 text-danger"><i class="bx bx-x-circle"></i> Canceled Contracts</h4>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Canceled Contract</li>
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
                            <th>Rejection Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contracts as $contract)
                        <tr>
                            <td>
                                <a href="{{ route('view.my.clients.details', ['id' => $contract->customer->id]) }}"
                                    class="text-primary text-decoration-none">
                                    <i class="bi bi-person me-2"></i>{{ $contract->customer->name }}
                                </a>
                            </td>
                            <td>
                                <a href="mailto:{{ $contract->customer->email }}"><i
                                        class="bi bi-envelope me-2 text-muted"></i>{{ $contract->customer->email }}
                                </a>
                            </td>
                            <td>
                                <a href="tel:{{ $contract->customer->phone }}"><i
                                        class="bi bi-phone me-2 text-muted"></i>{{ $contract->customer->phone }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                    class="text-primary text-decoration-none">
                                    <i class="bi bi-file-text me-2"></i>{{ $contract->contract_number }}
                                </a>
                            </td>
                            <td>{{ $contract->type->name }} {{ $contract->type->type }}</td>
                            <td>
                                <i class="bi bi-currency-dollar me-2 text-muted"></i>{{ $contract->contract_price }}
                                SAR
                            </td>
                            <td>
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ $contract->contract_start_date ? date('M d, Y',
                                strtotime($contract->contract_start_date)) : 'Not set' }}
                            </td>
                            <td>
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ $contract->contract_end_date ? date('M d, Y',
                                strtotime($contract->contract_end_date)) : 'Not set' }}
                            </td>
                            <td>
                                <div class="px-3 py-1 badge rounded-pill bg-danger-subtle text-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    {{ strtoupper($contract->contract_status) }}
                                </div>
                            </td>
                            <td>
                                <i class="bi bi-exclamation-triangle me-2 text-muted"></i>
                                {{ $contract->rejection_reason ? $contract->rejection_reason : 'Stopped from the Sales
                                Manager' }}
                            </td>
                            <td>
                                <form action="{{ route('contract.return', ['id' => $contract->id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="bi bi-arrow-return-left"></i> Return
                                    </button>
                                </form>
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
<!-- DataTables Scripts -->
<script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: true,
                paging: true,
                info: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"l><"col-md-6"p>>',
                buttons: [{
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

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
</script>
@endpush