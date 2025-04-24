@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">{{ __('admin.tickets.title') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('admin.tickets.all_tickets') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('admin.tickets.title') }}</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <form class="ms-auto position-relative">
                    <div class="px-3 position-absolute top-50 translate-middle-y search-icon"><i class="bx bx-search"></i></div>
                    <input class="form-control ps-5" type="text" placeholder="{{ __('admin.tickets.search_placeholder') }}">
                </form>
            </div>
            <div class="mt-3 table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin.tickets.table.id') }}</th>
                            <th>{{ __('admin.tickets.table.title') }}</th>
                            <th>{{ __('admin.tickets.table.customer') }}</th>
                            <th>{{ __('admin.tickets.table.contract') }}</th>
                            <th>{{ __('admin.tickets.table.status') }}</th>
                            <th>{{ __('admin.tickets.table.created') }}</th>
                            <th>{{ __('admin.tickets.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>{{ $ticket->tiket_title }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <h6 class="mb-0 font-14">{{ $ticket->customer->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($ticket->customer && $ticket->customer->contracts->isNotEmpty())
                                    <a href="{{ route('admin.contracts.show', $ticket->customer->contracts->first()->id) }}" class="text-primary">
                                        {{ $ticket->tiket_description }} 
                                    </a>
                                @else
                                    <span class="text-muted">{{ __('admin.tickets.table.no_contract') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->status == 'open' ? 'success' : 'secondary' }} text-white">
                                    {{ $ticket->status == 'open' ? __('admin.tickets.table.status.open') : __('admin.tickets.table.status.closed') }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="gap-3 d-flex">
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('admin.tickets.table.view_details') }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center">
                                <div class="text-center">
                                    <i class="bx bx-ticket fs-1 text-muted"></i>
                                    <p class="mt-2">{{ __('admin.tickets.table.no_tickets') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                {{ $tickets->links("vendor.pagination.custom") }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
