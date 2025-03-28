<div class="content">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2>Team KPI Report: {{ $team->name }}</h2>
            <p>Period: {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center bg-light">Team Information</th>
                </tr>
                <tr>
                    <th width="40%">Team Name</th>
                    <td>{{ $team->name }}</td>
                </tr>
                <tr>
                    <th>Team Leader</th>
                    <td>{{ $team->leader->name }}</td>
                </tr>
                <tr>
                    <th>Team Members</th>
                    <td>
                        @if($team->members->count() > 0)
                            {{ $team->members->pluck('name')->implode(', ') }}
                        @else
                            No members assigned
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center bg-light">Performance Summary</th>
                </tr>
                <tr>
                    <th width="40%">Total Visits</th>
                    <td>{{ $kpiData['total_visits'] }}</td>
                </tr>
                <tr>
                    <th>Completed Visits</th>
                    <td>{{ $kpiData['completed_visits'] }}</td>
                </tr>
                <tr>
                    <th>Completion Rate</th>
                    <td>{{ $kpiData['completion_rate'] }}%</td>
                </tr>
                <tr>
                    <th>Reports Created</th>
                    <td>{{ $kpiData['reports_created'] }}</td>
                </tr>
                <tr>
                    <th>Report Submission Rate</th>
                    <td>{{ $kpiData['report_rate'] }}%</td>
                </tr>
                <tr>
                    <th>Average Visit Duration</th>
                    <td>{{ $kpiData['avg_visit_duration'] }} minutes</td>
                </tr>
                <tr>
                    <th>Average Report Submission Time</th>
                    <td>{{ $kpiData['avg_report_time'] }} hours</td>
                </tr>
                <tr>
                    <th>Customer Satisfaction</th>
                    <td>{{ $kpiData['avg_satisfaction'] }}/5</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-4">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center bg-light">Top Target Insects</th>
                </tr>
                @forelse($kpiData['top_insects'] as $insect => $count)
                    <tr>
                        <th width="70%">{{ $insect }}</th>
                        <td>{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No data available</td>
                    </tr>
                @endforelse
            </table>
        </div>
        <div class="col-6 mb-4">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center bg-light">Top Pesticides Used</th>
                </tr>
                @forelse($kpiData['top_pesticides'] as $pesticide => $count)
                    <tr>
                        <th width="70%">{{ $pesticide }}</th>
                        <td>{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No data available</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="text-center">
                <p>This report was generated on {{ now()->format('d M Y H:i') }}</p>
                <p>Spider Web For Pest Control</p>
            </div>
        </div>
    </div>
</div>
