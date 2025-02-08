@extends('shared.dashboard')
@section('content')
@php
$branch_id = $branches;
if ($branch_id > 1) {
// if the branch is more than 1
$data_target_payment = '#test-l-4';
$aria_control_payment = 'test-l-4';
$id_payment = 'stepper1trigger4';
$number_payment = 4;
// summary will be the last step
$data_target_summery = '#test-l-5';
$aria_control_summery = 'test-l-5';
$id_summery = 'stepper1trigger5';
$number_summery = 5;
} else {
// if the branch is 1 or 0 / payment information will be befor the last step
$data_target_payment = '#test-l-3';
$aria_control_payment = 'test-l-3';
$id_payment = 'stepper1trigger3';
$number_payment = 3;
// summary will be the last step
$data_target_summery = '#test-l-4';
$aria_control_summery = 'test-l-4';
$id_summery = 'stepper1trigger4';
$number_summery = 4;
}
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
    <div id="stepper1" class="bs-stepper">
        <div class="card">
            <div class="card-header">
                <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
                    <div class="step" data-target="#test-l-1">
                        <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                            <div class="bs-stepper-circle">1</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Personal information</h5>
                                <p class="mb-0 steper-sub-title">Enter customer information</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="#test-l-2">
                        <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                            <div class="bs-stepper-circle">2</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Contract Details</h5>
                                <p class="mb-0 steper-sub-title">Setup Contract Details</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    @if ($branches > 1)
                    <div class="step" data-target="#test-l-3">
                        <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                            <div class="bs-stepper-circle">3</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Branch information</h5>
                                <p class="mb-0 steper-sub-title">Branch Details</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="{{ $data_target_payment }}">
                        <div class="step-trigger" role="tab" id="{{ $id_payment }}"
                            aria-controls="{{ $aria_control_payment }}">
                            <div class="bs-stepper-circle">{{ $number_payment }}</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">payment information</h5>
                                <p class="mb-0 steper-sub-title">payment Details</p>
                            </div>
                        </div>
                    </div>
                    <div class="bs-stepper-line"></div>
                    <div class="step" data-target="{{ $data_target_summery }}">
                        <div class="step-trigger" role="tab" id="{{ $id_summery }}"
                            aria-controls="{{ $aria_control_summery }}">
                            <div class="bs-stepper-circle">{{ $number_summery }}</div>
                            <div class="">
                                <h5 class="mb-0 steper-title">Summary</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="bs-stepper-content">
                    <form action="{{ route('contract.create') }}" method="POST" id="contractForm"
                        onsubmit="return validateForm(event)">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="contract_type_id" value="{{ $contract_type_id->id }}">
                        <input type="text" name="is_multi_branch" @if ($branches> 1) value="yes" @else value="no" @endif
                        hidden readonly>
                        <input type="number" id="branchs_number" name="branchs_number" value={{ $branches }} hidden
                            readonly>
                        <input type="hidden" name="Property_type" value="{{ $contract_type_id->name }}">
                        <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                            <h5 class="mb-1">Client Personal Information</h5>
                            <p class="mb-4">Enter the Client personal information to get closer Your Deal</p>
                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <label for="FullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="clientName" id="FullName"
                                        placeholder="Full Name" @isset($client_info) value="{{ $client_info->name }}"
                                        readonly @endisset>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="LastName" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" name="clientMobile" id="LastName"
                                        placeholder="0500000000" @isset($client_info) value="{{ $client_info->mobile }}"
                                        readonly @endisset>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="EmailAddress" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="clientEmail" id="EmailAddress"
                                        placeholder="example@domain.com" @isset($client_info)
                                        value="{{ $client_info->email }}" readonly @endisset>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="PhoneNumber" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="clientPhone" id="PhoneNumber"
                                        placeholder="Phone Number" @isset($client_info)
                                        value="{{ $client_info->phone }}" readonly @endisset>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="taxNumber" class="form-label">Tax Number</label>
                                    <input type="number" min="0" class="form-control" name="client_tax_number"
                                        id="taxNumber" placeholder="Enter Tax Number" @isset($client_info)
                                        value="{{ $client_info->tax_number }}" readonly @endisset>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="zipCode" class="form-label">Zip Code</label>
                                    <input type="number" min="0" class="form-control" name="client_zipcode" id="zipCode"
                                        placeholder="Enter Zip Code" @isset($client_info)
                                        value="{{ $client_info->zip_code }}" readonly @endisset>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="clientCity" class="form-label">City</label>
                                    <select class="form-select" name="client_city" id="clientCity"
                                        aria-label="Default select example" @isset($client_info)
                                        value="{{ $client_info->city }}" readonly @endisset>
                                        <option value="">Select City</option>
                                        @foreach($saudiCities as $city)
                                        <option value="{{ $city }}" @isset($client_info) {{ $client_info->city == $city
                                            ? 'selected' : '' }} @endisset>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="Address" class="form-label">Address</label>
                                    <input type="text" class="form-control" name="clientAddress" id="Address"
                                        placeholder="Address" @isset($client_info) value="{{ $client_info->address }}"
                                        readonly @endisset>
                                </div>
                                <div class="mb-3 col-12">
                                    <button type="button" class="px-4 btn btn-primary"
                                        onclick="handleNextStep();">Next<i
                                            class='bx bx-right-arrow-alt ms-2'></i></button>
                                </div>
                            </div>
                            <!---end row-->

                        </div>
                        <div id="test-l-2" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger2">

                            <h5 class="mb-1">Contracts Details</h5>
                            <p class="mb-4">Enter Your Contract Details.</p>

                            <div class="row g-3" x-data="{
                                    branchCount: {{ $branches }},
                                }">

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label for="contractNumber" class="form-label">Contract Number</label>
                                        <input type="text" class="form-control" name="contractnumber"
                                            id="contractNumber" placeholder="123.." value="{{ $contract_number }}"
                                            readonly>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="contractstartdate" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="contractstartdate"
                                            id="contractstartdate" required>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="contractenddate" class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="contractenddate"
                                            id="contractenddate" required>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="contracttype" class="form-label">Contract Type</label>
                                        <input type="text" class="form-control" name="contracttype" id="contracttype"
                                            value="{{ $contract_type_id->name }}" readonly>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="Property_type" class="form-label">Property type</label>
                                        <select type="text" class="form-select" name="Property_type" id="Property_type">
                                            <option disabled>Select One</option>
                                            <option value="Residential">Residential</option>
                                            <option value="Commercial">Commercial</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="contractDescription" class="form-label">Contract
                                            Description <span class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <textarea class="form-control" id="contractDescription"
                                                name="contract_description" placeholder="Enter contract description..."
                                                rows="3" required minlength="10" oninput="validateDescription(this)"
                                                onblur="validateDescription(this)"></textarea>
                                            <div class="invalid-feedback">
                                                Please enter a description (minimum 10 characters)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="number_of_visits" class="form-label">Number of Visits</label>
                                        <input type="number" class="form-control" name="number_of_visits"
                                            id="number_of_visits" min="1">
                                        <div class="form-text">Required for service contracts, not required for equipment purchase</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="warrantyperiod" class="form-label">Warranty Period (Months)</label>
                                        <input type="number" class="form-control" name="warrantyperiod"
                                            id="warrantyperiod" min="1" placeholder="Enter number of months">
                                        <div class="form-text">Please specify the warranty duration in months</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="gap-3 d-flex align-items-center">
                                            <button type="button" class="px-4 btn btn-outline-secondary"
                                                onclick="stepper.previous()"><i
                                                    class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                            <button type="button" class="px-4 btn btn-primary"
                                                onclick="handleNextStep();">Next<i
                                                    class='bx bx-right-arrow-alt ms-2'></i></button>
                                        </div>
                                    </div>
                                </div>
                                <!---end row-->

                            </div>
                        </div>
                        @if ($branches > 1)
                        <div id="test-l-3" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger3">
                            <h5 class="mb-1">Branchs Information</h5>
                            <p class="mb-4">Informing the company of branch information</p>
                            @for ($i = 0; $i < $branches; $i++) <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-1">Branch {{ $i + 1 }}</h5>
                                    <p class="mb-4">Informing the company of branch information</p>
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label for="branchName {{ $i }}" class="form-label">Branch
                                                Name</label>
                                            <input type="text" class="form-control" name="branchName{{ $i }}"
                                                id="branchName{{ $i }}" placeholder="Branch Name">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchmanager {{ $i }}" class="form-label">Branch
                                                Manager
                                                Name</label>
                                            <input type="text" class="form-control" name="branchmanager{{ $i }}"
                                                id="branchmanager{{ $i }}" placeholder="Branch Manager Name">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchphone {{ $i }}" class="form-label">Branch
                                                Manager
                                                Phone</label>
                                            <input type="text" class="form-control" name="branchphone{{ $i }}"
                                                id="branchphone{{ $i }}" placeholder="Branch phone{{ $i }}">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchAdress" class="form-label">Branch
                                                Address</label>
                                            <input class="form-control" name="branchadress{{ $i }}"
                                                id="branchAdress{{ $i }}" placeholder="Branch Address"
                                                aria-label="Default select example">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchcountry" class="form-label">City</label>
                                            <select class="form-select" name="branchcity{{ $i }}"
                                                id="branchcountry{{ $i }}" aria-label="Default select example"
                                                @isset($client_id) value="{{ $client_id->city }}" readonly @endisset>
                                                <option value="" selected disabled>Select a city</option>
                                                @foreach ($saudiCities as $city)
                                                <option value="{{ $city }}" @isset($client_id) {{ $client_id->city ==
                                                    $city
                                                    ? 'selected' : '' }} @endisset>
                                                    {{ $city }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @endfor
                        <div class="col-12">
                            <div class="gap-3 d-flex align-items-center">
                                <button type="button" class="px-4 btn btn-outline-secondary"
                                    onclick="stepper.previous()"><i
                                        class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="px-4 btn btn-primary" onclick="handleNextStep();">Next<i
                                        class='bx bx-right-arrow-alt ms-2'></i></button>
                            </div>
                        </div>
                </div>
                @endif
                <div id="{{ $aria_control_payment }}" role="tabpanel" class="bs-stepper-pane"
                    aria-labelledby="{{ $id_payment }}">
                    <h5 class="mb-1">Payment Information</h5>
                    <p class="mb-4">Enter payment information and the number of payments</p>

                    <div class="row g-3" x-data="{
                        paymentType: '',
                        contractAmount: 0,
                        numberOfPayments: 1,
                        payment_method: '',
                        first_payment_date: '',
                        payment_schedule: '',
                        payment_dates: {},
                        calculateVat() {
                            return (this.contractAmount * 0.15).toFixed(2);
                        },
                        calculateTotal() {
                            return (this.contractAmount * 1.15).toFixed(2);
                        },
                        calculatePerPayment() {
                            if (this.numberOfPayments > 0) {
                                return (this.contractAmount * 1.15 / this.numberOfPayments).toFixed(2);
                            }
                            return '0.00';
                        },
                        initPaymentDates() {
                            if (this.payment_schedule === 'custom') {
                                for (let i = 1; i <= this.numberOfPayments; i++) {
                                    if (!this.$data['payment_date_'+i]) {
                                        this.$data['payment_date_'+i] = '';
                                    }
                                }
                            }
                        }
                    }" x-init="$watch('numberOfPayments', value => initPaymentDates())" x-effect="initPaymentDates()">
                        <div class="col-12 col-lg-6">
                            <label for="Contractamount" class="form-label">Contract Amount (without VAT)</label>
                            <input type="number" class="form-control" name="contractamount" id="Contractamount"
                                x-model="contractAmount" required>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="paymentType" class="form-label">Payment Type</label>
                            <select class="form-select" name="payment_type" id="paymentType" x-model="paymentType"
                                required>
                                <option value="">Select Payment Type</option>
                                <option value="prepaid">Prepaid</option>
                                <option value="postpaid">Postpaid</option>
                            </select>
                        </div>

                        <template x-if="paymentType === 'postpaid'">
                            <div class="col-12">
                                <div class="p-3 mb-3 rounded border">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="numberOfPayments" class="form-label">Number of Payments</label>
                                            <input type="number" class="form-control" name="number_of_payments"
                                                id="numberOfPayments" x-model="numberOfPayments" min="1" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="firstPaymentDate" class="form-label">First Payment Date</label>
                                            <input type="date" class="form-control" name="first_payment_date"
                                                id="firstPaymentDate" x-model="first_payment_date" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="paymentSchedule" class="form-label">Payment Schedule</label>
                                            <select class="form-select" name="payment_schedule" id="paymentSchedule"
                                                x-model="payment_schedule" required>
                                                <option value="">Select Schedule</option>
                                                <option value="monthly">Monthly (Automatic)</option>
                                                <option value="custom">Custom Payment Dates</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="paymentType === 'postpaid'">
                            <div class="col-12">
                                <div class="p-3 rounded border">
                                    <h6 class="mb-3">Payment Summary</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <p class="mb-1">VAT Amount (15%)</p>
                                            <h5 x-text="calculateVat() + ' SAR'"></h5>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1">Total Amount (with VAT)</p>
                                            <h5 x-text="calculateTotal() + ' SAR'"></h5>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-1">Amount Per Payment</p>
                                            <h5 x-text="calculatePerPayment() + ' SAR'"></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="paymentType === 'postpaid' && payment_schedule === 'custom'">
                            <div class="col-12">
                                <div class="p-3 mb-3 rounded border">
                                    <h6 class="mb-3">Payment Schedule</h6>
                                    <div class="row g-3">
                                        <div class="mb-2 col-md-12">
                                            <div class="alert alert-info">
                                                Payment 1 will use the First Payment Date: <strong
                                                    x-text="first_payment_date"></strong>
                                            </div>
                                        </div>
                                        <template x-for="index in parseInt(numberOfPayments)" :key="index">
                                            <template x-if="index > 1">
                                                <div class="col-md-4">
                                                    <label :for="'payment_date_'+index" class="form-label"
                                                        x-text="'Payment ' + index + ' Date'"></label>
                                                    <input type="date" class="form-control"
                                                        :name="'payment_date_'+index" :id="'payment_date_'+index"
                                                        :min="first_payment_date" x-model="$data['payment_date_'+index]"
                                                        required>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="col-12">
                            <div class="gap-3 d-flex align-items-center">
                                <button type="button" class="px-4 btn btn-outline-secondary"
                                    onclick="stepper.previous()">
                                    <i class='bx bx-left-arrow-alt me-2'></i>Previous
                                </button>
                                <button type="button" class="px-4 btn btn-primary" onclick="handleNextStep()">
                                    Next<i class='bx bx-right-arrow-alt ms-2'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="{{ $aria_control_summery }}" role="tabpanel" class="bs-stepper-pane"
                    aria-labelledby="{{ $id_summery }}">
                    <h5 class="mb-1">Summary</h5>
                    <p class="mb-4">Review the information you entered</p>

                    <div class="row g-3">
                        <div class="col-12">
                            <h5 class="mb-4">Contract Summary</h5>
                            <div id="contract-summary">
                                <!-- Summary content will be dynamically inserted here -->
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary"
                                    onclick="stepper.previous()">Edit</button>
                                <button type="button" class="btn btn-danger" onclick="cancelContract()">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Contract</button>
                            </div>
                        </div>
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
    var currentStep = 0;
    var branchCount = document.getElementById('branchs_number')?.value || 0;
    var stepper = new Stepper(document.querySelector('#stepper1'), {
        linear: false,
        animation: true
    });

    function getTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function validateForm(event) {
        event.preventDefault(); // Prevent default form submission
        
        let isValid = true;
        const form = document.getElementById('contractForm');
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        // Reset all validations
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
            input.classList.remove('is-valid');
        });
        
        // Validate all required fields
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
                return;
            }
            
            switch(input.name) {
                case 'clientPhone':
                    if (!validatePhoneNumber(input)) isValid = false;
                    break;
                case 'first_payment_date':
                    if (!validateDate({ target: input })) isValid = false;
                    break;
                case 'clientEmail':
                    if (!validateEmail(input)) isValid = false;
                    break;
                case 'contract_description':
                    if (!validateDescription(input)) isValid = false;
                    break;
                default:
                    input.classList.add('is-valid');
            }
        });

        if (isValid) {
            // Double check the contract description
            const description = form.querySelector('[name="contract_description"]');
            if (!description || !description.value.trim()) {
                description.classList.add('is-invalid');
                isValid = false;
            }
        }

        if (!isValid) {
            // Find the first invalid input and focus it
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                // Find the step containing this field and activate it
                const step = firstInvalid.closest('.bs-stepper-pane');
                if (step) {
                    const stepId = step.id;
                    stepper.to(stepId);
                }
            }
            return false;
        }

        // If everything is valid, submit the form
        form.submit();
        return true;
    }

    function validateDescription(input) {
        const minLength = 10;
        const value = input.value.trim();
        
        if (!value || value.length < minLength) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        }
    }

    function handleNextStep() {
        const currentPane = document.querySelector('.bs-stepper-pane.active');
        if (!currentPane) return;

        // Get all required fields in current step
        const requiredFields = currentPane.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        // Reset validation state
        requiredFields.forEach(field => {
            field.classList.remove('is-invalid');
            field.classList.remove('is-valid');
        });

        // Validate each field
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                return;
            }

            switch(field.name) {
                case 'clientPhone':
                    if (!validatePhoneNumber(field)) isValid = false;
                    break;
                case 'first_payment_date':
                    if (!validateDate({ target: field })) isValid = false;
                    break;
                case 'clientEmail':
                    if (!validateEmail(field)) isValid = false;
                    break;
                case 'contract_description':
                    if (!validateDescription(field)) isValid = false;
                    break;
                default:
                    field.classList.add('is-valid');
            }
        });

        if (!isValid) {
            // Focus the first invalid field
            const firstInvalid = currentPane.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return false;
        }

        // If all valid, proceed to next step
        stepper.next();
        
        // Generate summary if moving to summary step
        const nextPane = document.querySelector('.bs-stepper-pane.active');
        if (nextPane && nextPane.id === '{{ $aria_control_summery }}') {
            generateSummary();
        }
    }

    // Add event listeners when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize validation for all required fields
        const form = document.getElementById('contractForm');
        const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                switch(this.name) {
                    case 'clientPhone':
                        validatePhoneNumber(this);
                        break;
                    case 'first_payment_date':
                        validateDate({ target: this });
                        break;
                    case 'clientEmail':
                        validateEmail(this);
                        break;
                    case 'contract_description':
                        validateDescription(this);
                        break;
                    default:
                        if (this.value.trim()) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                        } else {
                            this.classList.remove('is-valid');
                            this.classList.add('is-invalid');
                        }
                }
            });
        });

        const propertyType = document.querySelector('input[name="Property_type"]').value;
        const numberofvisits = document.getElementById('numberofvisits');
        
        function updateVisitsRequirement() {
            if (propertyType.toLowerCase().includes('buy') || propertyType.toLowerCase().includes('equipment')) {
                numberofvisits.removeAttribute('required');
            } else {
                numberofvisits.setAttribute('required', 'required');
            }
        }
        
        // Initial check
        updateVisitsRequirement();
    });

    function validatePhoneNumber(input) {
        const phoneRegex = /^[0-9]{10}$/;
        const isValid = phoneRegex.test(input.value);
        
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            return false;
        }
    }

    function validateEmail(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(input.value);
        
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            return false;
        }
    }

    function validateDate(event) {
        const input = event.target;
        const selectedDate = new Date(input.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        }
    }

    function generateSummary() {
        const summary = document.getElementById('contract-summary');
        if (!summary) return;

        // Get client information
        const fullName = document.getElementById('FullName')?.value || '';
        const mobile = document.getElementById('LastName')?.value || '';
        const phone = document.getElementById('PhoneNumber')?.value || '';
        
        // Get contract details
        const contractNumber = document.getElementById('contractNumber')?.value || '';
        const contractAmount = document.getElementById('Contractamount')?.value || '0';
        const vat = parseFloat(contractAmount) * 0.15;
        const totalAmount = parseFloat(contractAmount) + vat;
        
        // Get payment information
        const paymentType = document.querySelector('select[name="payment_type"]')?.value || '';
        let numberOfPayments = '';
        
        // Only get number of payments if payment type is postpaid
        if (paymentType === 'postpaid') {
            numberOfPayments = document.querySelector('[x-model="numberOfPayments"]')?.value || '1';
        }

        // Get branch information if multiple branches
        let branchesHtml = '';
        if (branchCount > 1) {
            const branchRows = [];
            for (let i = 0; i < branchCount; i++) {
                const branchName = document.getElementById(`branchName${i}`)?.value || '';
                const branchManager = document.getElementById(`branchmanager${i}`)?.value || '';
                const branchPhone = document.getElementById(`branchphone${i}`)?.value || '';
                const branchAddress = document.getElementById(`branchadress${i}`)?.value || '';
                const branchCity = document.getElementById(`branchcity${i}`)?.value || '';

                branchRows.push(`
                    <tr>
                        <th colspan="2" class="bg-light">Branch ${i + 1}</th>
                    </tr>
                    <tr>
                        <td>Branch Name</td>
                        <td>${branchName}</td>
                    </tr>
                    <tr>
                        <td>Branch Manager</td>
                        <td>${branchManager}</td>
                    </tr>
                    <tr>
                        <td>Branch Phone</td>
                        <td>${branchPhone}</td>
                    </tr>
                    <tr>
                        <td>Branch Address</td>
                        <td>${branchAddress}</td>
                    </tr>
                    <tr>
                        <td>Branch City</td>
                        <td>${branchCity}</td>
                    </tr>
                `);
            }
            if (branchRows.length > 0) {
                branchesHtml = `
                    <tr>
                        <th colspan="2" class="bg-light">Branch Information</th>
                    </tr>
                    ${branchRows.join('')}
                `;
            }
        }

        summary.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <th colspan="2" class="bg-light">Client Information</th>
                                </tr>
                                <tr>
                                    <td>Full Name</td>
                                    <td>${fullName}</td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td>${mobile}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>${phone}</td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="bg-light">Contract Details</th>
                                </tr>
                                <tr>
                                    <td>Contract Number</td>
                                    <td>${contractNumber}</td>
                                </tr>
                                <tr>
                                    <td>Contract Amount</td>
                                    <td>SAR ${parseFloat(contractAmount).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td>VAT (15%)</td>
                                    <td>SAR ${vat.toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td>Total Amount</td>
                                    <td>SAR ${totalAmount.toFixed(2)}</td>
                                </tr>
                                ${branchesHtml}
                                <tr>
                                    <th colspan="2" class="bg-light">Payment Information</th>
                                </tr>
                                <tr>
                                    <td>Payment Type</td>
                                    <td>${paymentType}</td>
                                </tr>
                                ${paymentType === 'postpaid' ? `
                                <tr>
                                    <td>Number of Payments</td>
                                    <td>${numberOfPayments}</td>
                                </tr>
                                ` : ''}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    function cancelContract() {
        if (confirm('Are you sure you want to cancel this contract? All entered data will be lost.')) {
            // Reset the form
            document.getElementById('contractForm').reset();
            
            // Reset the stepper to the first step
            stepper.to(1);
            
            // Clear any validation messages or errors
            clearValidationMessages();
            
            // Clear the contract summary if it exists
            const summaryDiv = document.getElementById('contract-summary');
            if (summaryDiv) {
                summaryDiv.innerHTML = '';
            }
        }
    }
    
    function clearValidationMessages() {
        // Remove any error messages and validation classes
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(message => message.remove());
        
        const invalidInputs = document.querySelectorAll('.is-invalid');
        invalidInputs.forEach(input => input.classList.remove('is-invalid'));
    }
</script>
@endsection