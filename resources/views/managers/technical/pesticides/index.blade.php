@extends('shared.dashboard')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('messages.pesticide_management') }}</h5>
                        <div>
                            <a href="{{ route('technical.pesticides.export') }}" class="btn btn-success btn-sm me-2">
                                <i class="fas fa-file-export me-1"></i> Export to CSV
                            </a>
                            <a href="{{ route('technical.pesticides.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle me-1"></i> {{ __('messages.add_new') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Search and Filter -->
                        <div class="mb-4 row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" placeholder="Search pesticides...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="insecticide">Insecticide</option>
                                    <option value="rodenticide">Rodenticide</option>
                                    <option value="fungicide">Fungicide</option>
                                    <option value="herbicide">Herbicide</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pesticides as $pesticide)
                                    <tr>
                                        <td>{{ $pesticide->name }}</td>
                                        <td>{{ ucfirst($pesticide->category) ?? 'N/A' }}</td>
                                        <td>{{ $pesticide->description ?? 'N/A' }}</td>
                                        <td>
                                            @if($pesticide->current_stock <= $pesticide->min_stock_threshold)
                                                <span class="badge bg-danger">Low: {{ $pesticide->current_stock }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $pesticide->current_stock }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $pesticide->active ? 'success' : 'danger' }}">
                                                {{ $pesticide->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('technical.pesticides.edit', $pesticide->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                                </a>
                                                <form action="{{ route('technical.pesticides.destroy', $pesticide->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __('messages.confirm_delete_pesticide') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No pesticides found</td>
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

@section('scripts')
<script>
    $(document).ready(function() {
        // Search functionality
        $('#searchBtn').on('click', function() {
            performSearch();
        });

        $('#search').on('keypress', function(e) {
            if (e.which === 13) {
                performSearch();
            }
        });

        // Category filter
        $('#categoryFilter').on('change', function() {
            performSearch();
        });

        function performSearch() {
            const searchTerm = $('#search').val().toLowerCase();
            const category = $('#categoryFilter').val().toLowerCase();
            
            $('table tbody tr').each(function() {
                const name = $(this).find('td:nth-child(1)').text().toLowerCase();
                const rowCategory = $(this).find('td:nth-child(2)').text().toLowerCase();
                const description = $(this).find('td:nth-child(3)').text().toLowerCase();
                
                const nameMatch = name.includes(searchTerm);
                const descriptionMatch = description.includes(searchTerm);
                const categoryMatch = category === '' || rowCategory.includes(category);
                
                if ((nameMatch || descriptionMatch) && categoryMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            // Show "no results" message if all rows are hidden
            const visibleRows = $('table tbody tr:visible').length;
            if (visibleRows === 0 && $('table tbody tr').length > 0) {
                if ($('#no-results-row').length === 0) {
                    $('table tbody').append('<tr id="no-results-row"><td colspan="6" class="text-center">No results found</td></tr>');
                }
            } else {
                $('#no-results-row').remove();
            }
        }
    });
</script>
@endsection