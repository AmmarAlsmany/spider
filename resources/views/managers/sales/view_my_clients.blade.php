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
                <h4 class="mb-0 text-primary"><i class="bx bx-group"></i> My Clients</h4>
            </div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Clients List</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="shadow-sm card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="clientsTable" class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" width="70">#</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Email</th>
                                <th scope="col">Address</th>
                                <th scope="col" width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $index => $item)
                                <tr>
                                    <td class="align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="client-icon">
                                                <i class="bx bx-user-circle"></i>
                                            </div>
                                            <div class="ms-2">
                                                <a href="{{ route('view.my.clients.details', ['id' => $item->id]) }}" 
                                                   class="mb-0 text-primary fw-semibold text-decoration-none">
                                                    {{ $item->name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <i class="bi bi-phone me-2 text-muted"></i>
                                        {{ $item->phone }}
                                    </td>
                                    <td class="align-middle">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        {{ $item->email }}
                                    </td>
                                    <td class="align-middle">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i>
                                        {{ $item->address }}
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('view.my.clients.details', ['id' => $item->id]) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
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
            padding: 1rem 0.75rem;
        }
        .table td {
            padding: 1rem 0.75rem;
        }
        .table tbody tr {
            transition: all 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .client-icon {
            width: 40px;
            height: 40px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .client-icon i {
            font-size: 24px;
            color: #6c757d;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>

    <!-- DataTables Scripts -->
    <script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#clientsTable').DataTable({
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
@endsection
