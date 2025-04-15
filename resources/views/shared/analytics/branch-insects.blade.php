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
                                    <canvas id="insectTrendsChart"></canvas>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="quantities" role="tabpanel" aria-labelledby="quantities-tab">
                                <div class="chart-container">
                                    <canvas id="insectQuantitiesChart"></canvas>
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
                            <canvas id="insectDensityChart"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
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
            const visitDates = sortedVisits.map(visit => {
                return new Date(visit.visit_date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            });

            // Prepare datasets for Chart.js
            const occurrenceDatasets = [];
            const quantityDatasets = [];

            // Colors for the datasets
            const colors = ['#FFCC00', '#3461ff', '#12bf24', '#ff6632', '#8932ff'];

            // Initialize data for top insects
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
                        if (visit.insect_quantities && visit.insect_quantities[insectValue]) {
                            quantityData[vIndex] = parseInt(visit.insect_quantities[insectValue]);
                        } else {
                            quantityData[vIndex] = 1; // Default to 1
                        }
                    }
                });

                // Create Chart.js formatted datasets
                occurrenceDatasets.push({
                    label: insectName,
                    data: occurrenceData,
                    backgroundColor: color,
                    borderColor: color,
                    borderWidth: 1,
                    barPercentage: 0.7,
                    categoryPercentage: 0.9
                });

                quantityDatasets.push({
                    label: insectName,
                    data: quantityData,
                    backgroundColor: color,
                    borderColor: color,
                    borderWidth: 1,
                    barPercentage: 0.7,
                    categoryPercentage: 0.9
                });
            });

            // Render charts if we have visit data
            if (visitData.length > 0 && topInsects.length > 0) {
                // Occurrence Chart (line chart with dates)
                const occurrenceCtx = document.getElementById('insectTrendsChart').getContext('2d');
                const occurrenceChart = new Chart(occurrenceCtx, {
                    type: 'bar',
                    data: {
                        labels: visitDates,
                        datasets: occurrenceDatasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Insect Occurrence Over Time',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        return context.dataset.label + ': ' + (value === 1 ? 'Present' :
                                            'Absent');
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                align: 'center'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Visit Date',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Occurrence',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    color: '#e0e0e0',
                                    lineWidth: 1,
                                    borderDash: [5, 5]
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuad'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });

                // Quantity Chart (line chart with dates)
                const quantityCtx = document.getElementById('insectQuantitiesChart').getContext('2d');
                const quantityChart = new Chart(quantityCtx, {
                    type: 'bar',
                    data: {
                        labels: visitDates,
                        datasets: quantityDatasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Insect Quantities Over Time',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw + ' insects';
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                align: 'center'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Visit Date',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Number of Insects',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                min: 0,
                                grid: {
                                    color: '#e0e0e0',
                                    lineWidth: 1,
                                    borderDash: [5, 5]
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuad'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });

                // Density Chart
                const densityCtx = document.getElementById('insectDensityChart').getContext('2d');
                const densityChart = new Chart(densityCtx, {
                    type: 'bar',
                    data: {
                        labels: visitDates,
                        datasets: quantityDatasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Insect Density Over Time',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            legend: {
                                position: 'top',
                                align: 'center'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Visit Date',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Number of Insects',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                min: 0,
                                grid: {
                                    color: '#e0e0e0',
                                    lineWidth: 1,
                                    borderDash: [5, 5]
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuad'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });
            } else {
                // Display message if no data
                document.querySelector("#insectTrendsChart").innerHTML =
                    '<div class="alert alert-info">No visit data available for this branch.</div>';
                document.querySelector("#insectQuantitiesChart").innerHTML =
                    '<div class="alert alert-info">No visit data available for this branch.</div>';
                document.querySelector("#insectDensityChart").innerHTML =
                    '<div class="alert alert-info">No visit data available for this branch.</div>';
            }
        });
    </script>
@endpush
