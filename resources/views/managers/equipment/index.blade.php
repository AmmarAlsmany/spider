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
                <i class="bx bx-arrow-back"></i> {{ __('equipment_views.back') }}
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-list-ul"></i> {{ __('equipment_views.equipment_types') }}</h4>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('equipment_views.equipment_types_list') }}</h5>
                <a href="{{ route('equipment-types.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> {{ __('equipment_views.add_new_equipment_type') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('equipment_views.name') }}</th>
                            <th>{{ __('equipment_views.description') }}</th>
                            <th>{{ __('equipment_views.status') }}</th>
                            <th>{{ __('equipment_views.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipment_types as $type)
                            <tr>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->description }}</td>
                                <td>
                                    @if($type->trashed())
                                        <span class="badge bg-danger">{{ __('equipment_views.deleted') }}</span>
                                    @else
                                        <span class="badge bg-{{ $type->is_active ? 'success' : 'warning' }}">
                                            {{ $type->is_active ? __('equipment_views.active') : __('equipment_views.inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->trashed())
                                        <form action="{{ route('equipment-types.restore', $type->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bx bx-refresh"></i> {{ __('equipment_views.restore') }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('equipment-types.edit', $type->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bx bx-edit"></i> {{ __('equipment_views.edit') }}
                                        </a>
                                        <form action="{{ route('equipment-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('equipment_views.confirm_delete') }}')">
                                                <i class="bx bx-trash"></i> {{ __('equipment_views.delete') }}
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
