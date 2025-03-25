@extends('shared.dashboard')

@push('styles')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .visit-type-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .visit-type-option .btn {
        text-align: center;
        padding: 12px;
        margin: 0;
        border-radius: 6px !important;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .visit-type-option .btn-check:checked + .btn {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    @media (min-width: 768px) {
        .visit-type-group {
            flex-direction: row;
            flex-wrap: wrap;
        }
        
        .visit-type-option {
            flex: 1;
            min-width: 200px;
        }
        
        .visit-type-option .btn {
            height: 100%;
            white-space: normal;
        }
    }

    @media (max-width: 768px) {
        .page-content {
            padding: 10px;
        }
        .card {
            margin-bottom: 15px;
        }
        .card-body {
            padding: 15px;
        }
        .row {
            margin: 0;
        }
        .col-md-3, .col-md-6 {
            width: 100%;
            padding: 5px;
        }
        .form-control {
            margin-bottom: 10px;
        }
        .quantity-input {
            width: 100%;
        }
        .form-check {
            margin-bottom: 10px;
        }
    }
</style>
@endpush

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
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create Visit Report</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('team-leader.visit.report.store', $visit->id) }}" method="POST" id="reportForm" class="mobile-friendly-form">
                @csrf
                
                <!-- Visit Information -->
                <div class="mb-4">
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" value="{{ $visit->contract->customer->name }}" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Region</label>
                            <input type="text" class="form-control" value="{{ $visit->branch ? $visit->branch->branch_name : 'Main' }}" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control" value="{{ date('Y-m-d', strtotime($visit->visit_date)) }}" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Contract No</label>
                            <input type="text" class="form-control" value="{{ $visit->contract->contract_number }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Time In</label>
                            <input type="time" class="form-control" name="time_in" required>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Time Out</label>
                            <input type="time" class="form-control" name="time_out" required>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label class="form-label">Visit No</label>
                            <input type="text" class="form-control" value="{{ $visit->visit_number }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Visit Type -->
                <div class="mb-4">
                    <label class="form-label">Visit Type</label>
                    <div class="visit-type-group">
                        <div class="visit-type-option">
                            <input type="radio" class="btn-check" name="visit_type" id="regular" value="regular" checked>
                            <label class="mb-2 btn btn-outline-primary w-100" for="regular">Regular Service</label>
                        </div>

                        <div class="visit-type-option">
                            <input type="radio" class="btn-check" name="visit_type" id="complementary" value="complementary">
                            <label class="mb-2 btn btn-outline-primary w-100" for="complementary">Complementary Visit</label>
                        </div>

                        <div class="visit-type-option">
                            <input type="radio" class="btn-check" name="visit_type" id="emergency" value="emergency">
                            <label class="mb-2 btn btn-outline-primary w-100" for="emergency">Emergency Visit</label>
                        </div>

                        <div class="visit-type-option">
                            <input type="radio" class="btn-check" name="visit_type" id="free" value="free">
                            <label class="mb-2 btn btn-outline-primary w-100" for="free">Free Service</label>
                        </div>

                        <div class="visit-type-option">
                            <input type="radio" class="btn-check" name="visit_type" id="other" value="other">
                            <label class="mb-2 btn btn-outline-primary w-100" for="other">Other</label>
                        </div>
                    </div>
                </div>

                <!-- Target Insects -->
                <div class="mb-4 row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Observed insects</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        @foreach($targetInsects->take(ceil($targetInsects->count() / 2)) as $insect)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check me-2">
                                                    <input type="checkbox" class="form-check-input insect-checkbox" name="target_insects[]" value="{{ $insect->value }}" id="{{ $insect->value }}">
                                                    <label class="form-check-label" for="{{ $insect->value }}">{{ $insect->name }}</label>
                                                </div>
                                                <div class="insect-quantity-container d-none">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control form-control-sm" name="insect_quantity[{{ $insect->value }}]" min="1" value="1" placeholder="Qty">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6">
                                        @foreach($targetInsects->skip(ceil($targetInsects->count() / 2)) as $insect)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check me-2">
                                                    <input type="checkbox" class="form-check-input insect-checkbox" name="target_insects[]" value="{{ $insect->value }}" id="{{ $insect->value }}">
                                                    <label class="form-check-label" for="{{ $insect->value }}">{{ $insect->name }}</label>
                                                </div>
                                                <div class="insect-quantity-container d-none">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control form-control-sm" name="insect_quantity[{{ $insect->value }}]" min="1" value="1" placeholder="Qty">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pesticides -->
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Pesticides</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $pesticides = \App\Models\Pesticide::where('active', true)->orderBy('name')->get();
                                        $halfCount = ceil($pesticides->count() / 2);
                                        $firstHalf = $pesticides->take($halfCount);
                                        $secondHalf = $pesticides->slice($halfCount);
                                    @endphp
                                    
                                    <div class="col-md-6">
                                        @foreach($firstHalf as $pesticide)
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="{{ $pesticide->slug }}" id="{{ $pesticide->slug }}">
                                            <label class="form-check-label" for="{{ $pesticide->slug }}">{{ $pesticide->name }}</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[{{ $pesticide->slug }}]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[{{ $pesticide->slug }}]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6">
                                        @foreach($secondHalf as $pesticide)
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="{{ $pesticide->slug }}" id="{{ $pesticide->slug }}">
                                            <label class="form-check-label" for="{{ $pesticide->slug }}">{{ $pesticide->name }}</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[{{ $pesticide->slug }}]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[{{ $pesticide->slug }}]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Elimination Steps -->
                <div class="mb-4 row g-3">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Elimination Steps</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <textarea class="form-control" name="elimination_steps" rows="4" placeholder="Enter the detailed steps used to eliminate the insects..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations & Notes -->
                <div class="mb-4 row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Recommendations & Observations</label>
                            <textarea class="form-control" name="recommendations" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Customer Notes</label>
                            <textarea class="form-control" name="customer_notes" rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="mb-4 row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Customer Signature</label>
                            <div class="p-3 rounded border">
                                <canvas id="signatureCanvas" width="400" height="200" class="border w-100"></canvas>
                                <input type="hidden" name="customer_signature" id="signatureData">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearSignature()">Clear Signature</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- lets add phone number field for the customer --}}

                <div class="mb-4 row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Signature Phone Number</label>
                            <input type="text" class="form-control" name="phone_signature" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" onclick="return validateForm()">
                            <i class="bx bx-save me-1"></i>Save Report
                        </button>
                        <a href="{{ route('team-leader.visits') }}" class="btn btn-secondary" onclick="return validateForm()">
                            <i class="bx bx-arrow-back me-1"></i>Back to Visits
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
<script>
    var canvas = document.getElementById('signatureCanvas');
    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    function clearSignature() {
        signaturePad.clear();
    }

    function validateForm() {
        if (signaturePad.isEmpty()) {
            alert('Please provide customer signature');
            return false;
        }

        // Get the signature data as base64 string
        document.getElementById('signatureData').value = signaturePad.toDataURL();
        
        // Check if at least one target insect is selected
        var targetInsects = document.querySelectorAll('input[name="target_insects[]"]:checked');
        if (targetInsects.length === 0) {
            alert('Please select at least one target insect');
            return false;
        }

        // Check if at least one pesticide is selected
        var pesticides = document.querySelectorAll('input[name="pesticides_used[]"]:checked');
        if (pesticides.length === 0) {
            alert('Please select at least one pesticide used');
            return false;
        }

        return true;
    }

    // Resize canvas for better display
    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    // Handle pesticide quantity inputs
    document.querySelectorAll('input[name="pesticides_used[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const quantityInput = this.parentElement.querySelector('.quantity-input');
            if (this.checked) {
                quantityInput.classList.remove('d-none');
            } else {
                quantityInput.classList.add('d-none');
                // Clear the inputs when unchecked
                quantityInput.querySelector('input[type="number"]').value = '';
            }
        });
    });

    // Handle insect quantity inputs
    document.querySelectorAll('.insect-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const quantityInput = this.closest('.d-flex').querySelector('.insect-quantity-container');
            if (this.checked) {
                quantityInput.classList.remove('d-none');
            } else {
                quantityInput.classList.add('d-none');
                // Clear the inputs when unchecked
                quantityInput.querySelector('input[type="number"]').value = '1';
            }
        });
    });
</script>
@endpush

@endsection
