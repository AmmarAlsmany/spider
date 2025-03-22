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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">{{ __('messages.start_date') }}</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">{{ __('messages.end_date') }}</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="team_id">{{ __('messages.team') }}</label>
                                    <select class="form-control" id="team_id" name="team_id">
                                        <option value="">{{ __('messages.all_teams') }}</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ $selectedTeam == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pesticide_id">{{ __('messages.pesticide') }}</label>
                                    <select class="form-control" id="pesticide_id" name="pesticide_id">
                                        <option value="">{{ __('messages.all_pesticides') }}</option>
                                        @foreach($pesticides as $pesticide)
                                            <option value="{{ $pesticide->slug }}" {{ $selectedPesticide == $pesticide->slug ? 'selected' : '' }}>
                                                {{ $pesticide->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                                </button>
                                <a href="{{ route('technical.pesticides.analytics') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> {{ __('messages.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="mb-4 row">
                        <div class="col-md-3">
                            <div class="text-white card bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_consumption') }}</h5>
                                    <h3 class="mb-0">{{ $summaryStats['total_consumption'] }} {{ __('messages.units') }}</h3>
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
                                        <canvas id="pesticideDistributionChart"></canvas>
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
                                        <canvas id="teamConsumptionChart"></canvas>
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
                                            @foreach($teamData['pesticides'] as $pesticide => $pesticideData)
                                                <span class="badge badge-primary">
                                                    {{ $pesticideData['name'] }}: 
                                                    @if($pesticideData['unit_counts']['g'] > 0)
                                                        {{ $pesticideData['unit_counts']['g'] }}g
                                                    @endif
                                                    @if($pesticideData['unit_counts']['ml'] > 0)
                                                        {{ $pesticideData['unit_counts']['g'] > 0 ? ' + ' : '' }}{{ $pesticideData['unit_counts']['ml'] }}ml
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
                                        <td colspan="5" class="text-center">{{ __('messages.no_consumption_data') }}</td>
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
                                        <td colspan="5" class="text-center">{{ __('messages.no_pesticide_usage_data') }}</td>
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

        // Prepare data for Pesticide Distribution Chart
        const pesticideLabels = [];
        const pesticideData = [];
        const pesticideColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69',
            '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b', '#3a3b45'
        ];

        @if(isset($summaryStats['pesticide_usage']) && count($summaryStats['pesticide_usage']) > 0)
            @foreach($summaryStats['pesticide_usage'] as $slug => $data)
                pesticideLabels.push("{{ $data['name'] }}");
                pesticideData.push({{ $data['total_quantity'] }});
            @endforeach
        @endif

        console.log("Pesticide Labels:", pesticideLabels);
        console.log("Pesticide Data:", pesticideData);

        // Create Pesticide Distribution Chart if canvas exists and we have data
        if (document.getElementById('pesticideDistributionChart') && pesticideLabels.length > 0) {
            console.log("Creating pesticide chart");
            const pesticideCtx = document.getElementById('pesticideDistributionChart').getContext('2d');
            
            new Chart(pesticideCtx, {
                type: 'pie',
                data: {
                    labels: pesticideLabels,
                    datasets: [{
                        data: pesticideData,
                        backgroundColor: pesticideColors.slice(0, pesticideData.length),
                        hoverBackgroundColor: pesticideColors.slice(0, pesticideData.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' units';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.log("Pesticide chart canvas not found or no data available");
            if (document.getElementById('pesticideDistributionChart')) {
                document.getElementById('pesticideDistributionChart').parentNode.innerHTML = 
                    '<div class="alert alert-info">{{ __("messages.no_pesticide_usage_data_available") }}</div>';
            }
        }

        // Prepare data for Team Consumption Chart
        const teamLabels = [];
        const teamData = [];
        const teamColors = [
            'rgba(78, 115, 223, 0.8)',
            'rgba(28, 200, 138, 0.8)',
            'rgba(54, 185, 204, 0.8)',
            'rgba(246, 194, 62, 0.8)',
            'rgba(231, 74, 59, 0.8)',
            'rgba(90, 92, 105, 0.8)'
        ];

        @if(isset($summaryStats['team_usage']) && count($summaryStats['team_usage']) > 0)
            @foreach($summaryStats['team_usage'] as $teamId => $data)
                teamLabels.push("{{ $data['team_name'] }}");
                teamData.push({{ $data['total_quantity'] }});
            @endforeach
        @endif

        console.log("Team Labels:", teamLabels);
        console.log("Team Data:", teamData);

        // Create Team Consumption Chart if canvas exists and we have data
        if (document.getElementById('teamConsumptionChart') && teamLabels.length > 0) {
            console.log("Creating team chart");
            const teamCtx = document.getElementById('teamConsumptionChart').getContext('2d');
            
            new Chart(teamCtx, {
                type: 'bar',
                data: {
                    labels: teamLabels,
                    datasets: [{
                        label: '{{ __("messages.consumption_units") }}',
                        data: teamData,
                        backgroundColor: teamColors.slice(0, teamLabels.length),
                        borderColor: teamColors.map(color => color.replace('0.8', '1')).slice(0, teamLabels.length),
                        borderWidth: 1
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
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        } else {
            console.log("Team chart canvas not found or no data available");
            if (document.getElementById('teamConsumptionChart')) {
                document.getElementById('teamConsumptionChart').parentNode.innerHTML = 
                    '<div class="alert alert-info">{{ __("messages.no_team_usage_data_available") }}</div>';
            }
        }
    });
</script>
@endsection
