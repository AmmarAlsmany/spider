@extends('shared.dashboard')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8Mt9yKMkOnHeDpPvPvJR1j+MPY+2h24HFbaQrNfBIhssLPD3LC3B9FuQzjS9gQ2tRul6OzKneid7/7InUg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        padding: 4px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        border: none;
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }

    .select2-container--bootstrap-5 .select2-selection--single {
        height: 38px;
        padding: 5px 12px;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
        padding-left: 0;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    /* Make alerts more visible */
    .alert {
        position: relative;
        z-index: 1000;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
</style>
@endpush

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

    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Manage Teams</h6>
                </div>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createTeamModal">
                        Create New Team
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-md-6">
                    <form action="{{ route('teams.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2"
                            placeholder="Search by team name or leader" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))
                        <a href="{{ route('teams.index') }}" class="btn btn-secondary ms-2">Clear</a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Team Name</th>
                            <th>Team Leader</th>
                            <th>Members</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                            <tr>
                                <td>{{ $team->name }}</td>
                                <td>{{ $team->leader->name }}</td>
                                <td>
                                    <ul class="mb-0 list-unstyled">
                                        @foreach($team->members as $member)
                                            <li>{{ $member->name }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editTeamModal{{ $team->id }}">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#removeTeamModal{{ $team->id }}">
                                        <i class="fas fa-trash me-1"></i>Remove
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Team Modal -->
                            <div class="modal fade" id="editTeamModal{{ $team->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Team</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('teams.modify', $team->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Team Name</label>
                                                    <input type="text" class="form-control" name="name" 
                                                           value="{{ $team->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Team Leader</label>
                                                    <select class="form-select select2-single" name="team_leader_id" 
                                                            required data-placeholder="Select Team Leader"
                                                            {{ $team->visitSchedules()->where('status', 'scheduled')->exists() ? 'disabled' : '' }}>
                                                        <option value="{{ $team->team_leader_id }}" selected>{{ $team->leader->name }}</option>
                                                        @php
                                                        // Get available leaders (not assigned to any team except current)
                                                        $leaders = \App\Models\User::where('role', 'team_leader')
                                                            ->where('status', 'active')
                                                            ->where('id', '!=', $team->team_leader_id)  // Exclude current leader since already added
                                                            ->whereNotExists(function($query) use ($team) {
                                                                $query->from('teams')
                                                                    ->where('teams.id', '!=', $team->id)  // Exclude current team
                                                                    ->whereColumn('teams.team_leader_id', 'users.id');
                                                            })
                                                            ->whereNotExists(function($query) use ($team) {
                                                                $query->from('team_members')
                                                                    ->where('team_members.team_id', '!=', $team->id)  // Exclude current team
                                                                    ->whereColumn('team_members.user_id', 'users.id');
                                                            })
                                                            ->orderBy('name')
                                                            ->get();
                                                        @endphp
                                                        @foreach($leaders as $leader)
                                                            <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Team Members</label>
                                                    <select class="form-select select2-multiple" name="members[]" 
                                                            multiple data-placeholder="Select Members"
                                                            {{ $team->visitSchedules()->where('status', 'scheduled')->exists() ? 'disabled' : '' }}>
                                                        @php
                                                        // For edit modal - show current members plus available workers
                                                        $workers = \App\Models\User::where('role', 'worker')
                                                            ->where('status', 'active')
                                                            ->where(function($query) use ($team) {
                                                                // Always include current team members
                                                                $query->whereIn('id', $team->members->pluck('id'))
                                                                    // Or include available workers who are not in any team and not leaders
                                                                    ->orWhere(function($q) {
                                                                        // Not a member of any other team
                                                                        $q->whereNotExists(function($sub) {
                                                                            $sub->from('team_members')
                                                                                ->whereColumn('team_members.user_id', 'users.id');
                                                                        })
                                                                        // Not a leader of any team
                                                                        ->whereNotExists(function($sub) {
                                                                            $sub->from('teams')
                                                                                ->whereColumn('teams.team_leader_id', 'users.id');
                                                                        });
                                                                    });
                                                            })
                                                            ->orderBy('name')
                                                            ->get();
                                                        @endphp
                                                        @foreach($workers as $worker)
                                                            <option value="{{ $worker->id }}" 
                                                                {{ $team->members->contains($worker->id) ? 'selected' : '' }}>
                                                                {{ $worker->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" rows="3">{{ $team->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Team</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Remove Team Modal -->
                            <div class="modal fade" id="removeTeamModal{{ $team->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Remove Team</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to remove {{ $team->name }}?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('teams.delete', $team->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Remove Team</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center">
                                    <div class="text-muted">
                                        <i class="mb-2 fas fa-users" style="font-size: 2rem;"></i>
                                        <p class="mb-0">No teams found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Team Modal -->
<div class="modal fade" id="createTeamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('teams.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Team Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Leader</label>
                        <select class="form-select select2-single" name="team_leader_id" required
                            data-placeholder="Select Team Leader">
                            @php
                            $leaders = \App\Models\User::where('role', 'team_leader')
                                ->where('status', 'active')
                                // Not assigned as leader to any team
                                ->whereNotExists(function($query) {
                                    $query->from('teams')
                                        ->whereColumn('teams.team_leader_id', 'users.id');
                                })
                                // Not a member of any team (exclusive relationships)
                                ->whereNotExists(function($query) {
                                    $query->from('team_members')
                                        ->whereColumn('team_members.user_id', 'users.id');
                                })
                                ->orderBy('name')
                                ->get();
                            @endphp
                            @foreach($leaders as $leader)
                            <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Team Members</label>
                        <select class="form-select select2-multiple" name="members[]" multiple
                            data-placeholder="Select Members">
                            @php
                            // For create modal - show only available workers (not in any team and not team leaders)
                            $workers = \App\Models\User::where('role', 'worker')
                                ->where('status', 'active')
                                // Not a member of any team
                                ->whereNotExists(function($query) {
                                    $query->from('team_members')
                                        ->whereColumn('team_members.user_id', 'users.id');
                                })
                                // Not a leader of any team
                                ->whereNotExists(function($query) {
                                    $query->from('teams')
                                        ->whereColumn('teams.team_leader_id', 'users.id');
                                })
                                ->orderBy('name')
                                ->get();
                            @endphp
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Team</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-vKMx8Mt9yKMkOnHeDpPvPvJR1j+MPY+2h24HFbaQrNfBIhssLPD3LC3B9FuQzjS9gQ2tRul6OzKneid7/7InUg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            theme: 'bootstrap-5',
            width: '100%',
            closeOnSelect: false,
            allowClear: true
        });

        $('.select2-single').select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            placeholder: 'Select Team Leader'
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    });
</script>
<script type="text/javascript">
    @if(session('error'))
        toastr.error("{{ session('error') }}")
    @endif
    
    @if(session('success'))
        toastr.success("{{ session('success') }}")
    @endif
</script>
@endpush
@endsection