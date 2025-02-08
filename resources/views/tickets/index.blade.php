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
    </div>
</div>
@endsection