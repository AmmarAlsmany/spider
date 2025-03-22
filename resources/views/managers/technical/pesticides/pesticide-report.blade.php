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
                                        <div class="col-md-3">
                                            <div class="alert alert-primary">
                                                <strong>{{ __('messages.total_usage') }}:</strong> {{ $totalUsage }} {{
                                                __('messages.units') }}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="alert alert-info">
                                                <strong>{{ __('messages.teams_using') }}:</strong> {{ count($teamUsage)
                                                }}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="alert alert-success">
                                                <strong>{{ __('messages.status') }}:</strong>
                                                @if($pesticide->active)
                                                <span class="badge badge-success">{{ __('messages.active') }}</span>
                                                @else
                                                <span class="badge badge-danger">{{ __('messages.inactive') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="alert alert-warning">
                                                <strong>{{ __('messages.category') }}:</strong>
                                                {{ $pesticide->category ?? __('messages.not_categorized') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="mb-4 row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('messages.usage_by_team') }}</h5>
                                </div>
                                <div class="card-body chart-container">
                                    <canvas id="teamUsageChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('messages.usage_over_time') }}</h5>
                                </div>
                                <div class="card-body chart-container">
                                    <canvas id="timelineChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Usage Breakdown -->
                    <h4>{{ __('messages.team_usage_breakdown') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.team') }}</th>
                                    <th>{{ __('messages.total_usage') }}</th>
                                    <th>{{ __('messages.usage_by_unit') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamUsage as $teamId => $data)
                                <tr>
                                    <td>{{ $data['team_name'] }}</td>
                                    <td>{{ $data['total_quantity'] }}</td>
                                    <td>
                                        @if(isset($data['unit_counts']))
                                        <span class="badge bg-primary">{{ $data['unit_counts']['g'] ?? 0 }} g</span>
                                        <span class="badge bg-info">{{ $data['unit_counts']['ml'] ?? 0 }} ml</span>
                                        @else
                                        {{ __('messages.not_available') }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('technical.pesticides.analytics.teamReport', $teamId) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-chart-line"></i> {{ __('messages.view_team_report') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('messages.no_team_usage_data_available') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Usage Timeline -->
                    <h4 class="mt-4">{{ __('messages.usage_timeline') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.team') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.unit') }}</th>
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
                                    <td>{{ $quantity }}</td>
                                    <td>{{ $unit }}</td>
                                    <td>
                                        <a href="{{ route('visit.report.view', $report->visit_id) }}"
                                            class="btn btn-sm btn-info">
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

                    <!-- Visit Reports -->
                    <h4 class="mt-4">{{ __('messages.visit_reports') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
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

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Prepare data for chart
        const timelineData = @json(collect($timelineData)->map(function($item) {
            return [
                'date' => $item['date'],
                'total' => $item['total']
            ];
        })->values());

        console.log("Timeline Data:", timelineData);

        // Create team usage chart
        const teamUsageData = @json(collect($teamUsage)->map(function($data) {
            return [
                'team' => $data['team_name'],
                'quantity' => $data['total_quantity']
            ];
        })->values());

        console.log("Team Usage Data:", teamUsageData);

        if (teamUsageData.length > 0) {
            const teamUsageCtx = document.getElementById('teamUsageChart');
            if (teamUsageCtx) {
                console.log("Creating team usage chart");
                new Chart(teamUsageCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: teamUsageData.map(item => item.team),
                        datasets: [{
                            label: '{{ __("messages.team_usage") }}',
                            data: teamUsageData.map(item => item.quantity),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                title: {
                                    display: true,
                                    text: '{{ __("messages.quantity") }}'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: '{{ __("messages.team") }}'
                                }
                            }
                        }
                    }
                });
            } else {
                console.error("Team usage chart canvas not found");
            }
        } else {
            $('#teamUsageChart').parent().html('<div class="alert alert-info">{{ __("messages.no_team_usage_data_available") }}</div>');
        }

        // Create timeline chart
        if (timelineData.length > 0) {
            const timelineCtx = document.getElementById('timelineChart');
            if (timelineCtx) {
                console.log("Creating timeline chart");
                new Chart(timelineCtx.getContext('2d'), {
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
                                ticks: {
                                    precision: 0
                                },
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
                console.error("Timeline chart canvas not found");
            }
        } else {
            $('#timelineChart').parent().html('<div class="alert alert-info">{{ __("messages.no_timeline_data_available") }}</div>');
        }

        // Initialize DataTables
        $('.datatable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/{{ app()->getLocale() }}.json"
            }
        });
    });
</script>
@endsection