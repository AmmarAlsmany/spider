@extends('shared.dashboard')
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
    <!--breadcrumb-->
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-list-ul"></i> Equipment Types</h4>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Equipment Types List</h5>
                <a href="{{ route('equipment-types.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Add New Equipment Type
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipment_types as $type)
                            <tr>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->description }}</td>
                                <td>
                                    @if($type->trashed())
                                        <span class="badge bg-danger">Deleted</span>
                                    @else
                                        <span class="badge bg-{{ $type->is_active ? 'success' : 'warning' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->trashed())
                                        <form action="{{ route('equipment-types.restore', $type->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bx bx-refresh"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('equipment-types.edit', $type->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('equipment-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this equipment type?')">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
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
