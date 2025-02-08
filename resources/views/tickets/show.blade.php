@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">My Tickets</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('client.tikets') }}">My Tickets</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Ticket</li>
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
                            <a href="{{ route('client.tikets') }}" class="btn btn-secondary me-3">
                                <i class="bx bx-arrow-back"></i> Back
                            </a>
                            <h4 class="mb-0">Ticket Details</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Ticket Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Ticket Number:</th>
                                        <td>{{ $ticket->tiket_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Title:</th>
                                        <td>{{ $ticket->tiket_title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td>{{ $ticket->tiket_description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Priority:</th>
                                        <td>
                                            <span class="badge {{ $ticket->priority == 'high' ? 'bg-danger' : ($ticket->priority == 'medium' ? 'bg-warning' : 'bg-info') }}">
                                                {{ $ticket->priority }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge {{ $ticket->status == 'open' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $ticket->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Replies</h5>
                            <div class="card">
                                <div class="card-body">
                                    @if($ticket->replies && $ticket->replies->count() > 0)
                                        @foreach($ticket->replies as $reply)
                                            <div class="border-bottom mb-3 pb-3">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $reply->user->name }}</strong>
                                                    <small class="text-muted">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                                                </div>
                                                <p class="mb-0 mt-2">{{ $reply->reply }}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No replies yet</p>
                                    @endif

                                    @if($ticket->status != 'closed')
                                    <form action="{{ route('client.ticket.reply', $ticket->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="reply" class="form-label">Add Reply</label>
                                            <textarea class="form-control @error('reply') is-invalid @enderror" 
                                                id="reply" name="reply" rows="3" required></textarea>
                                            @error('reply')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">Send Reply</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
