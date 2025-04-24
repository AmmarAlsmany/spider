@extends('shared.dashboard')
@section('content')
<div class="container">
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
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="mb-4 page-breadcrumb d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}"><i
                                    class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('clients.annexes.pending_annexes')
                            }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="breadcrumb-title pe-3 fw-bold text-primary">{{ __('clients.annexes.pending_annexes')
                            }}</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @if($annexes->count() > 0)
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('clients.annexes.contract_number') }}</th>
                                        <th>{{ __('clients.annexes.annex_number') }}</th>
                                        <th>{{ __('clients.annexes.amount') }}</th>
                                        <th>{{ __('clients.annexes.due_date') }}</th>
                                        <th>{{ __('clients.annexes.created_by') }}</th>
                                        <th>{{ __('clients.annexes.created_at') }}</th>
                                        <th>{{ __('clients.annexes.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($annexes as $annex)
                                    <tr>
                                        <td>
                                            <a href="{{ route('client.contract.details', $annex->contract->id) }}"
                                                class="text-primary text-decoration-none">
                                                {{ $annex->contract->contract_number }}
                                            </a>
                                        </td>
                                        <td>{{ $annex->annex_number }}</td>
                                        <td>{{ number_format($annex->additional_amount * 1.15, 2) }} SAR</td>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ \Carbon\Carbon::parse($annex->due_date)->format('M d, Y') }}
                                            </span>
                                        </td>

                                        <td>{{ $annex->creator->name }}</td>
                                        <td>{{ $annex->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="gap-2 d-flex justify-content-center">
                                                <form action="{{ route('contracts.annex.approve', $annex->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('{{ __('clients.annexes.approve_confirm') }}')"
                                                        title="Approve Annex">
                                                        <i class="bx bx-check"></i> {{ __('clients.annexes.approve') }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('contracts.annex.reject', $annex->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('{{ __('clients.annexes.reject_confirm') }}')"
                                                        title="Reject Annex">
                                                        <i class="bx bx-x"></i> {{ __('clients.annexes.reject') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-4">
                                {{ $annexes->links('vendor.pagination.custom') }}
                            </div>
                            @else
                            <div class="py-4 text-center">
                                <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Data"
                                    style="max-width: 200px; opacity: 0.6;">
                                <h4 class="mt-3 text-muted">{{ __('clients.annexes.no_pending_annexes') }}</h4>
                                <p class="text-muted">{{ __('clients.annexes.no_annexes_message') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection