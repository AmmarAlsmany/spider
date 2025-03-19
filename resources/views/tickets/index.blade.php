@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Sales profile</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Client Tickets</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                                <i class="bx bx-arrow-back"></i> Back
                            </a>
                            <h4 class="mb-0">Client Tickets</h4>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('sales.tikets') }}" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control" placeholder="Search tickets..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary ms-2">Search</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ticket Number</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Client</th>
                                        <th>Phone Number</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Solved By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->tiket_number }}</td>
                                        <td>{{ $ticket->tiket_title }}</td>
                                        <td>{{ $ticket->tiket_description }}</td>
                                        <td><a href="{{ route('view.my.clients.details', ['id' => $ticket->customer->id]) }}"
                                                class="text-decoration-none fw-semibold text-primary">{{
                                                $ticket->customer->name }}</a></td>
                                        <td class="text-muted fw-semibold">{{ $ticket->customer->phone }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $ticket->priority == 'high' ? 'bg-danger' : ($ticket->priority == 'medium' ? 'bg-warning' : 'bg-info') }}">
                                                {{ $ticket->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $ticket->status == 'open' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $ticket->status }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $ticket->solver ? $ticket->solver->name : 'Not solved yet' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button" class="btn btn-info btn-sm me-1" data-bs-toggle="modal"
                                                data-bs-target="#viewReplies{{ $ticket->id }}">
                                                <i class="bx bx-message-square-detail"></i> View Replies
                                            </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No tickets found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewReplies{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ticket #{{ $ticket->tiket_number }} - Conversation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4 ticket-details">
                            <h6>Original Ticket</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $ticket->tiket_title }}</h6>
                                    <p class="card-text">{{ $ticket->tiket_description }}</p>
                                    <small class="text-muted">Created by {{ $ticket->customer->name }} -
                                        {{ $ticket->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="replies-section">
                            <h6>Replies</h6>
                            @if($ticket->replies && $ticket->replies->count() > 0)
                            @foreach($ticket->replies as $reply)
                            <div
                                class="card mb-2 {{ $reply->user_id == $ticket->customer_id ? 'bg-light' : 'bg-info-subtle' }}">
                                <div class="card-body">
                                    <p class="card-text">{{ $reply->reply }}</p>
                                    <small class="text-muted">
                                        By {{ $reply->user_id == $ticket->customer_id ?
                                        $ticket->customer->name : 'Support Team' }}
                                        - {{ $reply->created_at->format('Y-m-d H:i') }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <p class="text-muted">No replies yet.</p>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all modals with proper configuration
        var modalElements = document.querySelectorAll('.modal');
        modalElements.forEach(function(modalElement) {
            new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        });
    });
</script>
@endpush