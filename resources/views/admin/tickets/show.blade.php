@extends('shared.dashboard')

@section('content')
    <div class="page-content">
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3">{{ __('admin.tickets.title') }}</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.tickets.index') }}">{{ __('admin.tickets.all_tickets') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.tickets.details.title') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Ticket Information -->
            <div class="col-12 col-lg-8">
                <div class="mb-4 card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                {{ __('admin.tickets.details.ticket_number', ['number' => $ticket->tiket_number]) }}</h5>
                            <span class="badge bg-{{ $ticket->status == 'open' ? 'success' : 'warning' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-3">{{ $ticket->tiket_title }}</h4>
                        <div class="mb-4">
                            <p class="mb-2 text-muted">{{ __('admin.tickets.details.description_label') }}:</p>
                            <p>{{ $ticket->tiket_description }}</p>
                        </div>

                        <div class="mb-4 row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('admin.tickets.details.priority') }}:</strong>
                                    <span
                                        class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </p>
                                <p class="mb-2"><strong>{{ __('admin.tickets.details.created') }}:</strong>
                                    {{ $ticket->created_at->format('Y-m-d H:i') }}</p>
                                @if ($ticket->solved_at)
                                    <p class="mb-0"><strong>{{ __('admin.tickets.details.solved') }}:</strong>
                                        {{ $ticket->solved_at->format('Y-m-d H:i') }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>{{ __('admin.tickets.details.customer') }}:</strong>
                                    {{ $ticket->customer->name }}</p>
                                @if ($ticket->solver)
                                    <p class="mb-2"><strong>{{ __('admin.tickets.details.assigned_to') }}:</strong>
                                        {{ $ticket->solver->name }}</p>
                                @endif
                                <p class="mb-2"><strong>{{ __('admin.tickets.details.created_by') }}:</strong>
                                    {{ $ticket->creator->name }}</p>
                                @if ($ticket->customer->contracts->isNotEmpty())
                                    <p class="mb-0">
                                        <strong>{{ __('admin.tickets.details.contract') }}:</strong>
                                        <a href="{{ route('admin.contracts.show', $ticket->customer->contracts->first()->id) }}"
                                            class="text-primary">
                                            #{{ $ticket->customer->contracts->first()->contract_number }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Replies -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('admin.tickets.details.replies') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse($ticket->ticketReplies as $reply)
                            <div class="pb-4 mb-4 border-bottom">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $reply->user->name }}</strong>
                                        <span class="text-muted ms-2">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    @if ($reply->user_id === auth()->id())
                                        <div class="dropdown">
                                            <button class="p-0 btn btn-link text-muted" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="#">{{ __('admin.tickets.details.edit_reply') }}</a>
                                                </li>
                                                <li><a class="dropdown-item text-danger"
                                                        href="#">{{ __('admin.tickets.details.delete_reply') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <p class="mb-0">{{ $reply->reply }}</p>
                            </div>
                        @empty
                            <div class="py-4 text-center">
                                <i class="bx bx-message-square fs-1 text-muted"></i>
                                <p class="mt-2">{{ __('admin.tickets.details.no_replies') }}</p>
                            </div>
                        @endforelse

                        <!-- Reply Form -->
                        @if ($ticket->status === 'open')
                            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="mt-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="content"
                                        class="form-label">{{ __('admin.tickets.details.your_reply') }}</label>
                                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit"
                                        class="btn btn-primary">{{ __('admin.tickets.details.send_reply') }}</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-12 col-lg-4">
                <!-- Customer Information -->
                <div class="mb-4 card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('admin.tickets.details.customer_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>{{ __('admin.tickets.details.customer_name') }}:</strong>
                            {{ $ticket->customer->name }}</p>
                        <p class="mb-2"><strong>{{ __('admin.tickets.details.customer_email') }}:</strong>
                            {{ $ticket->customer->email }}</p>
                        <p class="mb-2"><strong>{{ __('admin.tickets.details.customer_phone') }}:</strong>
                            {{ $ticket->customer->phone }}</p>
                        <p class="mb-2"><strong>{{ __('admin.tickets.details.customer_mobile') }}:</strong>
                            {{ $ticket->customer->mobile }}</p>
                        <p class="mb-0"><strong>{{ __('admin.tickets.details.customer_address') }}:</strong>
                            {{ $ticket->customer->address }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('admin.tickets.details.actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="gap-2 d-grid">
                            @if ($ticket->status === 'open')
                                <button class="btn btn-success" type="button">
                                    <i class="bx bx-check me-1"></i> {{ __('admin.tickets.details.mark_solved') }}
                                </button>
                                <button class="btn btn-warning" type="button">
                                    <i class="bx bx-user me-1"></i> {{ __('admin.tickets.details.assign_ticket') }}
                                </button>
                            @else
                                <button class="btn btn-primary" type="button">
                                    <i class="bx bx-refresh me-1"></i> {{ __('admin.tickets.details.reopen_ticket') }}
                                </button>
                            @endif
                            <button class="btn btn-danger" type="button">
                                <i class="bx bx-trash me-1"></i> {{ __('admin.tickets.details.delete_ticket') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
