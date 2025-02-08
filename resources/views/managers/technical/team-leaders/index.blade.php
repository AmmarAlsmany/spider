@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Manage Team Leaders</h6>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeamLeaderModal">
                        Add New Team Leader
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="mb-3 row">
                <div class="col-md-6">
                    <form action="{{ route('team-leaders.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by name, email or phone" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))
                            <a href="{{ route('team-leaders.index') }}" class="btn btn-secondary ms-2">Clear</a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamLeaders as $leader)
                        <tr>
                            <td>{{ $leader->name }}</td>
                            <td>{{ $leader->email }}</td>
                            <td>{{ $leader->phone }}</td>
                            <td>
                                <span class="badge bg-{{ $leader->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($leader->status) }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#editTeamLeaderModal{{ $leader->id }}">
                                    Edit
                                </button>
                                @if($leader->status === 'active')
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                        data-bs-target="#removeTeamLeaderModal{{ $leader->id }}">
                                        Remove
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Edit Team Leader Modal -->
                        <div class="modal fade" id="editTeamLeaderModal{{ $leader->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Team Leader</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('team-leaders.update', $leader->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ $leader->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ $leader->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control" name="phone" value="{{ $leader->phone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <textarea class="form-control" name="address">{{ $leader->address }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">New Password (leave blank to keep current)</label>
                                                <input type="password" class="form-control" name="password">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="active" {{ $leader->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $leader->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update Team Leader</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Remove Team Leader Modal -->
                        <div class="modal fade" id="removeTeamLeaderModal{{ $leader->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Remove Team Leader</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to remove {{ $leader->name }}?</p>
                                        <p class="text-danger">
                                            <strong>Warning:</strong> This action cannot be undone if the team leader:
                                            <ul>
                                                <li>Is assigned to an active team</li>
                                                <li>Has active workers under their supervision</li>
                                            </ul>
                                            Please ensure all team responsibilities are transferred before removing the team leader.
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('team-leaders.delete', $leader->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Remove Team Leader</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Team Leader Modal -->
    <div class="modal fade" id="createTeamLeaderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Team Leader</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('team-leaders.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Team Leader</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection