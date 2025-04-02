@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Sales profile</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">Client Tickets</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">Client Tickets</h5>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <form action="{{ route('technical.client_tickets') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by ticket number, title or client name" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="open" {{ request('status')=='open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>In
                                            Progress</option>
                                        <option value="resolved" {{ request('status')=='resolved' ? 'selected' : '' }}>Resolved
                                        </option>
                                        <option value="closed" {{ request('status')=='closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="priority" class="form-select">
                                        <option value="">All Priority</option>
                                        <option value="high" {{ request('priority')=='high' ? 'selected' : '' }}>High</option>
                                        <option value="medium" {{ request('priority')=='medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="low" {{ request('priority')=='low' ? 'selected' : '' }}>Low</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-search"></i> Search
                                    </button>
                                    @if(request()->hasAny(['search', 'status', 'priority']))
                                    <a href="{{ route('technical.client_tickets') }}" class="btn btn-secondary">
                                        <i class="bx bx-reset"></i> Reset
                                    </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Client</th>
                                        <th>Client Mobile</th>
                                        <th>Contract #</th>
                                        <th>Branch</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->tiket_number }}</td>
                                        <td>{{ $ticket->customer->name }}</td>
                                        <td>{{ $ticket->customer->mobile }}</td>
                                        <td>{{ $ticket->customer->contracts->first()->contract_number ?? 'N/A' }}</td>
                                        <td>{{ $ticket->is_branch ?? 'Main Contract' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $ticket->tiket_title }}</h6>
                                                    <p class="mb-0 text-muted small">{{ Str::limit($ticket->tiket_description, 50)
                                                        }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'in_progress' ? 'warning' : ($ticket->status == 'resolved' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm me-1" data-bs-toggle="modal"
                                                data-bs-target="#viewReplies{{ $ticket->id }}">
                                                <i class="bx bx-message-square-detail"></i> View Replies
                                            </button>
                                        </td>
                                    </tr>
            
                                    <!-- View Replies Modal -->
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
                                    @empty
                                    <tr>
                                        <td colspan="8" class="py-4 text-center">No tickets found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(method_exists($tickets, 'links'))
                        <div class="mt-4">
                            {{ $tickets->links('vendor.pagination.custom') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
