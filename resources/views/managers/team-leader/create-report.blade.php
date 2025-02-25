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
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
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
                            <input type="text" class="form-control" value="{{ $visit->branch ? $visit->branch->region : 'Main' }}" readonly>
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
                                <h6 class="mb-0">Type of Target Insects</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="cockroaches" id="cockroaches">
                                            <label class="form-check-label" for="cockroaches">Cockroaches</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="rodents" id="rodents">
                                            <label class="form-check-label" for="rodents">Rodents</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="flying_insects" id="flying_insects">
                                            <label class="form-check-label" for="flying_insects">Flying Insects</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="ants" id="ants">
                                            <label class="form-check-label" for="ants">Ants</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="snakes" id="snakes">
                                            <label class="form-check-label" for="snakes">Snakes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="scorpions" id="scorpions">
                                            <label class="form-check-label" for="scorpions">Scorpions</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="lizard" id="lizard">
                                            <label class="form-check-label" for="lizard">Lizard</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="bed_bug" id="bed_bug">
                                            <label class="form-check-label" for="bed_bug">Bed Bug</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="termites_before" id="termites_before">
                                            <label class="form-check-label" for="termites_before">Termites Before Building</label>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="target_insects[]" value="termites_after" id="termites_after">
                                            <label class="form-check-label" for="termites_after">Termites After Building</label>
                                        </div>
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
                                    <div class="col-md-6">
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="agenda" id="agenda">
                                            <label class="form-check-label" for="agenda">Agenda</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[agenda]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[agenda]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="gel" id="gel">
                                            <label class="form-check-label" for="gel">Gel</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[gel]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[gel]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="snake_away" id="snake_away">
                                            <label class="form-check-label" for="snake_away">Snake Away</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[snake_away]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[snake_away]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="glue_board" id="glue_board">
                                            <label class="form-check-label" for="glue_board">Glue Board</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[glue_board]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[glue_board]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="fly_ribbon" id="fly_ribbon">
                                            <label class="form-check-label" for="fly_ribbon">Fly Ribbon</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[fly_ribbon]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[fly_ribbon]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="roban_pasta" id="roban_pasta">
                                            <label class="form-check-label" for="roban_pasta">Roban Pasta</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[roban_pasta]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[roban_pasta]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="powder" id="powder">
                                            <label class="form-check-label" for="powder">Powder</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[powder]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[powder]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="free_kill" id="free_kill">
                                            <label class="form-check-label" for="free_kill">Free Kill</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[free_kill]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[free_kill]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="boomer" id="boomer">
                                            <label class="form-check-label" for="boomer">Boomer</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[boomer]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[boomer]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="glue_tandem" id="glue_tandem">
                                            <label class="form-check-label" for="glue_tandem">Glue Tandem</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[glue_tandem]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[glue_tandem]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="ageta" id="ageta">
                                            <label class="form-check-label" for="ageta">Ageta</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[ageta]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[ageta]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="wax" id="wax">
                                            <label class="form-check-label" for="wax">Wax</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[wax]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[wax]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" class="form-check-input" name="pesticides_used[]" value="hotac" id="hotac">
                                            <label class="form-check-label" for="hotac">Hotac</label>
                                            <div class="mt-1 input-group quantity-input d-none">
                                                <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="pesticide_quantity[hotac]" placeholder="Quantity">
                                                <select class="form-select form-select-sm" name="pesticide_unit[hotac]">
                                                    <option value="g">Grams</option>
                                                    <option value="ml">Milliliters</option>
                                                </select>
                                            </div>
                                        </div>
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
</script>
@endpush

@endsection
