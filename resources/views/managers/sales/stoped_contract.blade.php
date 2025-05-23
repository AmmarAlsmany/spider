@extends('shared.dashboard')
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
                <i class="bx bx-arrow-back"></i> {{ __('sales_views.back') }}
            </a>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">{{ __('sales_views.stopped_contract') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="shadow-sm card">
        <div class="card-header">
            <h4 class="mb-0 text-danger"><i class="bx bx-x-circle"></i> {{ __('sales_views.stopped_contracts') }}</h4>
        </div>
        <div class="card-body">
            @include('shared.contract_search')
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('sales_views.client_name') }}</th>
                            <th>{{ __('sales_views.client_email') }}</th>
                            <th>{{ __('sales_views.client_phone') }}</th>
                            <th>{{ __('sales_views.contract_number') }}</th>
                            <th>{{ __('sales_views.contract_type') }}</th>
                            <th>{{ __('sales_views.contract_amount') }}</th>
                            <th>{{ __('sales_views.start_date') }}</th>
                            <th>{{ __('sales_views.end_date') }}</th>
                            <th>{{ __('sales_views.contract_status') }}</th>
                            <th>{{ __('sales_views.actions') }}</th>
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
                                <a href="mailto:{{ $contract->customer->email }}"
                                    class="text-primary text-decoration-none">
                                    {{ $contract->customer->email }}
                                </a>
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-phone me-2 text-muted"></i>
                                <a href="tel:{{ $contract->customer->phone }}"
                                    class="text-primary text-decoration-none">
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
                                {{ $contract->type->name }} {{ $contract->type->description }}
                                {{ $contract->type->type }}
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-currency-dollar me-2 text-muted"></i>
                                {{ $contract->contract_price }} {{ __('sales_views.sar') }}
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ $contract->contract_start_date ? date('M d, Y',
                                strtotime($contract->contract_start_date)) : __('sales_views.not_set') }}
                            </td>
                            <td class="align-middle">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ $contract->contract_end_date ? date('M d, Y',
                                strtotime($contract->contract_end_date)) : __('sales_views.not_set') }}
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
                                    <i class="bi bi-eye"></i> {{ __('sales_views.view') }}
                                </a>
                                <button type="button" class="btn btn-sm btn-success"
                                    onclick="setReturnContractId('{{ $contract->id }}')" data-bs-toggle="modal"
                                    data-bs-target="#returnContractModal">
                                    <i class="bi bi-arrow-clockwise"></i> {{ __('sales_views.return_contract') }}
                                </button>
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

<!-- Return Contract Modal -->
<div class="modal fade" id="returnContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('sales_views.return_contract') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('sales_views.how_return_contract') }}</p>
                <form id="returnContractForm" action="{{ route('contract.return', ['id' => ':id']) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select" required>
                            <option value="">{{ __('sales_views.select_status') }}</option>
                            <option value="approved">{{ __('sales_views.return_as_approved') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('sales_views.cancel') }}</button>
                <button type="submit" form="returnContractForm" class="btn btn-primary">{{ __('sales_views.return_contract') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    function setReturnContractId(id) {
            document.getElementById('returnContractForm').action = "{{ route('contract.return', ['id' => ':id']) }}"
                .replace(':id', id);
        }
</script>
<!-- DataTables Scripts -->
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