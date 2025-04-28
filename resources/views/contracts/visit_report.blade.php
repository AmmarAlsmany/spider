@extends('shared.dashboard')

@section('content')
<div class="page-content">
    @if(session('error'))
    <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-3 alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Visit Details</h5>
                            <p class="mb-0 text-muted">Visit #{{ $visit->visit_number }}</p>
                        </div>
                    </div>
                    <!-- Visit Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Visit Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="35%">Visit Date</th>
                                            <td>{{ date('M d, Y', strtotime($visit->visit_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Visit Time</th>
                                            <td>{{ date('h:i A', strtotime($visit->visit_time)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($visit->status == 'completed')
                                                <span class="badge bg-success">
                                                    <i class="bx bx-check-circle me-1"></i>Completed
                                                </span>
                                                @elseif($visit->status == 'cancelled')
                                                <span class="badge bg-danger">
                                                    <i class="bx bx-x-circle me-1"></i>Cancelled
                                                </span>
                                                @else
                                                <span class="badge bg-warning">
                                                    <i class="bx bx-time me-1"></i>Pending
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Visit Number</th>
                                            <td>{{ $visit->visit_number }} of {{ $visit->total_visits }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Customer Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="35%">Contract Number</th>
                                            <td>
                                                <a href="{{ route('contract.show.details', $visit->contract->id) }}"
                                                    class="text-primary">
                                                    {{ $visit->contract->contract_number }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Customer Name</th>
                                            <td>{{ $visit->contract->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location</th>
                                            <td>
                                                @if($visit->branch_id)
                                                {{ $visit->branch->branch_name }}
                                                <small class="d-block text-muted">{{ $visit->branch->branch_address
                                                    }}</small>
                                                @else
                                                Main Location
                                                <small class="d-block text-muted">{{ $visit->contract->customer->address
                                                    }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Contact Number</th>
                                            <td>{{ $visit->contract->customer->phone }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visit Report (if completed) -->
                    @if($visit->status == 'completed' && $visit->report)
                    <div class="mt-4 row">
                        <div class="col-12">
                            <div class="border card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Visit Report</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Target Insects</h6>
                                            @php
                                                $insects = is_array($visit->report->target_insects) 
                                                    ? $visit->report->target_insects 
                                                    : json_decode($visit->report->target_insects, true);
                                                $insectQuantities = is_array($visit->report->insect_quantities) 
                                                    ? $visit->report->insect_quantities 
                                                    : json_decode($visit->report->insect_quantities, true);
                                            @endphp
                                            <ul class="list-unstyled">
                                                @foreach($insects as $insect)
                                                <li>
                                                    <i class="bx bx-check text-success me-2"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $insect)) }}
                                                    @if(isset($insectQuantities[$insect]))
                                                        - <span class="badge bg-info">{{ $insectQuantities[$insect] }} {{ $insectQuantities[$insect] == 1 ? 'piece' : 'pieces' }}</span>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Pesticides Used with Quantities</h6>
                                            @php
                                                $pesticides = is_array($visit->report->pesticides_used) 
                                                    ? $visit->report->pesticides_used 
                                                    : json_decode($visit->report->pesticides_used, true);
                                                $quantities = is_array($visit->report->pesticide_quantities) 
                                                    ? $visit->report->pesticide_quantities 
                                                    : json_decode($visit->report->pesticide_quantities, true);
                                            @endphp
                                            <ul class="list-unstyled">
                                                @foreach($pesticides as $pesticide)
                                                <li>
                                                    <i class="bx bx-check text-success me-2"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $pesticide)) }}
                                                    @if(isset($quantities[$pesticide]))
                                                        - <span class="badge bg-info">{{ $quantities[$pesticide]['quantity'] }} {{ $quantities[$pesticide]['unit'] }}</span>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-4 row">
                                        <div class="col-12">
                                            <h6>Elimination Steps</h6>
                                            <p class="mb-0">{{ $visit->report->elimination_steps }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 row">
                                        <div class="col-md-6">
                                            <h6>Recommendations & Observations</h6>
                                            <p>{{ $visit->report->recommendations }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Customer Notes</h6>
                                            <p>{{ $visit->report->customer_notes ?: 'No notes provided' }}</p>
                                        </div>
                                    </div>
                                    <!-- Customer Satisfaction Rating -->
                                    @if(isset($visit->report->customer_satisfaction))
                                    <div class="mt-4 row">
                                        <div class="col-12">
                                            <h6>Customer Satisfaction</h6>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @php
                                                        $satisfactionEmoji = '';
                                                        $satisfactionText = '';
                                                        $badgeClass = '';
                                                        
                                                        switch($visit->report->customer_satisfaction) {
                                                            case 1:
                                                                $satisfactionEmoji = '😡';
                                                                $satisfactionText = 'Very Dissatisfied';
                                                                $badgeClass = 'bg-danger';
                                                                break;
                                                            case 2:
                                                                $satisfactionEmoji = '😕';
                                                                $satisfactionText = 'Dissatisfied';
                                                                $badgeClass = 'bg-warning';
                                                                break;
                                                            case 3:
                                                                $satisfactionEmoji = '😐';
                                                                $satisfactionText = 'Neutral';
                                                                $badgeClass = 'bg-secondary';
                                                                break;
                                                            case 4:
                                                                $satisfactionEmoji = '🙂';
                                                                $satisfactionText = 'Satisfied';
                                                                $badgeClass = 'bg-info';
                                                                break;
                                                            case 5:
                                                                $satisfactionEmoji = '😄';
                                                                $satisfactionText = 'Very Satisfied';
                                                                $badgeClass = 'bg-success';
                                                                break;
                                                        }
                                                    @endphp
                                                    <span style="font-size: 2rem;">{{ $satisfactionEmoji }}</span>
                                                </div>
                                                <div>
                                                    <span class="badge {{ $badgeClass }}">{{ $satisfactionText }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($visit->report->customer_signature)
                                    <div class="mt-4 row">
                                        <div class="col-md-6">
                                            <h6>Customer Signature</h6>
                                            <img src="{{ $visit->report->customer_signature }}" alt="Customer Signature"
                                                class="img-fluid" style="max-height: 100px;">
                                        </div>
                                    </div>
                                    @endif
                                    @if($visit->report->phone_signature)
                                    <div class="mt-4 row">
                                        <div class="col-md-6">
                                            <h6>Phone Signature</h6>
                                            <p>{{ $visit->report->phone_signature }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection