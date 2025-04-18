@extends('shared.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('messages.pesticide_consumption_analytics') }}</div>

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

                    <!-- Filters -->
                    <form method="GET" action="{{ route('technical.pesticides.analytics') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date">{{ __('messages.start_date') }}</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date">{{ __('messages.end_date') }}</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="team_id">{{ __('messages.team') }}</label>
                                    <select class="form-control" id="team_id" name="team_id">
                                        <option value="">{{ __('messages.all_teams') }}</option>
                                        @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ $selectedTeam==$team->id ? 'selected' : ''
                                            }}>
                                            {{ $team->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="pesticide_id">{{ __('messages.pesticide') }}</label>
                                    <select class="form-control" id="pesticide_id" name="pesticide_id">
                                        <option value="">{{ __('messages.all_pesticides') }}</option>
                                        @foreach ($pesticides as $pesticide)
                                        <option value="{{ $pesticide->slug }}" {{ $selectedPesticide==$pesticide->slug ?
                                            'selected' : '' }}>
                                            {{ $pesticide->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="place_id">{{ __('messages.place') }}</label>
                                    <select class="form-control" id="place_id" name="place_id">
                                        <option value="">{{ __('messages.all_places') }}</option>
                                        @foreach ($places as $place)
                                        <option value="{{ $place->id }}" {{ $selectedPlace==$place->id ? 'selected' : ''
                                            }}>
                                            {{ $place->branch_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="mb-4 row">
                        <div class="col-md-3">
                            <div class="text-white card bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_consumption') }}</h5>
                                    <h3 class="mb-0">{{ $summaryStats['total_consumption'] }}
                                        {{ __('messages.units') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_visits') }}</h5>
                                    <h3 class="mb-0">{{ $summaryStats['total_visits'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.most_used_pesticide') }}</h5>
                                    <h3 class="mb-0">{{ $summaryStats['most_used_pesticide'] ?? 'N/A' }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white card bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.most_efficient_team') }}</h5>
                                    <h3 class="mb-0">{{ $summaryStats['most_efficient_team'] ?? 'N/A' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="mb-4 row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('messages.pesticide_usage_distribution') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <div id="pesticideDistributionChart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('messages.team_consumption_comparison') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <div id="teamConsumptionChart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Consumption Table -->
                    <h4>{{ __('messages.team_consumption') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.team') }}</th>
                                    <th>{{ __('messages.total_pesticide_used') }}</th>
                                    <th>{{ __('messages.visits_completed') }}</th>
                                    <th>{{ __('messages.pesticides_used') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consumptionData as $teamId => $teamData)
                                <tr>
                                    <td>{{ $teamData['team_name'] }}</td>
                                    <td>{{ $teamData['total_quantity'] }}</td>
                                    <td>{{ $teamData['visit_count'] }}</td>
                                    <td>
                                        @foreach ($teamData['pesticides'] as $pesticide => $pesticideData)
                                        <span class="d-block">
                                            {{ $pesticideData['name'] }}:
                                            @if ($pesticideData['unit_counts']['g'] > 0)
                                            {{ $pesticideData['unit_counts']['g'] }}g
                                            @endif
                                            @if ($pesticideData['unit_counts']['ml'] > 0)
                                            {{ $pesticideData['unit_counts']['g'] > 0 ? ' + ' : '' }}{{
                                            $pesticideData['unit_counts']['ml'] }}ml
                                            @endif
                                        </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('technical.pesticides.analytics.teamReport', ['teamId' => $teamId, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-chart-line"></i> {{ __('messages.detailed_report') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        {{ __('messages.no_consumption_data') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pesticide Usage Table -->
                    <h4 class="mt-4">{{ __('messages.pesticide_usage') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.pesticide') }}</th>
                                    <th>{{ __('messages.total_quantity') }}</th>
                                    <th>{{ __('messages.grams') }}</th>
                                    <th>{{ __('messages.milliliters') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summaryStats['pesticide_usage'] as $pesticideSlug => $pesticideData)
                                <tr>
                                    <td>{{ $pesticideData['name'] }}</td>
                                    <td>{{ $pesticideData['total_quantity'] }}</td>
                                    <td>{{ $pesticideData['unit_counts']['g'] }}</td>
                                    <td>{{ $pesticideData['unit_counts']['ml'] }}</td>
                                    <td>
                                        <a href="{{ route('technical.pesticides.analytics.pesticideReport', $pesticideSlug) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-chart-line"></i> {{ __('messages.detailed_report') }}
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        {{ __('messages.no_pesticide_usage_data') }}</td>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    canvas {
        max-width: 100%;
        max-height: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('backend/assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
            // Debug the summary stats data
            console.log('Summary Stats:', @json($summaryStats));

            // Initialize DataTables
            $('.datatable').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "{{ __('messages.search') }}:",
                    "lengthMenu": "{{ __('messages.show') }} _MENU_ {{ __('messages.entries') }}",
                    "info": "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_ {{ __('messages.entries') }}",
                    "paginate": {
                        "first": "{{ __('messages.first') }}",
                        "last": "{{ __('messages.last') }}",
                        "next": "{{ __('messages.next') }}",
                        "previous": "{{ __('messages.previous') }}"
                    }
                }
            });

            // Debug function to check if Chart.js is loaded
            console.log('Chart.js loaded:', typeof Chart !== 'undefined');

            // Prepare data for Pesticide Distribution Chart
            const pesticideLabels = [];
            const pesticideData = [];
            const pesticideColors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69',
                '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b', '#3a3b45'
            ];

            const pesticideSeries = [];

            @if (isset($summaryStats['pesticide_usage']) && count($summaryStats['pesticide_usage']) > 0)
                @foreach ($summaryStats['pesticide_usage'] as $slug => $data)
                    pesticideLabels.push("{{ $data['name'] }}");
                    pesticideData.push({{ $data['total_quantity'] }});
                    pesticideSeries.push({{ $data['total_quantity'] }});
                @endforeach
            @endif

            console.log("Pesticide Chart Data:", {
                labels: pesticideLabels,
                data: pesticideData,
                hasData: pesticideLabels.length > 0,
                rawData: @json($summaryStats['pesticide_usage'] ?? [])
            });

            // Create Pesticide Distribution Chart with ApexCharts
            const pesticideChartElement = document.getElementById('pesticideDistributionChart');
            if (pesticideChartElement) {
                try {
                    if (pesticideLabels.length > 0) {
                        // Remove canvas and create div for ApexCharts
                        const chartContainer = pesticideChartElement.parentNode;
                        chartContainer.innerHTML = '<div id="apexPesticideChart"></div>';
                        
                        const pesticideChartOptions = {
                            series: pesticideSeries,
                            chart: {
                                type: 'pie',
                                height: 300
                            },
                            labels: pesticideLabels,
                            colors: pesticideColors.slice(0, pesticideLabels.length),
                            legend: {
                                position: 'bottom'
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: '100%'
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }],
                            tooltip: {
                                y: {
                                    formatter: function(value) {
                                        return value + ' units';
                                    }
                                }
                            }
                        };
                        
                        const pesticideChart = new ApexCharts(document.getElementById('apexPesticideChart'), pesticideChartOptions);
                        pesticideChart.render();
                        console.log('Pesticide chart created successfully with ApexCharts');
                    } else {
                        console.log('No data available for pesticide chart');
                        pesticideChartElement.parentNode.innerHTML =
                            '<div class="alert alert-info">{{ __('messages.no_pesticide_usage_data_available') }}</div>';
                    }
                } catch (error) {
                    console.error('Error creating pesticide chart:', error);
                    pesticideChartElement.parentNode.innerHTML =
                        '<div class="alert alert-danger">Error creating chart: ' + error.message + '</div>';
                }
            } else {
                console.error('Pesticide chart element not found');
            }

            // Prepare data for Team Consumption Chart
            const teamLabels = [];
            const teamData = [];
            const teamColors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'
            ];

            @if (isset($summaryStats['team_usage']) && count($summaryStats['team_usage']) > 0)
                @foreach ($summaryStats['team_usage'] as $teamId => $data)
                    teamLabels.push("{{ $data['team_name'] }}");
                    teamData.push({{ $data['total_quantity'] }});
                @endforeach
            @endif

            console.log("Team Chart Data:", {
                labels: teamLabels,
                data: teamData,
                hasData: teamLabels.length > 0,
                rawData: @json($summaryStats['team_usage'] ?? [])
            });

            // Create Team Consumption Chart with ApexCharts
            const teamChartElement = document.getElementById('teamConsumptionChart');
            if (teamChartElement) {
                try {
                    if (teamLabels.length > 0) {
                        // Remove canvas and create div for ApexCharts
                        const chartContainer = teamChartElement.parentNode;
                        chartContainer.innerHTML = '<div id="apexTeamChart"></div>';
                        
                        const teamChartOptions = {
                            series: [{
                                name: '{{ __('messages.consumption_units') }}',
                                data: teamData
                            }],
                            chart: {
                                type: 'bar',
                                height: 300,
                                toolbar: {
                                    show: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded'
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            colors: teamColors,
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: teamLabels,
                            },
                            yaxis: {
                                title: {
                                    text: '{{ __('messages.consumption_units') }}'
                                },
                                labels: {
                                    formatter: function(val) {
                                        return val.toFixed(0);
                                    }
                                }
                            },
                            fill: {
                                opacity: 1
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + " units"
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        };
                        
                        const teamChart = new ApexCharts(document.getElementById('apexTeamChart'), teamChartOptions);
                        teamChart.render();
                        console.log('Team chart created successfully with ApexCharts');
                    } else {
                        console.log('No data available for team chart');
                        teamChartElement.parentNode.innerHTML =
                            '<div class="alert alert-info">{{ __('messages.no_team_usage_data_available') }}</div>';
                    }
                } catch (error) {
                    console.error('Error creating team chart:', error);
                    teamChartElement.parentNode.innerHTML =
                        '<div class="alert alert-danger">Error creating chart: ' + error.message + '</div>';
                }
            } else {
                console.error('Team chart element not found');
            }
        });
</script>
@endpush