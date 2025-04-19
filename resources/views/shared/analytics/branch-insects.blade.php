@extends('shared.dashboard')

@section('title', 'Branch Insect Analytics')

@push('styles')
    <link href="{{ asset('backend/assets/plugins/apexcharts-bundle/css/apexcharts.css') }}" rel="stylesheet">
    <style>
        .analytics-card {
            transition: all 0.3s;
        }

        .analytics-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            min-height: 300px;
        }

        .top-insects-list .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .insect-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        .visit-timeline {
            position: relative;
            padding-left: 30px;
        }

        .visit-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }

        .visit-timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .visit-timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #3461ff;
        }

        .visit-date {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .insect-tag {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .tab-pane {
            padding-top: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Insect Analytics for Branch: {{ $branch->branch_name }}</h5>
                        <div>
                            <a href="{{ route('analytics.contract.branches', ['contractId' => $contract->id]) }}"
                                class="btn btn-sm btn-outline-primary me-2">
                                <i class="bx bx-building me-1"></i> All Branches
                            </a>
                            <a href="{{ route('analytics.branch.pdf', ['contractId' => $contract->id, 'branchId' => $branch->id]) }}"
                                class="btn btn-sm btn-outline-danger me-2" target="_blank">
                                <i class="bx bx-download me-1"></i> Download PDF
                            </a>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-arrow-back me-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Contract #:</strong> {{ $contract->id }}</p>
                                <p><strong>Customer:</strong> {{ $contract->customer->name }}</p>
                                <p><strong>Branch Manager:</strong> {{ $branch->branch_manager_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Branch Address:</strong> {{ $branch->branch_address }},
                                    {{ $branch->branch_city }}</p>
                                <p><strong>Contract Status:</strong> <span
                                        class="badge bg-{{ $contract->contract_status == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($contract->contract_status) }}</span>
                                </p>
                                <p><strong>Total Branch Visits:</strong> {{ count($visitData) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <div class="mt-4 row">
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

            <div class="col-12 col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-opacity-10 stats-icon bg-primary text-primary">
                                <i class="bx bx-bug fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Insect Types Found</h6>
                                <h3 class="mb-0">{{ $totalInsects }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-opacity-10 stats-icon bg-success text-success">
                                <i class="bx bx-calendar-check fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Total Branch Visits</h6>
                                <h3 class="mb-0">{{ $totalVisits }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-opacity-10 stats-icon bg-danger text-danger">
                                <i class="bx bx-bug-alt fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">Total Insects Found</h6>
                                <h3 class="mb-0">{{ $totalQuantity }}</h3>
                                <small class="text-muted">{{ $avgPerReport }} avg. per report</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="occurrences-tab" data-bs-toggle="tab"
                                    data-bs-target="#occurrences" type="button" role="tab" aria-controls="occurrences"
                                    aria-selected="true">Insect Occurrences</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="quantities-tab" data-bs-toggle="tab"
                                    data-bs-target="#quantities" type="button" role="tab" aria-controls="quantities"
                                    aria-selected="false">Insect Quantities</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="occurrences" role="tabpanel"
                                aria-labelledby="occurrences-tab">
                                <div class="chart-container">
                                    <div id="insectTrendsChart"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="quantities" role="tabpanel" aria-labelledby="quantities-tab">
                                <div class="chart-container">
                                    <div id="insectQuantitiesChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Top Insects Found</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group top-insects-list">
                            @forelse($insectStats as $index => $stat)
                                @if ($index < 10)
                                    <li class="list-group-item">
                                        <span>{{ $stat['name'] }}</span>
                                        <div>
                                            <span class="badge bg-primary insect-badge">{{ $stat['count'] }} reports</span>
                                            <span class="badge bg-info insect-badge">{{ $stat['quantity'] }} total</span>
                                        </div>
                                    </li>
                                @endif
                            @empty
                                <li class="list-group-item">No insects found in reports</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Insect Density Analysis</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div id="insectDensityChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Visit Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="visit-timeline">
                            @forelse($visitData as $visit)
                                <div class="visit-timeline-item">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="visit-date">
                                                <i class="bx bx-calendar me-1"></i>
                                                {{ date('d M Y', strtotime($visit['visit_date'])) }}
                                            </div>
                                            <h6>Insects Found:</h6>
                                            <div class="mb-3">
                                                @if (count($visit['target_insects']) > 0)
                                                    @foreach ($visit['target_insects'] as $insectValue)
                                                        @php
                                                            $insect = $targetInsects->firstWhere('value', $insectValue);
                                                            $insectName = $insect ? $insect->name : $insectValue;
                                                            $quantity = $visit['insect_quantities'][$insectValue] ?? 1;
                                                        @endphp
                                                        <span class="badge bg-light text-dark insect-tag">
                                                            {{ $insectName }} ({{ $quantity }})
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No insects reported</span>
                                                @endif
                                            </div>

                                            <h6>Recommendations:</h6>
                                            <p class="mb-0">
                                                {{ $visit['recommendations'] ?: 'No recommendations provided' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">No visit reports found for this branch.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize charts on page load
            initializeCharts();

            // Function to initialize charts
            function initializeCharts() {
                // Clear existing charts
                document.querySelector("#insectTrendsChart").innerHTML = '';
                document.querySelector("#insectQuantitiesChart").innerHTML = '';
                document.querySelector("#insectDensityChart").innerHTML = '';

                // Prepare data for insect trends chart
                const visitData = @json($visitData);
                const targetInsects = @json($targetInsects);
                const insectStats = @json($insectStats);

                console.log('Branch Visit Data:', visitData);
                console.log('Branch Target Insects:', targetInsects);
                console.log('Branch Insect Stats:', insectStats);

                // Sort insect stats by count (highest first)
                const sortedStats = [...insectStats].sort((a, b) => b.count - a.count);

                // Take only top 5 insects for readability
                const topInsects = sortedStats.slice(0, 5);

                // Create a map for insect names
                const insectNames = {};
                targetInsects.forEach(insect => {
                    insectNames[insect.value] = insect.name;
                });

                // Sort visits by date (oldest first)
                const sortedVisits = [...visitData].sort((a, b) =>
                    new Date(a.visit_date) - new Date(b.visit_date)
                );

                // Extract all unique dates for x-axis
                let visitDates = sortedVisits.map(visit => {
                    return new Date(visit.visit_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                });

                // Create dataset arrays
                let occurrenceDatasets = [];
                let quantityDatasets = [];

                // Colors for the datasets
                let colors = ['#FFCC00', '#3461ff', '#12bf24', '#ff6632', '#8932ff'];

                // Initialize data for top insects
                if (visitData.length > 0 && topInsects.length > 0) {
                    topInsects.forEach((insect, index) => {
                        const insectValue = insect.value;
                        const insectName = insect.name;
                        const color = colors[index % colors.length];

                        // Create occurrence data array (initialize with zeros)
                        const occurrenceData = Array(visitDates.length).fill(0);
                        const quantityData = Array(visitDates.length).fill(0);

                        // Fill in actual data from visits
                        sortedVisits.forEach((visit, vIndex) => {
                            const foundInsect = visit.target_insects.includes(insectValue);
                            if (foundInsect) {
                                occurrenceData[vIndex] = 1; // Mark as present

                                // Set quantity if available
                                if (visit.insect_quantities && visit.insect_quantities[
                                        insectValue]) {
                                    quantityData[vIndex] = parseInt(visit.insect_quantities[
                                        insectValue]);
                                } else {
                                    quantityData[vIndex] = 1; // Default to 1
                                }
                            }
                        });

                        // Create datasets for ApexCharts series
                        occurrenceDatasets.push({
                            name: insectName,
                            data: occurrenceData
                        });

                        quantityDatasets.push({
                            name: insectName,
                            data: quantityData
                        });
                    });
                }

                // Create ApexCharts charts if we have data
                if (occurrenceDatasets.length > 0) {
                    // Occurrence Chart (area chart with ApexCharts)
                    const occurrenceOptions = {
                        series: occurrenceDatasets,
                        chart: {
                            height: 350,
                            type: 'area',
                            toolbar: {
                                show: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        colors: colors,
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'light',
                                type: "vertical",
                                shadeIntensity: 0.4,
                                inverseColors: false,
                                opacityFrom: 0.9,
                                opacityTo: 0.6,
                                stops: [0, 100]
                            }
                        },
                        xaxis: {
                            categories: visitDates,
                            title: {
                                text: 'Visit Date',
                                style: {
                                    fontWeight: 'bold'
                                }
                            },
                            labels: {
                                rotate: -45,
                                rotateAlways: true
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Occurrence',
                                style: {
                                    fontWeight: 'bold'
                                }
                            },
                            min: 0,
                            max: 1.1,
                            labels: {
                                formatter: function(value) {
                                    return value === 1 ? 'Present' : 'Absent';
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(value) {
                                    return value === 1 ? 'Present' : 'Absent';
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        },
                        grid: {
                            borderColor: '#e0e0e0',
                            strokeDashArray: 5
                        },
                        title: {
                            text: 'Insect Occurrence Over Time',
                            align: 'center',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        markers: {
                            size: 5,
                            hover: {
                                size: 7
                            }
                        }
                    };

                    // Quantity Chart with ApexCharts (area chart)
                    const quantityOptions = {
                        series: quantityDatasets,
                        chart: {
                            height: 350,
                            type: 'area',
                            toolbar: {
                                show: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        colors: colors,
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'light',
                                type: "vertical",
                                shadeIntensity: 0.4,
                                inverseColors: false,
                                opacityFrom: 0.9,
                                opacityTo: 0.6,
                                stops: [0, 100]
                            }
                        },
                        xaxis: {
                            categories: visitDates,
                            title: {
                                text: 'Visit Date',
                                style: {
                                    fontWeight: 'bold'
                                }
                            },
                            labels: {
                                rotate: -45,
                                rotateAlways: true
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Number of Insects',
                                style: {
                                    fontWeight: 'bold'
                                }
                            },
                            min: 0
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(value) {
                                    return value + ' insects';
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        },
                        grid: {
                            borderColor: '#e0e0e0',
                            strokeDashArray: 5
                        },
                        title: {
                            text: 'Insect Quantities Over Time',
                            align: 'center',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        markers: {
                            size: 5,
                            hover: {
                                size: 7
                            }
                        }
                    };

                    // Initialize the tabs and charts
                    $('#occurrences-tab').on('shown.bs.tab', function(e) {
                        if (window.occurrenceChart) {
                            window.occurrenceChart.destroy();
                        }
                        // Render occurrence chart
                        window.occurrenceChart = new ApexCharts(document.querySelector(
                            "#insectTrendsChart"), occurrenceOptions);
                        window.occurrenceChart.render();
                    });

                    $('#quantities-tab').on('shown.bs.tab', function(e) {
                        if (window.quantityChart) {
                            window.quantityChart.destroy();
                        }
                        // Render quantity chart
                        window.quantityChart = new ApexCharts(document.querySelector(
                            "#insectQuantitiesChart"), quantityOptions);
                        window.quantityChart.render();
                    });

                    // Configure density chart with ApexCharts (spline area chart)
                    if (occurrenceDatasets.length > 0) {
                        const densityOptions = {
                            series: quantityDatasets,
                            chart: {
                                height: 350,
                                type: 'area',
                                toolbar: {
                                    show: true
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 3
                            },
                            colors: colors,
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'light',
                                    type: "vertical",
                                    shadeIntensity: 0.4,
                                    inverseColors: false,
                                    opacityFrom: 0.9,
                                    opacityTo: 0.6,
                                    stops: [0, 100]
                                }
                            },
                            xaxis: {
                                categories: visitDates,
                                title: {
                                    text: 'Visit Date',
                                    style: {
                                        fontWeight: 'bold'
                                    }
                                },
                                labels: {
                                    rotate: -45,
                                    rotateAlways: true
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Number of Insects',
                                    style: {
                                        fontWeight: 'bold'
                                    }
                                },
                                min: 0
                            },
                            tooltip: {
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function(value) {
                                        return value + ' insects';
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            },
                            grid: {
                                borderColor: '#e0e0e0',
                                strokeDashArray: 5
                            },
                            title: {
                                text: 'Insect Density Over Time',
                                align: 'center',
                                style: {
                                    fontSize: '16px',
                                    fontWeight: 'bold'
                                }
                            },
                            markers: {
                                size: 5,
                                hover: {
                                    size: 7
                                }
                            }
                        };

                        // Initialize density chart
                        if (window.densityChart) {
                            window.densityChart.destroy();
                        }
                        window.densityChart = new ApexCharts(document.querySelector("#insectDensityChart"),
                            densityOptions);
                        window.densityChart.render();
                    }
                } else {
                    // Display message if no data
                    document.querySelector("#insectTrendsChart").innerHTML =
                        '<div class="alert alert-info">No visit data available for this branch.</div>';
                    document.querySelector("#insectQuantitiesChart").innerHTML =
                        '<div class="alert alert-info">No visit data available for this branch.</div>';
                    document.querySelector("#insectDensityChart").innerHTML =
                        '<div class="alert alert-info">No visit data available for this branch.</div>';
                }
            }
        });
    </script>
@endpush
