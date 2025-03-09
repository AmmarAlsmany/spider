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

                    <!-- Summary Statistics -->
                    <div class="mb-4 row">
                        <div class="col-md-3">
                            <div class="text-white card bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_consumption') }}</h5>
                                    <p class="card-text display-4">{{ $summaryStats['total_consumption'] }}</p>
                                    <p class="card-text">{{ __('messages.across_all_teams') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white card bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_visits') }}</h5>
                                    <p class="card-text display-4">{{ $summaryStats['total_visits'] }}</p>
                                    <p class="card-text">{{ __('messages.in_selected_period') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white card bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.most_used_pesticide') }}</h5>
                                    <p class="card-text h4">{{ $summaryStats['most_used_pesticide'] ?? __('messages.none') }}</p>
                                    <p class="card-text">{{ $summaryStats['most_used_quantity'] ?? 0 }} {{ __('messages.units') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.most_efficient_team') }}</h5>
                                    <p class="card-text h4">{{ $summaryStats['most_efficient_team'] ?? __('messages.none') }}</p>
                                    <p class="card-text">{{ __('messages.based_on_visits_per_unit') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Consumption Table -->
                    <h4>{{ __('messages.team_consumption') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                        <table class="table table-bordered table-striped">
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

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize date pickers if needed
    });
</script>
@endsection
