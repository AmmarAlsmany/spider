@extends('shared.dashboard')

@section('title', 'Target Insects Management')

@push('styles')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .action-buttons .btn {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .action-buttons {
                display: flex;
                flex-direction: column;
            }

            .action-buttons .btn {
                margin-bottom: 5px;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        @if (session('success'))
            <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Target Insects Management</h5>
                <a href="{{ route('target-insects.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Add New Target Insect
                </a>
            </div>
            <div class="card-body">
                <!-- Search form -->
                <div class="mb-4">
                    <form action="{{ route('target-insects.index') }}" method="GET" class="row g-2">
                        <div class="col-md-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Search by name, value or description..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            @if (request('search'))
                                <a href="{{ route('target-insects.index') }}" class="btn btn-secondary w-100">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Value</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($targetInsects as $insect)
                                <tr>
                                    <td>{{ $insect->id }}</td>
                                    <td>{{ $insect->name }}</td>
                                    <td>{{ $insect->value }}</td>
                                    <td>{{ Str::limit($insect->description, 50) }}</td>
                                    <td>
                                        @if ($insect->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="action-buttons">
                                        <a href="{{ route('target-insects.edit', $insect->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('target-insects.destroy', $insect->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this target insect?')">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No target insects found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $targetInsects->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
@endsection
