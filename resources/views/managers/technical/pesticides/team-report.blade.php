@extends('shared.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('messages.team_pesticide_report') }}: {{ $team->name }}</span>
                        <a href="{{ route('technical.pesticides.analytics', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_analytics') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Date Range Filter -->
                    <form method="GET" action="{{ route('technical.pesticides.analytics.teamReport', $team->id) }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">{{ __('messages.start_date') }}</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">{{ __('messages.end_date') }}</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Team Summary -->
                    <div class="mb-4 row">
                        <div class="col-md-4">
                            <div class="text-white card bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_pesticide_used') }}</h5>
                                    <p class="card-text display-4">{{ $teamData['total_quantity'] }}</p>
                                    <p class="card-text">{{ __('messages.in_selected_period') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-white card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.visits_completed') }}</h5>
                                    <p class="card-text display-4">{{ $teamData['visit_count'] }}</p>
                                    <p class="card-text">{{ __('messages.in_selected_period') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-white card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.average_per_visit') }}</h5>
                                    <p class="card-text display-4">
                                        {{ $teamData['visit_count'] > 0 ? number_format($teamData['total_quantity'] / $teamData['visit_count'], 2) : 0 }}
                                    </p>
                                    <p class="card-text">{{ __('messages.units_per_visit') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pesticide Breakdown -->
                    <h4>{{ __('messages.pesticide_breakdown') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.pesticide') }}</th>
                                    <th>{{ __('messages.total_quantity') }}</th>
                                    <th>{{ __('messages.grams') }}</th>
                                    <th>{{ __('messages.milliliters') }}</th>
                                    <th>{{ __('messages.percentage_of_total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamData['pesticides'] as $pesticideSlug => $pesticideData)
                                    <tr>
                                        <td>{{ $pesticideData['name'] }}</td>
                                        <td>{{ $pesticideData['total_quantity'] }}</td>
                                        <td>{{ $pesticideData['unit_counts']['g'] }}</td>
                                        <td>{{ $pesticideData['unit_counts']['ml'] }}</td>
                                        <td>
                                            {{ $teamData['total_quantity'] > 0 ? number_format(($pesticideData['total_quantity'] / $teamData['total_quantity']) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('messages.no_pesticide_usage_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Visit Reports -->
                    <h4 class="mt-4">{{ __('messages.visit_reports') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.visit_type') }}</th>
                                    <th>{{ __('messages.created_by') }}</th>
                                    <th>{{ __('messages.pesticides_used') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($report->visit->visit_date)->format('Y-m-d') }}</td>
                                        <td>{{ ucfirst($report->visit_type) }}</td>
                                        <td>{{ $report->createdBy->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $pesticidesUsed = json_decode($report->pesticides_used, true) ?? [];
                                                $pesticideQuantities = json_decode($report->pesticide_quantities, true) ?? [];
                                            @endphp
                                            
                                            @foreach($pesticidesUsed as $pesticide)
                                                @php
                                                    $quantity = $pesticideQuantities[$pesticide]['quantity'] ?? 0;
                                                    $unit = $pesticideQuantities[$pesticide]['unit'] ?? 'g';
                                                @endphp
                                                <span class="badge badge-primary">
                                                    {{ $pesticide }}: {{ $quantity }}{{ $unit }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('technical.visit.report.view', $report->visit_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> {{ __('messages.view_report') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('messages.no_reports_found') }}</td>
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
@endsection
