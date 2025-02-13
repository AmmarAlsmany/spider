@extends('shared.dashboard')
@section('content')
@php
$saudiCities = [
    'Riyadh',
    'Jeddah',
    'Mecca',
    'Medina',
    'Dammam',
    'Taif',
    'Tabuk',
    'Buraidah',
    'Khamis Mushait',
    'Abha',
    'Al-Khobar',
    'Al-Ahsa',
    'Najran',
    'Yanbu',
    'Al-Qatif',
    'Al-Jubail',
    "Ha'il",
    'Al-Hofuf',
    'Al-Mubarraz',
    'Kharj',
    'Qurayyat',
    'Hafr Al-Batin',
    'Al-Kharj',
    'Arar',
    'Sakaka',
    'Jizan',
    'Al-Qunfudhah',
    'Bisha',
    'Al-Bahah',
    'Unaizah',
    'Rafha',
    'Dawadmi',
    'Ar Rass',
    "Al Majma'ah",
    'Tarut',
    'Baljurashi',
    'Shaqra',
    'Al-Zilfi',
    'Ar Rayn',
    'Wadi ad-Dawasir',
    'Badr',
    'Al Ula',
    'Tharmada',
    'Turabah',
    'Tayma',
];
sort($saudiCities);
@endphp

<style>
    .bs-stepper .bs-stepper-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #fff;
        background-color: #4361ee;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .map-container {
        height: 300px;
        margin-bottom: 15px;
    }

    .map {
        height: 100%;
        width: 100%;
        border-radius: 8px;
    }
</style>

<div class="page-content">
    <!--breadcrumb-->
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="pe-3 breadcrumb-title d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-3">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <h4 class="mb-0 text-primary"><i class="bx bx-file-plus"></i> Create Equipment Purchase Contract</h4>
        </div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Equipment Purchase Contract</li>
                </ol>
            </nav>
        </div>
    </div>

    <div id="stepper1" class="bs-stepper">
        <div class="card">
            <div class="card-header">
                <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
                    <div class="step" data-target="#test-l-1">
                        <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                            <div class="bs-stepper-circle">1</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Equipment Information</h5>
                                <p class="mb-0 steper-sub-title">Enter equipment details</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-2">
                        <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                            <div class="bs-stepper-circle">2</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Customer Details</h5>
                                <p class="mb-0 steper-sub-title">Enter customer information</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-3">
                        <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                            <div class="bs-stepper-circle">3</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Financial Information</h5>
                                <p class="mb-0 steper-sub-title">Enter financial details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="bs-stepper-content">
                    <form action="{{ route('equipment.contract.store') }}" method="POST" id="contractForm">
                        @csrf
                        <input type="hidden" name="contract_number" value="{{ $contract_number }}">
                        <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                            <h5 class="mb-1">Equipment Information</h5>
                            <p class="mb-4">Enter the equipment details</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="equipment_type" class="form-label">Equipment Type</label>
                                    <input type="text" class="form-control" id="equipment_type" name="equipment_type" required>
                                </div>
                                <div class="col-12">
                                    <label for="equipment_model" class="form-label">Equipment Model</label>
                                    <input type="text" class="form-control" id="equipment_model" name="equipment_model" required>
                                </div>
                                <div class="col-12">
                                    <label for="equipment_quantity" class="form-label">Number of Equipment</label>
                                    <input type="number" class="form-control" id="equipment_quantity" name="equipment_quantity" required min="1" value="1">
                                </div>
                                <div class="col-12">
                                    <label for="equipment_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="equipment_description" name="equipment_description" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
                            </div>
                        </div>

                        <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">
                            <h5 class="mb-1">Customer Details</h5>
                            <p class="mb-4">Enter the customer details</p>
                            <div class="row g-3">
                                @if(request()->has('existing'))
                                <div class="col-12">
                                    <label for="client_id" class="form-label">Select Existing Client</label>
                                    <select class="form-select" name="client_id" id="client_id" required>
                                        <option value="">Choose a client...</option>
                                        @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="col-12">
                                    <label for="customer_name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_mobile" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="customer_address" name="customer_address" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_city" class="form-label">City</label>
                                    <select class="form-select" id="customer_city" name="customer_city" required>
                                        <option value="">Select a city...</option>
                                        @foreach($saudiCities as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="customer_zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control" id="customer_zip_code" name="customer_zip_code" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_tax_number" class="form-label">Tax Number</label>
                                    <input type="text" class="form-control" id="customer_tax_number" name="customer_tax_number" required>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="stepper.previous()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="btn btn-primary" onclick="stepper.next()">Next</button>
                            </div>
                        </div>

                        <div id="test-l-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <h5 class="mb-1">Financial Information</h5>
                            <p class="mb-4">Enter the financial details</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="financial_amount" class="form-label">Equipment Price (without VAT)</label>
                                    <input type="number" class="form-control" id="financial_amount" name="financial_amount" required>
                                </div>
                                <div class="col-12">
                                    <label for="vat_percentage" class="form-label">VAT Percentage</label>
                                    <input type="number" class="form-control" id="vat_percentage" name="vat_percentage" value="15" required>
                                </div>

                                <div class="col-12">
                                    <label for="warranty_type" class="form-label">Warranty Type</label>
                                    <select class="form-select" id="warranty_type" name="warranty_type" required onchange="toggleWarrantyPeriod(this.value)">
                                        <option value="none">No Warranty</option>
                                        <option value="standard">Standard Warranty</option>
                                        <option value="extended">Extended Warranty</option>
                                    </select>
                                </div>

                                <div class="col-12" id="warranty_period_div" style="display: none;">
                                    <label for="warranty_period" class="form-label">Warranty Period (Months)</label>
                                    <input type="number" class="form-control" id="warranty_period" name="warranty_period" min="1" max="60" value="12">
                                </div>

                                <div class="col-12">
                                    <label for="payment_type" class="form-label">Payment Type</label>
                                    <select class="form-select" id="payment_type" name="payment_type" required onchange="toggleInstallments(this.value)">
                                        <option value="cash">Cash</option>
                                        <option value="installment">Installment</option>
                                    </select>
                                </div>
                                <div class="col-12" id="installments_div" style="display: none;">
                                    <label for="number_of_installments" class="form-label">Number of Installments</label>
                                    <input type="number" class="form-control" id="number_of_installments" name="number_of_installments" value="3" min="2" max="12">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="stepper.previous()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="submit" class="btn btn-primary">Create Contract</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
<script>
    var stepper = new Stepper(document.querySelector('#stepper1'), {
        linear: false,
        animation: true
    });

    function toggleInstallments(value) {
        const installmentsDiv = document.getElementById('installments_div');
        const installmentsInput = document.getElementById('number_of_installments');
        
        if (value === 'installment') {
            installmentsDiv.style.display = 'block';
            installmentsInput.required = true;
        } else {
            installmentsDiv.style.display = 'none';
            installmentsInput.required = false;
        }
    }

    function toggleWarrantyPeriod(value) {
        const warrantyPeriodDiv = document.getElementById('warranty_period_div');
        const warrantyDescriptionDiv = document.getElementById('warranty_description_div');
        const warrantyPeriodInput = document.getElementById('warranty_period');
        const warrantyDescriptionInput = document.getElementById('warranty_description');
        
        if (value === 'none') {
            warrantyPeriodDiv.style.display = 'none';
            warrantyDescriptionDiv.style.display = 'none';
            warrantyPeriodInput.required = false;
            warrantyDescriptionInput.required = false;
        } else {
            warrantyPeriodDiv.style.display = 'block';
            warrantyDescriptionDiv.style.display = 'block';
            warrantyPeriodInput.required = true;
            warrantyDescriptionInput.required = true;
            
            // Set default warranty period based on type
            if (value === 'standard') {
                warrantyPeriodInput.value = 12; // 1 year for standard
                warrantyPeriodInput.max = 24;
            } else if (value === 'extended') {
                warrantyPeriodInput.value = 24; // 2 years for extended
                warrantyPeriodInput.max = 60;
            }
        }
    }
</script>
@endsection
