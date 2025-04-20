@extends('shared.dashboard')

@section('content')
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 card-title">{{ __('contract_views.visit_report') }}</h4>
                            <a href="{{ route('contracts.show', $visit->contract_id) }}" class="btn btn-primary">
                                <i class="bx bx-arrow-back"></i> {{ __('contract_views.back') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 row">
                                <div class="col-md-6">
                                    <h5>{{ __('contract_views.contract_details') }}</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>{{ __('contract_views.contract_number') }}</th>
                                            <td>{{ $visit->contract->contract_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('contract_views.client_information') }}</th>
                                            <td>{{ $visit->contract->client->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('contract_views.visit_date') }}</th>
                                            <td>{{ $visit->visit_date }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('contract_views.status') }}</th>
                                            <td>
                                                @if ($visit->status == 'completed')
                                                    <span
                                                        class="badge bg-success">{{ __('contract_views.completed') }}</span>
                                                @elseif($visit->status == 'pending')
                                                    <span
                                                        class="badge bg-warning">{{ __('contract_views.pending') }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger">{{ __('contract_views.cancelled') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>{{ __('contract_views.technician') }}</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>{{ __('contract_views.name') }}</th>
                                            <td>{{ $visit->technician->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('contract_views.phone') }}</th>
                                            <td>{{ $visit->technician->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('contract_views.email') }}</th>
                                            <td>{{ $visit->technician->email }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <h5 class="mt-4">{{ __('contract_views.visit_details') }}</h5>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-4 card">
                                        <div class="card-body">
                                            <h6>{{ __('contract_views.work_done') }}</h6>
                                            <p>{{ $visit->work_description ?: 'N/A' }}</p>

                                            @if ($visit->notes)
                                                <h6>{{ __('contract_views.notes') }}</h6>
                                                <p>{{ $visit->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($visit->status == 'completed')
                                <div class="mt-4 row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 card-title">{{ __('contract_views.customer_signature') }}
                                                </h5>
                                            </div>
                                            <div class="text-center card-body">
                                                @if ($visit->customer_signature)
                                                    <img src="{{ $visit->customer_signature }}" alt="Customer Signature"
                                                        class="img-fluid" style="max-height: 150px;">
                                                @else
                                                    <p class="text-muted">{{ __('contract_views.no_signature_available') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 card-title">{{ __('contract_views.technician_signature') }}
                                                </h5>
                                            </div>
                                            <div class="text-center card-body">
                                                @if ($visit->technician_signature)
                                                    <img src="{{ $visit->technician_signature }}"
                                                        alt="Technician Signature" class="img-fluid"
                                                        style="max-height: 150px;">
                                                @else
                                                    <p class="text-muted">{{ __('contract_views.no_signature_available') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($visit->status == 'pending')
                                <div class="mt-4 row">
                                    <div class="text-center col-12">
                                        <form action="{{ route('visits.reschedule', $visit->id) }}" method="POST">
                                            @csrf
                                            <div class="row justify-content-center">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="reschedule_date"
                                                            class="form-label">{{ __('contract_views.reschedule') }}</label>
                                                        <input type="date" name="reschedule_date" id="reschedule_date"
                                                            class="form-control" required min="{{ date('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="submit"
                                                        class="btn btn-primary w-100">{{ __('contract_views.submit') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
