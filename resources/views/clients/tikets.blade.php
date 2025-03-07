@extends('shared.dashboard')
@section('title', __('tickets.my_tickets'))
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
    <div class="row">
        <div class="col-12">
            <div class="mb-4 card">
                <div class="pb-0 card-header d-flex justify-content-between align-items-center">
                    <h6>{{ __('tickets.my_tickets') }}</h6>
                    <a href="{{ route('client.tickets.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus"></i> {{ __('tickets.new_ticket') }}
                    </a>
                </div>
                <div class="px-0 pt-0 pb-2 card-body">
                    <div class="p-0 table-responsive">
                        <table class="table mb-0 align-items-center">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        {{ __('tickets.subject') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        {{ __('tickets.status') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        {{ __('tickets.priority') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        {{ __('tickets.created') }}</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        {{ __('tickets.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <div class="px-2 py-1 d-flex">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $ticket->tiket_title }}</h6>
                                                <p class="mb-0 text-xs text-secondary">{{
                                                    Str::limit($ticket->tiket_description, 50) }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge badge-sm bg-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ __('tickets.status_types.' . $ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-sm bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                                            {{ __('tickets.priority_levels.' . $ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-xs text-secondary font-weight-bold">{{
                                            $ticket->created_at->diffForHumans() }}</span>
                                    </td>
                                    @if($ticket->status != 'open')
                                    <td>
                                        <a href="{{ route('client.tikets.show', $ticket->id) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="bx bx-message-square-detail"></i> {{ __('tickets.view_replies') }}
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center">
                                        <p class="mb-0 text-secondary">{{ __('tickets.no_tickets') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection