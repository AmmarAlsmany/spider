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
                                    <label for="equipment_type" class="form-label">Equipment Type <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="equipment_type" name="equipment_type" required>
                                    <div class="invalid-feedback">Please enter the equipment type</div>
                                </div>
                                <div class="col-12">
                                    <label for="equipment_model" class="form-label">Equipment Model <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="equipment_model" name="equipment_model" required>
                                    <div class="invalid-feedback">Please enter the equipment model</div>
                                </div>
                                <div class="col-12">
                                    <label for="equipment_quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="equipment_quantity" name="equipment_quantity" required min="1">
                                    <div class="invalid-feedback">Please enter a valid quantity (minimum 1)</div>
                                </div>
                                <div class="col-12">
                                    <label for="equipment_description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="equipment_description" name="equipment_description" rows="3" required></textarea>
                                    <div class="invalid-feedback">Please enter the equipment description</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handleNextStep()">Next</button>
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
                                    <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                    <div class="invalid-feedback">Please enter the customer name</div>
                                </div>
                                <div class="col-12">
                                    <label for="customer_mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" required>
                                    <div class="invalid-feedback">Please enter a valid Saudi mobile number</div>
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
                                <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="btn btn-primary" onclick="handleNextStep()">Next</button>
                            </div>
                        </div>

                        <div id="test-l-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <h5 class="mb-1">Financial Information</h5>
                            <p class="mb-4">Enter the financial details</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="financial_amount" class="form-label">Unit Price (SAR) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="financial_amount" name="financial_amount" required min="0" step="0.01">
                                    <div class="invalid-feedback">Please enter a valid amount</div>
                                </div>
                                <div class="col-12">
                                    <label for="vat_percentage" class="form-label">VAT Percentage <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="vat_percentage" name="vat_percentage" required min="0" max="100" step="0.01" value="15">
                                    <div class="invalid-feedback">Please enter a valid VAT percentage (0-100)</div>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <p class="mb-1">Subtotal: <strong><span id="subtotal">0.00</span> SAR</strong></p>
                                        <p class="mb-1">VAT Amount: <strong><span id="vat_amount">0.00</span> SAR</strong></p>
                                        <p class="mb-0">Total Amount: <strong><span id="total_amount">0.00</span> SAR</strong></p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="warranty_type" class="form-label">Warranty Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="warranty_type" name="warranty_type" required onchange="toggleWarrantyPeriod(this.value)">
                                        <option value="none">No Warranty</option>
                                        <option value="standard">Standard Warranty</option>
                                        <option value="extended">Extended Warranty</option>
                                    </select>
                                    <div class="invalid-feedback">Please select the warranty type</div>
                                </div>

                                <div class="col-12" id="warranty_period_div" style="display: none;">
                                    <label for="warranty_period" class="form-label">Warranty Period (Months) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="warranty_period" name="warranty_period" min="1" max="60" value="12">
                                    <div class="invalid-feedback">Please enter a valid warranty period (1-60 months)</div>
                                </div>

                                <div class="col-12">
                                    <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="payment_type" name="payment_type" required onchange="toggleInstallments(this.value)">
                                        <option value="postpaid">Postpaid</option>
                                        <option value="prepaid">Prepaid</option>
                                    </select>
                                    <div class="invalid-feedback">Please select the payment type</div>
                                </div>
                                <div class="col-12" id="installments_div" style="display: none;">
                                    <label for="number_of_installments" class="form-label">Number of Installments <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="number_of_installments" name="number_of_installments" value="3" min="2" max="12">
                                    <div class="invalid-feedback">Please enter a valid number of installments (minimum 2)</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i class='bx bx-left-arrow-alt me-2'></i>Previous</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var stepper;
    var currentStep = 0;

    document.addEventListener('DOMContentLoaded', function () {
        stepper = new Stepper(document.querySelector('#stepper1'), {
            linear: true,
            animation: true
        });
    });

    async function validateEquipmentInfo() {
        const form = document.getElementById('contractForm');
        const requiredFields = [
            { name: 'equipment_type', message: 'Please enter equipment type' },
            { name: 'equipment_model', message: 'Please enter equipment model' },
            { name: 'equipment_quantity', pattern: /^[1-9]\d*$/, message: 'Please enter a valid quantity (minimum 1)' },
            { name: 'equipment_description', message: 'Please enter equipment description' }
        ];

        let isValid = true;
        let errorMessage = '';

        for (const field of requiredFields) {
            const input = form.querySelector(`[name="${field.name}"]`);
            if (!input.value) {
                isValid = false;
                errorMessage = field.message;
                input.classList.add('is-invalid');
                break;
            }
            if (field.pattern && !field.pattern.test(input.value)) {
                isValid = false;
                errorMessage = field.message;
                input.classList.add('is-invalid');
                break;
            }
            input.classList.remove('is-invalid');
        }

        if (!isValid) {
            await Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
        }

        return isValid;
    }

    async function validateCustomerInfo() {
        const form = document.getElementById('contractForm');
        if (form.querySelector('[name="client_id"]')) {
            return true; // Skip validation for existing client
        }

        const requiredFields = [
            { name: 'customer_name', message: 'Please enter customer name' },
            { name: 'customer_mobile', pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/, message: 'Please enter a valid Saudi mobile number' },
            { name: 'customer_email', pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: 'Please enter a valid email address' },
            { name: 'customer_address', message: 'Please enter customer address' },
            { name: 'customer_city', message: 'Please select customer city' }
        ];

        let isValid = true;
        let errorMessage = '';

        for (const field of requiredFields) {
            const input = form.querySelector(`[name="${field.name}"]`);
            if (!input.value) {
                isValid = false;
                errorMessage = field.message;
                input.classList.add('is-invalid');
                break;
            }
            if (field.pattern && !field.pattern.test(input.value)) {
                isValid = false;
                errorMessage = field.message;
                input.classList.add('is-invalid');
                break;
            }
            input.classList.remove('is-invalid');
        }

        if (!isValid) {
            await Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
        }

        return isValid;
    }

    async function validateFinancialInfo() {
        const form = document.getElementById('contractForm');
        const requiredFields = [
            { name: 'financial_amount', pattern: /^[0-9]+(\.[0-9]{1,2})?$/, message: 'Please enter a valid amount' },
            { name: 'vat_percentage', pattern: /^[0-9]+(\.[0-9]{1,2})?$/, message: 'Please enter a valid VAT percentage' },
            { name: 'warranty_type', message: 'Please select warranty type' }
        ];

        let isValid = true;
        let errorMessage = '';

        for (const field of requiredFields) {
            const input = form.querySelector(`[name="${field.name}"]`);
            if (!input.value) {
                isValid = false;
                errorMessage = field.message;
                input.classList.add('is-invalid');
                break;
            }
            if (field.pattern && !field.pattern.test(input.value)) {
                isValid = false;
                errorMessage = field.message;
                input.classList.add('is-invalid');
                break;
            }
            input.classList.remove('is-invalid');
        }

        // Additional validation for warranty period if warranty type is not 'none'
        const warrantyType = form.querySelector('[name="warranty_type"]').value;
        if (warrantyType !== 'none') {
            const warrantyPeriod = form.querySelector('[name="warranty_period"]');
            if (!warrantyPeriod.value || parseInt(warrantyPeriod.value) < 1 || parseInt(warrantyPeriod.value) > 60) {
                isValid = false;
                errorMessage = 'Please enter a valid warranty period (1-60 months)';
                warrantyPeriod.classList.add('is-invalid');
            }
        }

        // Additional validation for installments if payment type is prepaid
        const paymentType = form.querySelector('[name="payment_type"]').value;
        if (paymentType === 'prepaid') {
            const installments = form.querySelector('[name="number_of_installments"]');
            if (!installments.value || parseInt(installments.value) < 1) {
                isValid = false;
                errorMessage = 'Please enter a valid number of installments (minimum 1)';
                installments.classList.add('is-invalid');
            }
        }

        if (!isValid) {
            await Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
        }

        return isValid;
    }

    async function handleNextStep() {
        let isValid = true;

        switch(currentStep) {
            case 0:
                isValid = await validateEquipmentInfo();
                break;
            case 1:
                isValid = await validateCustomerInfo();
                break;
            case 2:
                isValid = await validateFinancialInfo();
                break;
        }

        if (isValid) {
            currentStep++;
            stepper.next();
        }
    }

    function handlePreviousStep() {
        currentStep--;
        stepper.previous();
    }

    function toggleWarrantyPeriod(value) {
        const warrantyPeriodDiv = document.getElementById('warranty_period_div');
        if (value === 'none') {
            warrantyPeriodDiv.style.display = 'none';
        } else {
            warrantyPeriodDiv.style.display = 'block';
        }
    }

    function calculateTotal() {
        const amount = parseFloat(document.getElementById('financial_amount').value) || 0;
        const quantity = parseInt(document.getElementById('equipment_quantity').value) || 0;
        const vatPercentage = parseFloat(document.getElementById('vat_percentage').value) || 0;
        
        const subtotal = amount * quantity;
        const vatAmount = subtotal * (vatPercentage / 100);
        const total = subtotal + vatAmount;
        
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('vat_amount').textContent = vatAmount.toFixed(2);
        document.getElementById('total_amount').textContent = total.toFixed(2);
    }

    // Add event listeners for amount calculations
    document.addEventListener('DOMContentLoaded', function() {
        const calcInputs = ['financial_amount', 'equipment_quantity', 'vat_percentage'];
        calcInputs.forEach(id => {
            document.getElementById(id)?.addEventListener('input', calculateTotal);
        });
    });
</script>
@endsection
