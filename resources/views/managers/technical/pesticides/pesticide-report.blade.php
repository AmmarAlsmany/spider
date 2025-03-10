@extends('shared.dashboard')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('messages.pesticide_usage_report') }}: {{ $pesticide->name }}</span>
                        <a href="{{ route('technical.pesticides.analytics', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="btn btn-sm btn-secondary">
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
                    <form method="GET"
                        action="{{ route('technical.pesticides.analytics.pesticideReport', $pesticide->slug) }}"
                        class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">{{ __('messages.start_date') }}</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">{{ __('messages.end_date') }}</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $endDate }}">
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

                    <!-- Pesticide Info -->
                    <div class="mb-4 row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $pesticide->name }}</h5>
                                    <p class="card-text">{{ $pesticide->description }}</p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="alert alert-primary">
                                                <strong>{{ __('messages.total_usage') }}:</strong> {{ $totalUsage }} {{
                                                __('messages.units') }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-info">
                                                <strong>{{ __('messages.teams_using') }}:</strong> {{ count($teamUsage)
                                                }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-success">
                                                <strong>{{ __('messages.status') }}:</strong>
                                                @if($pesticide->active)
                                                <span class="badge badge-success">{{ __('messages.active') }}</span>
                                                @else
                                                <span class="badge badge-danger">{{ __('messages.inactive') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Usage Breakdown -->
                    <h4>{{ __('messages.team_usage_breakdown') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.team') }}</th>
                                    <th>{{ __('messages.total_quantity') }}</th>
                                    <th>{{ __('messages.grams') }}</th>
                                    <th>{{ __('messages.milliliters') }}</th>
                                    <th>{{ __('messages.percentage_of_total') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamUsage as $teamId => $data)
                                <tr>
                                    <td>{{ $data['team_name'] }}</td>
                                    <td>{{ $data['total_quantity'] }}</td>
                                    <td>{{ $data['unit_counts']['g'] }}</td>
                                    <td>{{ $data['unit_counts']['ml'] }}</td>
                                    <td>
                                        {{ $totalUsage > 0 ? number_format(($data['total_quantity'] / $totalUsage) *
                                        100, 1) : 0 }}%
                                    </td>
                                    <td>
                                        <a href="{{ route('technical.pesticides.analytics.teamReport', ['teamId' => $teamId, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-users"></i> {{ __('messages.view_team_report') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_team_usage_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Usage Timeline -->
                    <h4 class="mt-4">{{ __('messages.usage_timeline') }}</h4>
                    <div class="chart-container" style="position: relative; height:300px; width:100%">
                        <canvas id="usageChart"></canvas>
                    </div>

                    <!-- Visit Reports -->
                    <h4 class="mt-4">{{ __('messages.visit_reports') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.team') }}</th>
                                    <th>{{ __('messages.visit_type') }}</th>
                                    <th>{{ __('messages.created_by') }}</th>
                                    <th>{{ __('messages.quantity_used') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                @php
                                $pesticidesUsed = json_decode($report->pesticides_used, true) ?? [];
                                $pesticideQuantities = json_decode($report->pesticide_quantities, true) ?? [];

                                if (!in_array($pesticide->slug, $pesticidesUsed)) {
                                    continue;
                                }

                                $quantity = $pesticideQuantities[$pesticide->slug]['quantity'] ?? 0;
                                $unit = $pesticideQuantities[$pesticide->slug]['unit'] ?? 'g';
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->visit->visit_date)->format('Y-m-d') }}</td>
                                    <td>{{ $report->visit->team->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($report->visit_type) }}</td>
                                    <td>{{ $report->createdBy->name ?? 'N/A' }}</td>
                                    <td>{{ $quantity }}{{ $unit }}</td>
                                    <td>
                                        <a href="{{ route('visit.report.view', $report->visit_id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('messages.view_report') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('messages.no_reports_found') }}</td>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Prepare data for chart
        @php
        $chartData = collect($teamUsage)->flatMap(function($team) {
            return collect($team['dates'] ?? [])->map(function($quantity, $date) use ($team) {
                return [
                    'date' => $date,
                    'team' => $team['team_name'],
                    'quantity' => (float) $quantity
                ];
            });
        })->groupBy('date')->map(function($group) {
            return [
                'date' => $group->first()['date'],
                'total' => (float) $group->sum('quantity')
            ];
        })->sortBy('date')->values();
        @endphp
        const timelineData = @json($chartData);

        // Create chart
        if (timelineData.length > 0) {
            const ctx = document.getElementById('usageChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: timelineData.map(item => item.date),
                    datasets: [{
                        label: '{{ $pesticide->name }} {{ __("messages.usage") }}',
                        data: timelineData.map(item => item.total),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __("messages.quantity") }}'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: '{{ __("messages.date") }}'
                            }
                        }
                    }
                }
            });
        } else {
            $('#usageChart').parent().html('<div class="alert alert-info">{{ __("messages.no_timeline_data_available") }}</div>');
        }
    });
</script>
@endsection