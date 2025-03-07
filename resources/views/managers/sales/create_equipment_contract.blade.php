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
                                <h5 class="mb-0 steper-title">Payment Information</h5>
                                <p class="mb-0 steper-sub-title">Enter payment details and schedule</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-4">
                        <div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="test-l-4">
                            <div class="bs-stepper-circle">4</div>
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
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
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
                                <div class="step-number">2</div>
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
                            <h5 class="mb-1">Payment Information</h5>
                            <p class="mb-4">Enter payment information and the number of payments</p>

                            <div class="row g-3" x-data="{
                                contractAmount: 0,
                                payment_type: 'prepaid',
                                payment_schedule: 'monthly',
                                numberOfPayments: 1,
                                first_payment_date: '',
                                initPaymentDates() {
                                    if (this.payment_schedule === 'monthly') {
                                        // Clear custom payment date fields when switching to monthly
                                        const container = document.getElementById('custom-payment-dates');
                                        if (container) {
                                            container.innerHTML = '';
                                        }
                                    } else if (this.payment_schedule === 'custom') {
                                        generateCustomPaymentDateFields(this.numberOfPayments);
                                    }
                                }
                            }" x-init="$watch('numberOfPayments', value => initPaymentDates()); $watch('payment_schedule', value => initPaymentDates())">
                                <div class="col-12 col-lg-6">
                                    <label for="Contractamount" class="form-label">Contract Amount (without VAT) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="contractamount" id="Contractamount"
                                        x-model="contractAmount" required min="1" step="0.01">
                                    <div class="invalid-feedback">Please enter a valid contract amount</div>
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
                                    <div class="col-12 col-lg-6">
                                        <label for="number_of_payments" class="form-label">Number of Payments <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="number_of_payments" id="number_of_payments"
                                            x-model="numberOfPayments" required min="1" max="24">
                                        <div class="invalid-feedback">Please enter a valid number of payments (1-24)</div>
                                    </div>
                                </template>

                                <template x-if="payment_type === 'postpaid'">
                                    <div class="col-12 col-lg-6">
                                        <label for="payment_schedule" class="form-label">Payment Schedule <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_schedule" id="payment_schedule" x-model="payment_schedule" required>
                                            <option value="monthly">Monthly</option>
                                            <option value="custom">Custom Dates</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a payment schedule</div>
                                    </div>
                                </template>

                                <div class="col-12 col-lg-6">
                                    <label for="first_payment_date" class="form-label">First Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="first_payment_date" id="first_payment_date"
                                        x-model="first_payment_date" required min="{{ date('Y-m-d') }}">
                                    <div class="invalid-feedback">Please select a valid payment date</div>
                                </div>

                                <template x-if="payment_type === 'postpaid' && payment_schedule === 'custom'">
                                    <div class="col-12">
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
                                            <div class="col-6 text-end" x-text="'SAR ' + (Number(contractAmount) * 0.15).toFixed(2)"></div>
                                            <div class="col-6"><strong>Total Amount:</strong></div>
                                            <div class="col-6 text-end"><strong x-text="'SAR ' + (Number(contractAmount) * 1.15).toFixed(2)"></strong></div>
                                            <template x-if="payment_type === 'postpaid'">
                                                <div class="mt-2 col-12">
                                                    <div class="row">
                                                        <div class="col-6">Payment per Installment:</div>
                                                        <div class="col-6 text-end" x-text="'SAR ' + (Number(contractAmount) * 1.15 / numberOfPayments).toFixed(2)"></div>
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

                        <div id="test-l-4" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger4">
                            <div class="step-indicator">
                                <div class="step-number">4</div>
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
        const fields = [
            { id: 'Contractamount', regex: new RegExp('^[0-9]+(\\.[0-9]{1,2})?$') },
            { id: 'payment_type', regex: new RegExp('.+') }
        ];

        const paymentType = document.getElementById('payment_type').value;
        if (paymentType === 'postpaid') {
            fields.push({ id: 'number_of_payments', regex: new RegExp('^([1-9]|1[0-2])$') });
        }

        return fields.every(field => {
            const element = document.getElementById(field.id);
            if (!element) return true;
            const isValid = field.regex.test(element.value);
            element.classList.toggle('is-invalid', !isValid);
            return isValid;
        });
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
                return true; // Summary step, no validation needed
            default:
                return false;
        }
    }

    function handleNextStep() {
        if (validateCurrentStep()) {
            currentStep++;
            stepper.next();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields correctly.'
            });
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

        // Initialize AlpineJS data
        window.initPaymentDates = function() {
            const numberOfPayments = this.numberOfPayments;
            const schedule = this.payment_schedule;
            
            if (numberOfPayments > 0) {
                this.paymentDates = [];
                for (let i = 0; i < numberOfPayments; i++) {
                    if (schedule === 'monthly') {
                        this.paymentDates.push(new Date(Date.now() + (i * 30 * 24 * 60 * 60 * 1000)).toISOString().split('T')[0]);
                    } else if (schedule === 'quarterly') {
                        this.paymentDates.push(new Date(Date.now() + (i * 90 * 24 * 60 * 60 * 1000)).toISOString().split('T')[0]);
                    }
                }
            }
        };
    });
</script>
@endsection
