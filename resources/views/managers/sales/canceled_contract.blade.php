@extends('shared.dashboard')
@section('content')
    <div class="page-content">
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
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
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
                                        <a href="mailto:{{ $contract->customer->email }}"><i class="bi bi-envelope me-2 text-muted"></i>{{ $contract->customer->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $contract->customer->phone }}"><i class="bi bi-phone me-2 text-muted"></i>{{ $contract->customer->phone }}
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
                                        <i class="bi bi-currency-dollar me-2 text-muted"></i>{{ $contract->contract_price }} SAR
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-event me-2 text-muted"></i>
                                        {{ $contract->contract_start_date ? date('M d, Y', strtotime($contract->contract_start_date)) : 'Not set' }}
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-event me-2 text-muted"></i>
                                        {{ $contract->contract_end_date ? date('M d, Y', strtotime($contract->contract_end_date)) : 'Not set' }}
                                    </td>
                                    <td>
                                        <div class="px-3 py-1 badge rounded-pill bg-danger-subtle text-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            {{ strtoupper($contract->contract_status) }}
                                        </div>
                                    </td>
                                    <td>{{ $contract->rejection_reason }}</td>
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
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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

    <!-- DataTables Scripts -->
    <script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: false,
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
                ],
                "pageLength": 10,
                "dom": '<"row"<"col-md-6"B><"col-md-6"f>>rtip'
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
