@extends('shared.dashboard')
@section('content')
    <div class="page-content">
        <div class="mb-4 page-breadcrumb d-flex align-items-center">
            <div class="pe-3 breadcrumb-title d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                    <i class="bx bx-arrow-back"></i> {{ __('contract_views.back') }}
                </a>
                <h4 class="mb-0 text-success"><i class="bx bx-check-circle"></i> {{ __('contracts.active_contracts') }}</h4>
            </div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i
                                    class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">
                            {{ __('contracts.active_contract') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="border-0 shadow-sm card">
            <div class="p-4 card-body">
                <!-- Search Form -->
                <div class="mb-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="client_name" class="form-control"
                                placeholder="{{ __('contract_views.search_by_client_name') }}"
                                value="{{ request('client_name') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="client_email" class="form-control"
                                placeholder="{{ __('contract_views.search_by_client_email') }}"
                                value="{{ request('client_email') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="client_phone" class="form-control"
                                placeholder="{{ __('contract_views.search_by_client_phone') }}"
                                value="{{ request('client_phone') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="contract_number" class="form-control"
                                placeholder="{{ __('contract_views.search_by_contract_number') }}"
                                value="{{ request('contract_number') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i> {{ __('contract_views.search') }}
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-secondary">
                                <i class="bx bx-reset"></i> {{ __('contract_views.reset') }}
                            </a>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table id="example2" class="table align-middle table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">{{ __('contract_views.client_name') }}</th>
                                <th class="py-3">{{ __('contract_views.client_email') }}</th>
                                <th class="py-3">{{ __('contract_views.client_phone') }}</th>
                                <th class="py-3">{{ __('contract_views.contract_number') }}</th>
                                <th class="py-3">{{ __('contract_views.contract_type') }}</th>
                                <th class="py-3">{{ __('contract_views.contract_amount') }}</th>
                                <th class="py-3">{{ __('contract_views.warranty') }}</th>
                                <th class="py-3">{{ __('contract_views.number_of_visits') }}</th>
                                <th class="py-3">{{ __('contract_views.start_date') }}</th>
                                <th class="py-3">{{ __('contract_views.end_date') }}</th>
                                <th class="py-3">{{ __('contract_views.status') }}</th>
                                <th class="py-3">{{ __('contract_views.view_visit_details') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contracts as $contract)
                                <tr>
                                    <td>
                                        <a href="{{ route('view.my.clients.details', ['id' => $contract->customer->id]) }}"
                                            class="text-decoration-none fw-semibold text-primary">
                                            {{ $contract->customer->name }}
                                        </a>
                                    </td>
                                    <td><a
                                            href="mailto:{{ $contract->customer->email }}">{{ $contract->customer->email }}</a>
                                    </td>
                                    <td><a
                                            href="tel:{{ $contract->customer->phone }}">{{ $contract->customer->phone }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('contract.show.details', ['id' => $contract->id]) }}"
                                            class="text-decoration-none fw-semibold text-primary">
                                            {{ $contract->contract_number }}
                                        </a>
                                    </td>
                                    <td><span class="fw-medium">{{ $contract->type->name }}</span></td>
                                    <td><span class="fw-semibold">{{ $contract->contract_price }} SAR</span></td>
                                    <td><span class="fw-medium">{{ $contract->warranty }}
                                            {{ __('contract_views.months') }}</span></td>
                                    <td><span class="fw-medium">{{ $contract->number_of_visits }}
                                            {{ __('contract_views.visits') }}</span></td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $contract->contract_start_date
                                                ? date('M d, Y', strtotime($contract->contract_start_date))
                                                : __('contract_views.not_set') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-medium">
                                            {{ $contract->contract_end_date
                                                ? date('M d, Y', strtotime($contract->contract_end_date))
                                                : __('contract_views.not_set') }}
                                        </span>
                                    </td>
                                    @if ($contract->contract_status == 'pending')
                                        <td>
                                            <div class="px-3 py-2 badge bg-warning text-dark rounded-pill">
                                                <i class='bx bxs-circle me-1'></i>
                                                {{ strtoupper($contract->contract_status) }}
                                            </div>
                                        </td>
                                    @elseif ($contract->contract_status == 'approved')
                                        <td>
                                            <div class="px-3 py-2 badge bg-success rounded-pill">
                                                <i class='bx bxs-circle me-1'></i>
                                                {{ strtoupper($contract->contract_status) }}
                                            </div>
                                        </td>
                                    @else
                                        <td>
                                            <div class="px-3 py-2 badge bg-info rounded-pill">
                                                <i class='bx bxs-circle me-1'></i>
                                                {{ strtoupper($contract->contract_status) }}
                                            </div>
                                        </td>
                                    @endif
                                    @if ($contract->contract_status == 'approved')
                                        <td>
                                            <a href="{{ route('view.contract.visit', ['id' => $contract->id]) }}"
                                                class="px-4 btn btn-primary btn-sm rounded-pill">
                                                <i class="bx bx-show me-1"></i>{{ __('contract_views.view_visit') }}
                                            </a>
                                        </td>
                                    @else
                                        <td><span class="text-muted">-</span></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="py-3">{{ __('contract_views.client_name') }}</th>
                                <th class="py-3">{{ __('contract_views.client_email') }}</th>
                                <th class="py-3">{{ __('contract_views.client_phone') }}</th>
                                <th class="py-3">{{ __('contract_views.contract_number') }}</th>
                                <th class="py-3">{{ __('contract_views.contract_type') }}</th>
                                <th class="py-3">{{ __('contract_views.contract_amount') }}</th>
                                <th class="py-3">{{ __('contract_views.warranty') }}</th>
                                <th class="py-3">{{ __('contract_views.number_of_visits') }}</th>
                                <th class="py-3">{{ __('contract_views.start_date') }}</th>
                                <th class="py-3">{{ __('contract_views.end_date') }}</th>
                                <th class="py-3">{{ __('contract_views.status') }}</th>
                                <th class="py-3">{{ __('contract_views.view_visit_details') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                var table = $('#example2').DataTable({
                    lengthChange: false,
                    buttons: ['copy', 'excel', 'pdf', 'print']
                });

                table.buttons().container()
                    .appendTo('#example2_wrapper .col-md-6:eq(0)');
            });
        </script>
    @endpush
@endsection
