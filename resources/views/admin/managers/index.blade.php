@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-0 text-gray-800">Managers</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i> Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Managers</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.managers.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bx bx-plus-circle me-1"></i> Add Manager
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($managers as $manager)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-light rounded-circle">
                                        <span class="avatar-text">{{ substr($manager->name, 0, 1) }}</span>
                                    </div>
                                    {{ $manager->name }}
                                </div>
                            </td>
                            <td>{{ $manager->email }}</td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $manager->role)) }}</span></td>
                            <td>
                                @if($manager->status === 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.managers.edit', $manager) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.managers.destroy', $manager) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this manager?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection