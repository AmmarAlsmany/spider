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
                                <h5 class="mb-0 steper-title">Customer Information</h5>
                                <p class="mb-0 steper-sub-title">Enter customer details</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-2">
                        <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                            <div class="bs-stepper-circle">2</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Equipment Details</h5>
                                <p class="mb-0 steper-sub-title">Enter equipment and warranty details</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-3">
                        <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                            <div class="bs-stepper-circle">3</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Branch Information</h5>
                                <p class="mb-0 steper-sub-title">Enter branch details</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-4">
                        <div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="test-l-4">
                            <div class="bs-stepper-circle">4</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Payment Information</h5>
                                <p class="mb-0 steper-sub-title">Enter payment details and schedule</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-5">
                        <div class="step-trigger" role="tab" id="stepper1trigger5" aria-controls="test-l-5">
                            <div class="bs-stepper-circle">5</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Summary</h5>
                                <p class="mb-0 steper-sub-title">Review and submit</p>
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
                            <h5 class="mb-1">Customer Information</h5>
                            <p class="mb-4">Enter the customer details</p>
                            <div class="row g-3">
                                @if(request()->has('existing'))
                                <div class="col-12">
                                    <label for="client_id" class="form-label">Select Existing Client</label>
                                    <select class="form-select" name="client_id" id="client_id" required>
                                        <option value="">Choose a client...</option>
                                        @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}
                                            <small class="text-muted">({{ $client->email }})</small>
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="col-12">
                                    <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                    <div class="invalid-feedback">Please enter the customer name</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required>
                                    <div class="invalid-feedback">Please enter a valid Saudi mobile number (05xxxxxxxx)</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_address" name="customer_address" required>
                                    <div class="invalid-feedback">Please enter the customer address</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_city" class="form-label">City <span class="text-danger">*</span></label>
                                    <select class="form-select" id="customer_city" name="customer_city" required>
                                        <option value="">Select a city...</option>
                                        @foreach($saudiCities as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select the customer city</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control" id="customer_zip_code" name="customer_zip_code">
                                    <div class="invalid-feedback">Please enter a valid ZIP code</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_tax_number" class="form-label">Tax Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_tax_number" name="customer_tax_number" required>
                                    <div class="invalid-feedback">Please enter the tax number</div>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handleNextStep()">Next</button>
                            </div>
                        </div>

                        <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">
                            <div class="step-indicator">
                                <h5 class="mb-1">Equipment Details</h5>
                            </div>
                            <p class="mb-4">Enter equipment and warranty details</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="equipment_type_id" name="equipment_type_id" required>
                                            <option value="">Select Equipment Type</option>
                                            @foreach($equipment_types as $type)
                                                <option value="{{ $type->id }}" data-price="{{ $type->default_price }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="equipment_type_id">Equipment Type <i class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Select the type of equipment you wish to purchase"></i></label>
                                        <div class="invalid-feedback">Please select an equipment type</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="equipment_model" name="equipment_model" placeholder="Enter model" required>
                                        <label for="equipment_model">Equipment Model <i class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Enter the specific model of the equipment"></i></label>
                                        <div class="invalid-feedback">Please enter the equipment model</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="equipment_quantity" name="equipment_quantity" placeholder="Enter quantity" required min="1">
                                        <label for="equipment_quantity">Quantity <i class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Enter the number of units you wish to purchase"></i></label>
                                        <div class="invalid-feedback">Please enter a valid quantity (minimum 1)</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="warranty" name="warranty" placeholder="Enter warranty period" required min="0" value="0">
                                        <label for="warranty">Warranty Period (Months) <i class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Enter the warranty period in months"></i></label>
                                        <div class="invalid-feedback">Please enter a valid warranty period</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="equipment_description" name="equipment_description" placeholder="Enter description" style="height: 100px" required></textarea>
                                        <label for="equipment_description">Description <i class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Provide a detailed description of the equipment"></i></label>
                                        <div class="invalid-feedback">Please enter the equipment description</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="btn btn-primary" onclick="handleNextStep()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
                            </div>
                        </div>

                        <div id="test-l-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <div class="step-indicator">
                                <h5 class="mb-1">Branch Information</h5>
                            </div>
                            <p class="mb-4">Enter branch details. You can add multiple branches if needed.</p>
                            
                            <div id="branches-container">
                                <!-- Initial branch form -->
                                <div class="p-3 mb-4 rounded border branch-form" data-branch-index="0">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Branch #1</h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-branch-btn" style="display: none;">
                                            <i class='bx bx-trash'></i> Remove
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="branchName0" class="form-label">Branch Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="branchName0" name="branchName[0]" required>
                                            <div class="invalid-feedback">Please enter the branch name</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="branchmanager0" class="form-label">Branch Manager Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="branchmanager0" name="branchmanager[0]" required>
                                            <div class="invalid-feedback">Please enter the branch manager name</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="branchmanagerPhone0" class="form-label">Manager Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="branchmanagerPhone0" name="branchmanagerPhone[0]" 
                                                pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required>
                                            <div class="invalid-feedback">Please enter a valid Saudi mobile number (05xxxxxxxx)</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="branchCity0" class="form-label">City <span class="text-danger">*</span></label>
                                            <select class="form-select" id="branchCity0" name="branchCity[0]" required>
                                                <option value="">Select a city...</option>
                                                @foreach($saudiCities as $city)
                                                    <option value="{{ $city }}">{{ $city }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select the branch city</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="branchAddress0" class="form-label">Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="branchAddress0" name="branchAddress[0]" rows="2" required></textarea>
                                            <div class="invalid-feedback">Please enter the branch address</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary" id="add-branch-btn">
                                    <i class='bx bx-plus me-1'></i> Add Another Branch
                                </button>
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="btn btn-primary" onclick="handleNextStep()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
                            </div>
                        </div>

                        <div id="test-l-4" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger4">
                            <h5 class="mb-1">Payment Information</h5>
                            <p class="mb-4">Enter payment information and the number of payments</p>

                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <label for="Contractamount" class="form-label">Contract Amount (without VAT) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="contractamount" id="Contractamount"
                                        x-model="contractAmount" required min="1" step="0.01">
                                    <div class="invalid-feedback">Please enter a valid contract amount</div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <label for="first_payment_date" class="form-label">First Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="first_payment_date" id="first_payment_date"
                                        x-model="first_payment_date" required min="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback">Please select a valid payment date</div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="payment_type" id="payment_type" x-model="payment_type" required>
                                        <option value="prepaid">Prepaid (Full Amount)</option>
                                        <option value="postpaid">Postpaid (Installments)</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a payment type</div>
                                </div>

                                <template x-if="payment_type === 'postpaid'">
                                    <div class="col-12 col-lg-6 postpaid-field">
                                        <label for="number_of_payments" class="form-label">Number of Payments <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="number_of_payments" id="number_of_payments"
                                            x-model="numberOfPayments" required min="1" max="24">
                                        <div class="invalid-feedback">Please enter a valid number of payments (1-24)</div>
                                    </div>
                                </template>

                                <template x-if="payment_type === 'postpaid'">
                                    <div class="col-12 col-lg-6 postpaid-field">
                                        <label for="payment_schedule" class="form-label">Payment Schedule <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_schedule" id="payment_schedule" x-model="payment_schedule" required>
                                            <option value="monthly">Monthly</option>
                                            <option value="custom">Custom Dates</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a payment schedule</div>
                                    </div>
                                </template>

                                <template x-if="payment_type === 'postpaid' && payment_schedule === 'custom'">
                                    <div class="col-12 postpaid-field custom-schedule-field">
                                        <div class="p-3 rounded border">
                                            <h6 class="mb-3">Custom Payment Dates</h6>
                                            <div id="custom-payment-dates">
                                                <!-- Custom payment date fields will be generated here -->
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-6">Contract Amount:</div>
                                            <div class="col-6 text-end" x-text="'SAR ' + Number(contractAmount).toFixed(2)"></div>
                                            <div class="col-6">VAT (15%):</div>
                                            <div class="col-6 text-end" x-text="'SAR ' + vatAmount.toFixed(2)"></div>
                                            <div class="col-6"><strong>Total Amount:</strong></div>
                                            <div class="col-6 text-end"><strong x-text="'SAR ' + totalAmount.toFixed(2)"></strong></div>
                                            <template x-if="payment_type === 'postpaid'">
                                                <div class="mt-2 col-12">
                                                    <div class="row">
                                                        <div class="col-6">Payment per Installment:</div>
                                                        <div class="col-6 text-end" x-text="'SAR ' + installmentAmount.toFixed(2)"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="btn btn-primary" onclick="handleNextStep()">Next<i class='bx bx-right-arrow-alt ms-2'></i></button>
                            </div>
                        </div>

                        <div id="test-l-5" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger5">
                            <div class="step-indicator">
                                <h5 class="mb-1">Summary</h5>
                            </div>
                            <p class="mb-4">Review your contract details</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="mb-4 card-title">Contract Summary</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-3">Equipment Information</h6>
                                                    <table class="table table-borderless">
                                                        <tbody id="equipment-summary">
                                                            <!-- Will be populated by JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-3">Payment Information</h6>
                                                    <table class="table table-borderless">
                                                        <tbody id="payment-summary">
                                                            <!-- Will be populated by JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="mb-4 card-title">Branch Information</h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6 class="mb-3">Branch Details</h6>
                                                    <table class="table table-borderless">
                                                        <tbody id="branch-summary">
                                                            <!-- Will be populated by JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="submit" class="btn btn-success"><i class='bx bx-check me-2'></i>Submit Contract</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    let stepper;
    let currentStep = 0;

    // Validation functions
    function validateStep1() {
        const form = document.getElementById('contractForm');
        if (form.querySelector('[name="client_id"]')) {
            // For existing client
            const clientId = form.querySelector('[name="client_id"]').value;
            return clientId ? true : false;
        } else {
            // For new client
            const fields = [
                { id: 'customer_name', regex: new RegExp('.+') },
                { id: 'customer_mobile', regex: new RegExp('^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$') },
                { id: 'customer_email', regex: new RegExp('^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$') },
                { id: 'customer_address', regex: new RegExp('.+') },
                { id: 'customer_city', regex: new RegExp('.+') },
                { id: 'customer_tax_number', regex: new RegExp('^[0-9]{10,15}$') }
            ];

            return fields.every(field => {
                const element = document.getElementById(field.id);
                if (!element) return true;
                const isValid = field.regex.test(element.value);
                element.classList.toggle('is-invalid', !isValid);
                return isValid;
            });
        }
    }

    function validateStep2() {
        const fields = [
            { id: 'equipment_type_id', regex: new RegExp('.+') },
            { id: 'equipment_model', regex: new RegExp('.+') },
            { id: 'equipment_quantity', regex: new RegExp('^[1-9]\\d*$') },
            { id: 'equipment_description', regex: new RegExp('.+') },
            { id: 'warranty', regex: new RegExp('^[0-9]+$') }
        ];

        return fields.every(field => {
            const element = document.getElementById(field.id);
            if (!element) return true;
            const isValid = field.regex.test(element.value);
            element.classList.toggle('is-invalid', !isValid);
            return isValid;
        });
    }

    function validateStep3() {
        const branches = document.querySelectorAll('.branch-form');
        let isValid = true;

        branches.forEach((branch, index) => {
            // Use safer selectors that work with both naming patterns
            const branchName = branch.querySelector('input[id^="branchName"]') || branch.querySelector('input[name^="branchName"]');
            const branchManager = branch.querySelector('input[id^="branchmanager"]') || branch.querySelector('input[name^="branchmanager"]');
            const branchManagerPhone = branch.querySelector('input[id^="branchmanagerPhone"]') || branch.querySelector('input[name^="branchmanagerPhone"]');
            const branchCity = branch.querySelector('select[id^="branchCity"]') || branch.querySelector('select[name^="branchCity"]');
            const branchAddress = branch.querySelector('textarea[id^="branchAddress"]') || branch.querySelector('textarea[name^="branchAddress"]');

            // Safely check values
            if (!branchName?.value || !branchManager?.value || !branchManagerPhone?.value || 
                !branchCity?.value || !branchAddress?.value) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all branch details'
                });
            }
        });

        return isValid;
    }

    function validateStep4() {
        const form = document.getElementById('contractForm');
        let isValid = true;
        let errorMessage = '';

        // Validate contract amount
        const contractAmount = form.querySelector('#Contractamount');
        if (!contractAmount || !contractAmount.value || parseFloat(contractAmount.value) <= 0) {
            if (contractAmount) contractAmount.classList.add('is-invalid');
            isValid = false;
            errorMessage = 'Please enter a valid contract amount greater than 0';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
            return false;
        }
        if (contractAmount) contractAmount.classList.remove('is-invalid');

        // Get payment type
        const paymentType = form.querySelector('#payment_type');
        if (!paymentType || !paymentType.value || !['prepaid', 'postpaid'].includes(paymentType.value)) {
            if (paymentType) paymentType.classList.add('is-invalid');
            isValid = false;
            errorMessage = 'Please select a valid payment type';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
            return false;
        }
        if (paymentType) paymentType.classList.remove('is-invalid');

        // Validate first payment date (required for both prepaid and postpaid)
        const firstPaymentDate = form.querySelector('#first_payment_date');
        if (!firstPaymentDate || !firstPaymentDate.value) {
            if (firstPaymentDate) firstPaymentDate.classList.add('is-invalid');
            isValid = false;
            errorMessage = 'Please select the payment date';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
            return false;
        }
        
        // Validate first payment date is not in the past
        if (firstPaymentDate && firstPaymentDate.value) {
            const selectedDate = new Date(firstPaymentDate.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                firstPaymentDate.classList.add('is-invalid');
                errorMessage = 'First payment date cannot be in the past';
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage
                });
                return false;
            }
        }
        if (firstPaymentDate) firstPaymentDate.classList.remove('is-invalid');

        if (paymentType.value === 'postpaid') {
            // Validate number of payments
            const numberOfPayments = form.querySelector('#number_of_payments');
            if (!numberOfPayments || !numberOfPayments.value ||
                parseInt(numberOfPayments.value) < 1 || parseInt(numberOfPayments.value) > 24) {
                if (numberOfPayments) numberOfPayments.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please enter a valid number of payments (1-24)';
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage
                });
                return false;
            }
            if (numberOfPayments) numberOfPayments.classList.remove('is-invalid');

            // Validate payment schedule
            const paymentSchedule = form.querySelector('#payment_schedule');
            if (!paymentSchedule || !paymentSchedule.value) {
                if (paymentSchedule) paymentSchedule.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please select a payment schedule';
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage
                });
                return false;
            }
            if (paymentSchedule) paymentSchedule.classList.remove('is-invalid');

            // Validate custom payment dates if custom schedule is selected
            if (paymentSchedule.value === 'custom') {
                const customDateInputs = form.querySelectorAll('[id^="payment_date_"]:not([id="payment_date_1"])');
                if (customDateInputs && customDateInputs.length > 0) {
                    let previousDate = new Date(firstPaymentDate.value);

                    for (let i = 0; i < customDateInputs.length; i++) {
                        const input = customDateInputs[i];
                        if (!input.value) {
                            input.classList.add('is-invalid');
                            isValid = false;
                            errorMessage = `Please select payment date ${i + 2}`;
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMessage
                            });
                            return false;
                        }

                        const currentDate = new Date(input.value);
                        if (currentDate <= previousDate) {
                            input.classList.add('is-invalid');
                            isValid = false;
                            errorMessage = `Payment date ${i + 2} must be after the previous payment date`;
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMessage
                            });
                            return false;
                        }

                        input.classList.remove('is-invalid');
                        previousDate = currentDate;
                    }
                }
            }
        }

        return isValid;
    }

    function validateCurrentStep() {
        switch(currentStep) {
            case 0:
                return validateStep1();
            case 1:
                return validateStep2();
            case 2:
                return validateStep3();
            case 3:
                return validateStep4();
            case 4:
                return true; // Summary step, no validation needed
            default:
                return false;
        }
    }

    function updateSummary() {
        // Update equipment summary
        const equipmentSummary = document.getElementById('equipment-summary');
        if (equipmentSummary) {
            const equipmentType = document.getElementById('equipment_type_id');
            const equipmentTypeText = equipmentType ? equipmentType.options[equipmentType.selectedIndex]?.text : 'N/A';
            const model = document.getElementById('equipment_model').value;
            const description = document.getElementById('equipment_description').value;
            
            equipmentSummary.innerHTML = `
                <tr>
                    <td><strong>Equipment Type:</strong></td>
                    <td>${equipmentTypeText || 'N/A'}</td>
                </tr>
                <tr>
                    <td><strong>Model:</strong></td>
                    <td>${model || 'N/A'}</td>
                </tr>
                <tr>
                    <td><strong>Description:</strong></td>
                    <td>${description || 'N/A'}</td>
                </tr>
            `;
        }
        
        // Update payment summary
        const paymentSummary = document.getElementById('payment-summary');
        if (paymentSummary) {
            const contractAmount = document.getElementById('Contractamount').value;
            const paymentType = document.getElementById('payment_type').value;
            const firstPaymentDate = document.getElementById('first_payment_date').value;
            
            // Calculate VAT and total amount
            const vatAmount = parseFloat(contractAmount) * 0.15;
            const totalAmount = parseFloat(contractAmount) * 1.15;
            
            let paymentDetails = `
                <tr>
                    <td><strong>Contract Amount:</strong></td>
                    <td>SAR ${parseFloat(contractAmount).toFixed(2)}</td>
                </tr>
                <tr>
                    <td><strong>VAT Amount (15%):</strong></td>
                    <td>SAR ${vatAmount.toFixed(2)}</td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td>SAR ${totalAmount.toFixed(2)}</td>
                </tr>
                <tr>
                    <td><strong>Payment Type:</strong></td>
                    <td>${paymentType === 'prepaid' ? 'Prepaid (Full Amount)' : 'Postpaid (Installments)'}</td>
                </tr>
            `;
            
            // Add installment details if postpaid
            if (paymentType === 'postpaid') {
                const numberOfPayments = document.getElementById('number_of_payments').value;
                const paymentSchedule = document.getElementById('payment_schedule').value;
                const installmentAmount = totalAmount / parseInt(numberOfPayments);
                
                paymentDetails += `
                    <tr>
                        <td><strong>Number of Payments:</strong></td>
                        <td>${numberOfPayments}</td>
                    </tr>
                    <tr>
                        <td><strong>Payment Schedule:</strong></td>
                        <td>${paymentSchedule === 'monthly' ? 'Monthly' : 'Custom'}</td>
                    </tr>
                    <tr>
                        <td><strong>Amount per Installment:</strong></td>
                        <td>SAR ${installmentAmount.toFixed(2)}</td>
                    </tr>
                `;
                
                // Add payment dates section
                paymentDetails += `<tr><td colspan="2"><strong>Payment Dates:</strong></td></tr>`;
                
                // Always include the first payment date as payment 1
                paymentDetails += `
                    <tr>
                        <td>Payment 1:</td>
                        <td>${firstPaymentDate}</td>
                    </tr>
                `;
                
                // Add custom payment dates if applicable
                if (paymentSchedule === 'custom') {
                    for (let i = 2; i <= parseInt(numberOfPayments); i++) {
                        const dateInput = document.getElementById(`payment_date_${i}`);
                        if (dateInput && dateInput.value) {
                            paymentDetails += `
                                <tr>
                                    <td>Payment ${i}:</td>
                                    <td>${dateInput.value}</td>
                                </tr>
                            `;
                        }
                    }
                }
            } else {
                // For prepaid, just show the payment date
                paymentDetails += `
                    <tr>
                        <td><strong>Payment Date:</strong></td>
                        <td>${firstPaymentDate}</td>
                    </tr>
                `;
            }
            
            paymentSummary.innerHTML = paymentDetails;
        }
        
        // Update branch summary
        const branchSummary = document.getElementById('branch-summary');
        if (branchSummary) {
            const branches = document.querySelectorAll('.branch-form');
            
            let branchDetails = '';
            
            branches.forEach((branch, index) => {
                // Use safer selectors
                const branchName = branch.querySelector('input[id^="branchName"]') || branch.querySelector('input[name^="branchName"]');
                const branchManager = branch.querySelector('input[id^="branchmanager"]') || branch.querySelector('input[name^="branchmanager"]');
                const branchManagerPhone = branch.querySelector('input[id^="branchmanagerPhone"]') || branch.querySelector('input[name^="branchmanagerPhone"]');
                const branchCity = branch.querySelector('select[id^="branchCity"]') || branch.querySelector('select[name^="branchCity"]');
                const branchAddress = branch.querySelector('textarea[id^="branchAddress"]') || branch.querySelector('textarea[name^="branchAddress"]');
                
                // Only add to the summary if all values exist
                if (branchName?.value && branchManager?.value && branchManagerPhone?.value && 
                    branchCity?.value && branchAddress?.value) {
                    
                    // For city, get the selected option text
                    let cityText = branchCity.value;
                    if (branchCity.selectedIndex !== -1) {
                        cityText = branchCity.options[branchCity.selectedIndex].text;
                    }
                    
                    branchDetails += `
                        <tr>
                            <td colspan="2"><strong>Branch #${index + 1}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Branch Name:</strong></td>
                            <td>${branchName.value}</td>
                        </tr>
                        <tr>
                            <td><strong>Branch Manager:</strong></td>
                            <td>${branchManager.value}</td>
                        </tr>
                        <tr>
                            <td><strong>Manager Phone:</strong></td>
                            <td>${branchManagerPhone.value}</td>
                        </tr>
                        <tr>
                            <td><strong>City:</strong></td>
                            <td>${cityText}</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>${branchAddress.value}</td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                    `;
                }
            });
            
            branchSummary.innerHTML = branchDetails || '<tr><td colspan="2">No branch information available</td></tr>';
        }
    }
    
    function handleNextStep() {
        if (validateCurrentStep()) {
            if (currentStep === 3) {
                // Update summary before showing the summary step
                updateSummary();
            }
            stepper.next();
            currentStep++;
        }
    }

    function handlePreviousStep() {
        currentStep--;
        stepper.previous();
    }

    function submitForm() {
        if (validateCurrentStep()) {
            document.getElementById('contractForm').submit();
        }
    }

    // Initialize stepper when DOM is loaded
    document.addEventListener('DOMContentLoaded', function () {
        stepper = new Stepper(document.querySelector('#stepper1'), {
            linear: true,
            animation: true
        });

        // Function to generate custom payment date fields
        window.generateCustomPaymentDateFields = function(numberOfPayments) {
            const container = document.getElementById('custom-payment-dates');
            if (!container) return;
            
            container.innerHTML = '';
            
            const today = new Date();
            const minDate = today.toISOString().split('T')[0];
            
            // Get the first payment date value
            const firstPaymentDate = document.getElementById('first_payment_date');
            let firstDate = '';
            if (firstPaymentDate && firstPaymentDate.value) {
                firstDate = firstPaymentDate.value;
                
                // Add the first payment date as payment number 1
                const row = document.createElement('div');
                row.className = 'mb-3';
                
                const label = document.createElement('label');
                label.className = 'form-label';
                label.innerHTML = `Payment 1 Date <span class="text-danger">*</span>`;
                
                const input = document.createElement('input');
                input.type = 'date';
                input.className = 'form-control';
                input.name = 'payment_date_1';
                input.id = 'payment_date_1';
                input.value = firstDate;
                input.disabled = true;
                
                const helpText = document.createElement('div');
                helpText.className = 'form-text';
                helpText.textContent = 'This is your first payment date';
                
                row.appendChild(label);
                row.appendChild(input);
                row.appendChild(helpText);
                container.appendChild(row);
            }
            
            // Generate the rest of the payment date fields starting from payment 2
            for (let i = 2; i <= numberOfPayments; i++) {
                const row = document.createElement('div');
                row.className = 'mb-3';
                
                const label = document.createElement('label');
                label.className = 'form-label';
                label.htmlFor = `payment_date_${i}`;
                label.innerHTML = `Payment ${i} Date <span class="text-danger">*</span>`;
                
                const input = document.createElement('input');
                input.type = 'date';
                input.className = 'form-control';
                input.name = `payment_date_${i}`;
                input.id = `payment_date_${i}`;
                input.required = true;
                input.min = minDate;
                
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Please select a valid payment date';
                
                row.appendChild(label);
                row.appendChild(input);
                row.appendChild(feedback);
                container.appendChild(row);
            }
        }
        
        // Added JavaScript functions to handle payment type and schedule changes
        function handlePaymentTypeChange() {
            const paymentType = document.getElementById('payment_type').value;
            const postpaidFields = document.querySelectorAll('.postpaid-field');
            
            if (paymentType === 'postpaid') {
                postpaidFields.forEach(field => {
                    field.style.display = 'block';
                });
            } else {
                postpaidFields.forEach(field => {
                    field.style.display = 'none';
                });
                
                // Clear custom payment dates container when switching to prepaid
                const container = document.getElementById('custom-payment-dates');
                if (container) {
                    container.innerHTML = '';
                }
            }
        }
        
        function handlePaymentScheduleChange() {
            const paymentSchedule = document.getElementById('payment_schedule').value;
            const numberOfPayments = parseInt(document.getElementById('number_of_payments').value);
            
            if (paymentSchedule === 'custom' && numberOfPayments > 0) {
                generateCustomPaymentDateFields(numberOfPayments);
            } else {
                // Clear custom payment dates container when switching to monthly
                const container = document.getElementById('custom-payment-dates');
                if (container) {
                    container.innerHTML = '';
                }
            }
        }

        // Add event listeners when the document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initial setup
            handlePaymentTypeChange();
            
            // Add event listeners for payment type and schedule changes
            const paymentTypeSelect = document.getElementById('payment_type');
            if (paymentTypeSelect) {
                paymentTypeSelect.addEventListener('change', handlePaymentTypeChange);
            }
            
            const paymentScheduleSelect = document.getElementById('payment_schedule');
            if (paymentScheduleSelect) {
                paymentScheduleSelect.addEventListener('change', handlePaymentScheduleChange);
            }
            
            const numberOfPaymentsInput = document.getElementById('number_of_payments');
            if (numberOfPaymentsInput) {
                numberOfPaymentsInput.addEventListener('change', function() {
                    if (document.getElementById('payment_schedule').value === 'custom') {
                        generateCustomPaymentDateFields(parseInt(this.value));
                    }
                });
            }
            
            // Initialize the branch counter
            let branchCounter = 1;

            // Add Branch button functionality
            document.getElementById('add-branch-btn').addEventListener('click', function() {
                const branchesContainer = document.getElementById('branches-container');
                const newIndex = branchCounter;
                
                // Create a new branch form
                const branchForm = document.createElement('div');
                branchForm.className = 'p-3 mb-4 rounded border branch-form';
                branchForm.setAttribute('data-branch-index', newIndex);
                
                // Create the branch form HTML
                branchForm.innerHTML = `
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Branch #${newIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-branch-btn">
                            <i class='bx bx-trash'></i> Remove
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="branchName${newIndex}" class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="branchName${newIndex}" name="branchName[${newIndex}]" required>
                            <div class="invalid-feedback">Please enter the branch name</div>
                        </div>
                        <div class="col-md-6">
                            <label for="branchmanager${newIndex}" class="form-label">Branch Manager Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="branchmanager${newIndex}" name="branchmanager[${newIndex}]" required>
                            <div class="invalid-feedback">Please enter the branch manager name</div>
                        </div>
                        <div class="col-md-6">
                            <label for="branchmanagerPhone${newIndex}" class="form-label">Manager Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="branchmanagerPhone${newIndex}" name="branchmanagerPhone[${newIndex}]" 
                                pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required>
                            <div class="invalid-feedback">Please enter a valid Saudi mobile number (05xxxxxxxx)</div>
                        </div>
                        <div class="col-md-6">
                            <label for="branchCity${newIndex}" class="form-label">City <span class="text-danger">*</span></label>
                            <select class="form-select" id="branchCity${newIndex}" name="branchCity[${newIndex}]" required>
                                <option value="">Select a city...</option>
                                ${getSaudiCitiesOptions()}
                            </select>
                            <div class="invalid-feedback">Please select the branch city</div>
                        </div>
                        <div class="col-12">
                            <label for="branchAddress${newIndex}" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="branchAddress${newIndex}" name="branchAddress[${newIndex}]" rows="2" required></textarea>
                            <div class="invalid-feedback">Please enter the branch address</div>
                        </div>
                    </div>
                `;
                
                // Add the new branch form to the container
                branchesContainer.appendChild(branchForm);
                
                // Add event listener to the remove button
                branchForm.querySelector('.remove-branch-btn').addEventListener('click', function() {
                    branchForm.remove();
                    // Reindex the remaining branches to ensure proper array indexing
                    reindexBranches();
                    updateRemoveButtons();
                });
                
                // Show the remove button on the first branch if we have more than one branch
                updateRemoveButtons();
                
                // Increment the counter
                branchCounter++;
            });
            
            // Function to update remove buttons visibility
            function updateRemoveButtons() {
                const branches = document.querySelectorAll('.branch-form');
                const firstBranchRemoveBtn = branches[0]?.querySelector('.remove-branch-btn');
                
                if (firstBranchRemoveBtn) {
                    // Show the remove button on the first branch only if we have more than one branch
                    if (branches.length > 1) {
                        firstBranchRemoveBtn.style.display = 'block';
                    } else {
                        firstBranchRemoveBtn.style.display = 'none';
                    }
                }
                
                // Make sure we always have at least one branch
                if (branches.length === 1) {
                    branches[0].querySelector('.remove-branch-btn').style.display = 'none';
                }
            }
            
            // Add event listener to the first branch's remove button
            document.querySelector('.branch-form .remove-branch-btn').addEventListener('click', function() {
                this.closest('.branch-form').remove();
                reindexBranches();
                updateRemoveButtons();
            });
            
            // Function to reindex branches after removal
            function reindexBranches() {
                const branches = document.querySelectorAll('.branch-form');
                branches.forEach((branch, index) => {
                    branch.setAttribute('data-branch-index', index);
                    
                    // Update the branch title
                    const title = branch.querySelector('h6');
                    if (title) {
                        title.textContent = `Branch #${index + 1}`;
                    }
                    
                    // Update input names and IDs
                    updateInputAttributes(branch, 'branchName', index);
                    updateInputAttributes(branch, 'branchmanager', index);
                    updateInputAttributes(branch, 'branchmanagerPhone', index);
                    updateInputAttributes(branch, 'branchCity', index);
                    updateInputAttributes(branch, 'branchAddress', index);
                });
                
                // Update branch counter
                branchCounter = branches.length;
            }
            
            // Helper function to update input attributes
            function updateInputAttributes(branch, baseName, index) {
                const input = branch.querySelector(`[name^="${baseName}["]`);
                if (input) {
                    input.name = `${baseName}[${index}]`;
                    input.id = `${baseName}${index}`;
                    
                    // Update label for attribute
                    const label = branch.querySelector(`label[for^="${baseName}"]`);
                    if (label) {
                        label.setAttribute('for', `${baseName}${index}`);
                    }
                }
            }

            // Helper function to generate Saudi cities options
            function getSaudiCitiesOptions() {
                const cities = @json($saudiCities);
                return cities.map(city => `<option value="${city}">${city}</option>`).join('');
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Branch counter for new branches
        let branchCounter = 1;
        
        // Get the add branch button
        const addBranchBtn = document.getElementById('add-branch-btn');
        if (!addBranchBtn) {
            console.error('Add branch button not found');
            return;
        }
        
        // Add click event listener
        addBranchBtn.onclick = function() {
            console.log('Add branch button clicked');
            
            // Get the branches container
            const branchesContainer = document.getElementById('branches-container');
            if (!branchesContainer) {
                console.error('Branches container not found');
                return;
            }
            
            // Create new branch element
            const newBranch = document.createElement('div');
            newBranch.className = 'p-3 mb-4 rounded border branch-form';
            newBranch.dataset.branchIndex = branchCounter;
            
            // Generate the HTML for the new branch
            newBranch.innerHTML = `
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Branch #${branchCounter + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-branch-btn">
                        <i class='bx bx-trash'></i> Remove
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="branchName${branchCounter}" class="form-label">Branch Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="branchName${branchCounter}" name="branchName[${branchCounter}]" required>
                        <div class="invalid-feedback">Please enter the branch name</div>
                    </div>
                    <div class="col-md-6">
                        <label for="branchmanager${branchCounter}" class="form-label">Branch Manager Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="branchmanager${branchCounter}" name="branchmanager[${branchCounter}]" required>
                        <div class="invalid-feedback">Please enter the branch manager name</div>
                    </div>
                    <div class="col-md-6">
                        <label for="branchmanagerPhone${branchCounter}" class="form-label">Manager Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="branchmanagerPhone${branchCounter}" name="branchmanagerPhone[${branchCounter}]" 
                            pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required>
                        <div class="invalid-feedback">Please enter a valid Saudi mobile number (05xxxxxxxx)</div>
                    </div>
                    <div class="col-md-6">
                        <label for="branchCity${branchCounter}" class="form-label">City <span class="text-danger">*</span></label>
                        <select class="form-select" id="branchCity${branchCounter}" name="branchCity[${branchCounter}]" required>
                            <option value="">Select a city...</option>
                            @foreach($saudiCities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select the branch city</div>
                    </div>
                    <div class="col-12">
                        <label for="branchAddress${branchCounter}" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="branchAddress${branchCounter}" name="branchAddress[${branchCounter}]" rows="2" required></textarea>
                        <div class="invalid-feedback">Please enter the branch address</div>
                    </div>
                </div>
            `;
            
            // Add the new branch to the container
            branchesContainer.appendChild(newBranch);
            
            // Add remove button event listener
            const removeBtn = newBranch.querySelector('.remove-branch-btn');
            if (removeBtn) {
                removeBtn.onclick = function() {
                    newBranch.remove();
                };
            }
            
            // Increment the counter
            branchCounter++;
            
            // Show the remove button on the first branch
            const firstBranch = branchesContainer.querySelector('.branch-form:first-child');
            const firstBranchRemoveBtn = firstBranch.querySelector('.remove-branch-btn');
            if (firstBranchRemoveBtn) {
                firstBranchRemoveBtn.style.display = 'inline-block';
            }
        };
        
        // Add event listener to the first branch's remove button
        const firstBranch = document.querySelector('.branch-form:first-child');
        if (firstBranch) {
            const removeBtn = firstBranch.querySelector('.remove-branch-btn');
            if (removeBtn) {
                removeBtn.onclick = function() {
                    // Only remove if there are other branches
                    const branches = document.querySelectorAll('.branch-form');
                    if (branches.length > 1) {
                        firstBranch.remove();
                    }
                };
            }
        }
    });
</script>
@endsection
