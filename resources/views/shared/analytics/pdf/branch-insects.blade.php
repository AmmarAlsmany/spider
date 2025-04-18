{{-- PDF version of branch insect analytics - simplified for PDF output --}}

<div class="summary-stats">
    @php
        $totalVisits = count($visitData);
        $totalInsects = count($insectStats);
        $totalQuantity = 0;
        $totalReports = 0;

        foreach ($insectStats as $stat) {
            $totalReports += $stat['count'];
            $totalQuantity += $stat['quantity'] ?? $stat['count'];
        }

        $avgPerReport = $totalReports > 0 ? round($totalQuantity / $totalReports, 1) : 0;
    @endphp

    <div class="row">
        <div class="col-4">
            <div class="stats-card">
                <div class="stats-title">Insect Types Found</div>
                <div class="stats-value">{{ $totalInsects }}</div>
            </div>
        </div>

        <div class="col-4">
            <div class="stats-card">
                <div class="stats-title">Total Visits</div>
                <div class="stats-value">{{ $totalVisits }}</div>
            </div>
        </div>

        <div class="col-4">
            <div class="stats-card">
                <div class="stats-title">Total Insects Found</div>
                <div class="stats-value">{{ $totalQuantity }}</div>
                <div>{{ $avgPerReport }} avg. per report</div>
            </div>
        </div>
    </div>
</div>

<h3>Branch Details</h3>
<table>
    <tr>
        <th>Branch Name</th>
        <td>{{ $branch->branch_name }}</td>
        <th>Address</th>
        <td>{{ $branch->branch_address }}</td>
    </tr>
    <tr>
        <th>Contact Person</th>
        <td>{{ $branch->branch_manager_name }}</td>
        <th>Phone</th>
        <td>{{ $branch->branch_manager_phone }}</td>
    </tr>
</table>

{{-- Charts Section --}}
<div class="charts-section">
    <h3>Insect Analytics Charts</h3>

    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h4>Insect Occurrence Over Time</h4>
                {{-- The controller will replace this with an actual chart image --}}
                @if (isset($chartImages['occurrence']) && !empty($chartImages['occurrence']))
                    <img src="{{ $chartImages['occurrence'] }}" alt="Insect Occurrence Chart" class="chart-img" />
                @else
                    <div class="no-chart"
                        style="height: 150px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                        <div style="text-align: center; color: #666;">
                            <p>Chart not available in PDF export</p>
                            <p style="font-size: 11px; margin-top: 5px;">View the online dashboard for interactive
                                charts</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h4>Insect Quantities Over Time</h4>
                {{-- The controller will replace this with an actual chart image --}}
                @if (isset($chartImages['quantity']) && !empty($chartImages['quantity']))
                    <img src="{{ $chartImages['quantity'] }}" alt="Insect Quantities Chart" class="chart-img" />
                @else
                    <div class="no-chart"
                        style="height: 150px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                        <div style="text-align: center; color: #666;">
                            <p>Chart not available in PDF export</p>
                            <p style="font-size: 11px; margin-top: 5px;">View the online dashboard for interactive
                                charts</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <h4>Insect Distribution</h4>
                {{-- The controller will replace this with an actual chart image --}}
                @if (isset($chartImages['distribution']) && !empty($chartImages['distribution']))
                    <img src="{{ $chartImages['distribution'] }}" alt="Insect Distribution Chart" class="chart-img" />
                @else
                    <div class="no-chart"
                        style="height: 150px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                        <div style="text-align: center; color: #666;">
                            <p>Chart not available in PDF export</p>
                            <p style="font-size: 11px; margin-top: 5px;">View the online dashboard for interactive
                                charts</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<h3>Insect Prevalence Summary</h3>
<table>
    <thead>
        <tr>
            <th>Insect Type</th>
            <th>Occurrences</th>
            <th>Total Quantity</th>
            <th>% of Total</th>
            <th>Avg. per Visit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($insectStats as $stats)
            <tr>
                <td class="fw-bold">{{ $stats['name'] }}</td>
                <td class="text-center">{{ $stats['count'] }}</td>
                <td class="text-center">{{ $stats['quantity'] ?? $stats['count'] }}</td>
                <td class="text-center">
                    {{ round((($stats['quantity'] ?? $stats['count']) / $totalQuantity) * 100, 1) }}%
                </td>
                <td class="text-center">{{ round(($stats['quantity'] ?? $stats['count']) / max(1, $totalVisits), 1) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h3 class="mt-4">Visit History</h3>
<table>
    <thead>
        <tr>
            <th>Visit Date</th>
            <th>Technician</th>
            <th>Insects Found</th>
            <th>Total Quantity</th>
            <th>Treatment Methods</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($visitData as $visit)
            <tr>
                <td>{{ date('d M Y', strtotime($visit['visit_date'])) }}</td>
                <td>{{ $visit['technician'] ?? 'N/A' }}</td>
                <td>
                    @php
                        $insects = [];
                        foreach ($visit['target_insects'] as $insectValue) {
                            $insect = \App\Models\TargetInsect::where('value', $insectValue)->first();
                            $insectName = $insect ? $insect->name : $insectValue;
                            $quantity = $visit['insect_quantities'][$insectValue] ?? 1;
                            $insects[$insectName] = $quantity;
                        }
                    @endphp
                    @foreach ($insects as $insect => $quantity)
                        <div>{{ $insect }}: {{ $quantity }}</div>
                    @endforeach
                </td>
                <td class="text-center">{{ array_sum($insects) }}</td>
                <td>{{ $visit['recommendations'] ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
