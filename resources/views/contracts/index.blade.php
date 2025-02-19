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
$property_types = [
    'Residential',
    'Commercial',
];
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
                                    <label for="FullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="clientName" id="FullName"
                                        placeholder="Full Name" required
                                        @isset($client_info) value="{{ $client_info->name }}" @endisset>
                                    <div class="invalid-feedback">Please enter client's full name</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="Phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="clientPhone" id="Phone"
                                        placeholder="05xxxxxxxx" pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required
                                        @isset($client_info) value="{{ $client_info->phone }}" @endisset>
                                    <div class="invalid-feedback">Please enter a valid Saudi phone number</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="Mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="clientMobile" id="Mobile"
                                        placeholder="05xxxxxxxx" pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required
                                        @isset($client_info) value="{{ $client_info->mobile }}" @endisset>
                                    <div class="invalid-feedback">Please enter a valid Saudi mobile number</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="Email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="clientEmail" id="Email"
                                        placeholder="example@domain.com" required
                                        @isset($client_info) value="{{ $client_info->email }}" @endisset>
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="taxNumber" class="form-label">Tax Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="client_tax_number" id="taxNumber"
                                        placeholder="Enter Tax Number" pattern="^[0-9]{15}$" required
                                        @isset($client_info) value="{{ $client_info->tax_number }}" @endisset>
                                    <div class="invalid-feedback">Please enter a valid 15-digit tax number</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="zipCode" class="form-label">Zip Code</label>
                                    <input type="text" class="form-control" name="client_zipcode" id="zipCode"
                                        placeholder="Enter Zip Code" pattern="^[0-9]{5}$"
                                        @isset($client_info) value="{{ $client_info->zip_code }}" @endisset>
                                    <div class="invalid-feedback">Please enter a valid 5-digit zip code</div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="City" class="form-label">City <span class="text-danger">*</span></label>
                                    <select class="form-select" name="clientCity" id="City" required>
                                        <option value="">Select City</option>
                                        @foreach($saudiCities as $city)
                                            <option value="{{ $city }}" @isset($client_info) @if($client_info->city == $city) selected @endif @endisset>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a city</div>
                                </div>
                                <div class="col-12">
                                    <label for="Address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="clientAddress" id="Address" rows="3"
                                        placeholder="Enter full address" required>@isset($client_info){{ $client_info->address }}@endisset</textarea>
                                    <div class="invalid-feedback">Please enter the client's address</div>
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
                                        <label for="contractNumber" class="form-label">Contract Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="contractnumber"
                                            id="contractNumber" placeholder="123.." value="{{ $contract_number }}"
                                            readonly>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="contractstartdate" class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="contractstartdate"
                                            id="contractstartdate" required>
                                        <div class="invalid-feedback">Please select a start date</div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="contractenddate" class="form-label">End Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="contractenddate"
                                            id="contractenddate" required>
                                        <div class="invalid-feedback">Please select an end date that's after the start date</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="contract_type_id" class="form-label">Contract Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="contract_type_id" id="contract_type_id" required>
                                            @foreach($contract_types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a contract type</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="Property_type" class="form-label">Property Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="Property_type" id="Property_type" required>
                                            @foreach($property_types as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Please select a property type</div>
                                    </div>

                                    <div class="col-12">
                                        <label for="contractDescription" class="form-label">Contract Description <span class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <textarea class="form-control" id="contractDescription"
                                                name="contract_description" placeholder="Enter contract description..."
                                                rows="3" required minlength="10"></textarea>
                                            <div class="invalid-feedback">
                                                Please enter a description (minimum 10 characters)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="number_of_visits" class="form-label">Number of Visits <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="number_of_visits"
                                            id="number_of_visits" min="1" required value="1">
                                        <div class="invalid-feedback">Please enter at least 1 visit</div>
                                        <div class="form-text">Minimum 1 visit required</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="warranty" class="form-label">Warranty Period (Months) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="warranty"
                                            id="warranty" min="0" required value="0">
                                        <div class="invalid-feedback">Please enter a valid warranty period</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="gap-3 d-flex align-items-center">
                                            <button type="button" class="px-4 btn btn-outline-secondary"
                                                onclick="handlePreviousStep()"><i
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
                                            <label for="branchName{{ $i }}" class="form-label">Branch Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="branchName{{ $i }}"
                                                id="branchName{{ $i }}" placeholder="Branch Name" required>
                                            <div class="invalid-feedback">Please enter the branch name</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchmanager{{ $i }}" class="form-label">Branch Manager Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="branchmanager{{ $i }}"
                                                id="branchmanager{{ $i }}" placeholder="Branch Manager Name" required>
                                            <div class="invalid-feedback">Please enter the branch manager name</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchphone{{ $i }}" class="form-label">Branch Manager Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="branchphone{{ $i }}"
                                                id="branchphone{{ $i }}" placeholder="05xxxxxxxx" required
                                                pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$">
                                            <div class="invalid-feedback">Please enter a valid Saudi phone number (e.g., 05xxxxxxxx)</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchAddress{{ $i }}" class="form-label">Branch Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="branchAddress{{ $i }}"
                                                id="branchAddress{{ $i }}" placeholder="Branch Address" required>
                                            <div class="invalid-feedback">Please enter the branch address</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="branchcity{{ $i }}" class="form-label">City <span class="text-danger">*</span></label>
                                            <select class="form-select" name="branchcity{{ $i }}" id="branchcity{{ $i }}" required>
                                                <option value="">Select City</option>
                                                @foreach($saudiCities as $city)
                                                    <option value="{{ $city }}">{{ $city }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a city</div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        @endfor
                        <div class="col-12">
                            <div class="gap-3 d-flex align-items-center">
                                <button type="button" class="px-4 btn btn-outline-secondary"
                                    onclick="handlePreviousStep()"><i
                                        class='bx bx-left-arrow-alt me-2'></i>Previous</button>
                                <button type="button" class="px-4 btn btn-primary" onclick="handleNextStep()">Next<i
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

                        <template x-if="payment_type === 'prepaid'">
                            <div class="col-12 col-lg-6">
                                <label for="first_payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="first_payment_date" id="first_payment_date"
                                    x-model="first_payment_date" required min="{{ date('Y-m-d') }}">
                                <div class="invalid-feedback">Please select a valid payment date</div>
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

                        <template x-if="payment_type === 'postpaid'">
                            <div class="col-12 col-lg-6">
                                <label for="first_payment_date" class="form-label">First Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="first_payment_date" id="first_payment_date"
                                    x-model="first_payment_date" required min="{{ date('Y-m-d') }}">
                                <div class="invalid-feedback">Please select a valid first payment date</div>
                            </div>
                        </template>

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
                                <strong>VAT (15%):</strong> <span x-text="(contractAmount * 0.15).toFixed(2)"></span> SAR<br>
                                <strong>Total Amount:</strong> <span x-text="(contractAmount * 1.15).toFixed(2)"></span> SAR
                                <template x-if="payment_type === 'postpaid'">
                                    <div>
                                        <strong>Payment per Installment:</strong> <span x-text="(contractAmount * 1.15 / numberOfPayments).toFixed(2)"></span> SAR
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="gap-3 d-flex align-items-center">
                            <button type="button" class="px-4 btn btn-outline-secondary" onclick="handlePreviousStep()">
                                <i class='bx bx-left-arrow-alt me-2'></i>Previous
                            </button>
                            <button type="button" class="px-4 btn btn-primary" onclick="handleNextStep()">
                                Next<i class='bx bx-right-arrow-alt ms-2'></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="{{ $aria_control_summery }}" role="tabpanel" class="bs-stepper-pane"
                    aria-labelledby="{{ $id_summery }}">
                    <h5 class="mb-1">Summary</h5>
                    <p class="mb-4">Review the information you entered</p>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-4 card-title">Contract Summary</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Client Information</h6>
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Full Name:</strong></td>
                                                        <td id="summary-client-name"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Mobile:</strong></td>
                                                        <td id="summary-client-mobile"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Email:</strong></td>
                                                        <td id="summary-client-email"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Tax Number:</strong></td>
                                                        <td id="summary-tax-number"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>City:</strong></td>
                                                        <td id="summary-client-city"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Address:</strong></td>
                                                        <td id="summary-client-address"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Contract Details</h6>
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Contract Number:</strong></td>
                                                        <td id="summary-contract-number"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Contract Date:</strong></td>
                                                        <td id="summary-contract-date"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Warranty Period:</strong></td>
                                                        <td id="summary-warranty"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mt-4 row">
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Payment Information</h6>
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Payment Type:</strong></td>
                                                        <td id="summary-payment-type"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Payment Schedule:</strong></td>
                                                        <td id="summary-payment-schedule"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Number of Payments:</strong></td>
                                                        <td id="summary-number-payments"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Contract Amount:</strong></td>
                                                        <td id="summary-contract-amount"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>VAT (15%):</strong></td>
                                                        <td id="summary-vat-amount"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Total Amount:</strong></td>
                                                        <td id="summary-total-amount"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>First Payment Date:</strong></td>
                                                        <td id="summary-first-payment-date"></td>
                                                    </tr>
                                                    <tr id="summary-payment-dates-row" style="display: none;">
                                                        <td colspan="2">
                                                            <strong>Payment Schedule:</strong>
                                                            <div id="summary-payment-dates" class="mt-2">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6" id="summary-payment-dates">
                                            <h6 class="mb-3">Payment Schedule</h6>
                                            <div id="payment-dates-list"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary"
                                    onclick="handlePreviousStep()">Edit</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var stepper;
    var currentStep = 0;
    var branchCount = document.getElementById('branchs_number')?.value || 0;

    document.addEventListener('DOMContentLoaded', function () {
        stepper = new Stepper(document.querySelector('.bs-stepper'), {
            linear: true,
            animation: true
        });
    });

    function validateClientInfo() {
        const form = document.getElementById('contractForm');
        const requiredFields = [
            { name: 'clientName', id: 'FullName', pattern: /.+/, message: 'Please enter client\'s full name' },
            { name: 'clientPhone', id: 'Phone', pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/, message: 'Please enter a valid Saudi phone number' },
            { name: 'clientMobile', id: 'Mobile', pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/, message: 'Please enter a valid Saudi mobile number' },
            { name: 'clientEmail', id: 'Email', pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: 'Please enter a valid email address' },
            { name: 'clientCity', id: 'City', pattern: /.+/, message: 'Please enter client\'s city' },
            { name: 'clientAddress', id: 'Address', pattern: /.+/, message: 'Please enter client\'s address' },
            { name: 'client_tax_number', id: 'taxNumber', pattern: /^[0-9]{15}$/, message: 'Please enter a valid 15-digit tax number' }
        ];

        let isValid = true;

        // Reset validation state
        requiredFields.forEach(field => {
            const element = document.getElementById(field.id);
            if (element) {
                element.classList.remove('is-invalid', 'is-valid');
            }
        });

        // Validate each field
        requiredFields.forEach(field => {
            const element = document.getElementById(field.id);
            if (!element) return;

            const value = element.value.trim();
            if (!value || !field.pattern.test(value)) {
                element.classList.add('is-invalid');
                isValid = false;
                
                // Show specific error message
                const feedback = element.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = field.message;
                }
            } else {
                element.classList.add('is-valid');
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please check all client information fields are filled correctly',
                confirmButtonText: 'OK'
            });
        }

        return isValid;
    }

    function validateContractDetails() {
        const form = document.getElementById('contractForm');
        let isValid = true;

        // Get all the required fields
        const contractNumber = form.querySelector('[name="contractnumber"]');
        const startDate = form.querySelector('[name="contractstartdate"]');
        const endDate = form.querySelector('[name="contractenddate"]');
        const warranty = form.querySelector('[name="warranty"]');
        const contractType = form.querySelector('[name="contract_type_id"]');
        const propertyType = form.querySelector('[name="Property_type"]');
        const description = form.querySelector('[name="contract_description"]');
        const visits = form.querySelector('[name="number_of_visits"]');

        // Reset validation states
        [contractNumber, startDate, endDate, warranty, contractType, propertyType, description, visits].forEach(field => {
            if (field) {
                field.classList.remove('is-invalid', 'is-valid');
            }
        });

        // Validate contract number
        if (!contractNumber || !contractNumber.value.trim()) {
            if (contractNumber) {
                contractNumber.classList.add('is-invalid');
                const feedback = contractNumber.nextElementSibling;
                if (feedback) feedback.textContent = 'Please enter a valid contract number';
            }
            isValid = false;
        } else {
            contractNumber.classList.add('is-valid');
        }

        // Validate property type
        if (!propertyType || !propertyType.value.trim()) {
            if (propertyType) {
                propertyType.classList.add('is-invalid');
                const feedback = propertyType.nextElementSibling;
                if (feedback) feedback.textContent = 'Please select a property type';
            }
            isValid = false;
        } else {
            propertyType.classList.add('is-valid');
        }

        // Validate contract description
        if (!description || !description.value.trim() || description.value.trim().length < 10) {
            if (description) {
                description.classList.add('is-invalid');
                const feedback = description.nextElementSibling;
                if (feedback) feedback.textContent = 'Please enter a detailed description (minimum 10 characters)';
            }
            isValid = false;
        } else {
            description.classList.add('is-valid');
        }

        // Validate number of visits
        if (!visits || !visits.value.trim() || parseInt(visits.value) < 1) {
            if (visits) {
                visits.classList.add('is-invalid');
                const feedback = visits.nextElementSibling;
                if (feedback) feedback.textContent = 'Please enter at least 1 visit';
            }
            isValid = false;
        } else {
            visits.classList.add('is-valid');
        }

        // Validate start date
        if (!startDate || !startDate.value.trim()) {
            if (startDate) {
                startDate.classList.add('is-invalid');
                const feedback = startDate.nextElementSibling;
                if (feedback) feedback.textContent = 'Please select a start date';
            }
            isValid = false;
        } else {
            startDate.classList.add('is-valid');
        }

        // Validate end date
        if (!endDate || !endDate.value.trim()) {
            if (endDate) {
                endDate.classList.add('is-invalid');
                const feedback = endDate.nextElementSibling;
                if (feedback) feedback.textContent = 'Please select an end date';
            }
            isValid = false;
        } else {
            endDate.classList.add('is-valid');
        }

        // Validate warranty
        if (!warranty || !warranty.value.trim() || parseInt(warranty.value) < 0) {
            if (warranty) {
                warranty.classList.add('is-invalid');
                const feedback = warranty.nextElementSibling;
                if (feedback) feedback.textContent = 'Please enter a valid warranty period';
            }
            isValid = false;
        } else {
            warranty.classList.add('is-valid');
        }

        // Additional date validation only if both dates are filled
        if (startDate && endDate && startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            
            if (end <= start) {
                endDate.classList.add('is-invalid');
                const feedback = endDate.nextElementSibling;
                if (feedback) feedback.textContent = 'End date must be after start date';
                isValid = false;
            }
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required contract details correctly',
                confirmButtonText: 'OK'
            });
        }

        return isValid;
    }

    function validateBranchInfo() {
        const form = document.getElementById('contractForm');
        let isValid = true;

        // Only validate if there are branches to validate
        if (branchCount <= 1) {
            return true;
        }

        for (let i = 0; i < branchCount; i++) {
            const fields = [
                { id: `branchName${i}`, label: 'Branch Name' },
                { id: `branchmanager${i}`, label: 'Branch Manager' },
                { id: `branchphone${i}`, label: 'Branch Phone', pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/ },
                { id: `branchAddress${i}`, label: 'Branch Address' },
                { id: `branchcity${i}`, label: 'Branch City' }
            ];

            fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    element.classList.remove('is-invalid', 'is-valid');
                    const value = element.value.trim();
                    
                    if (!value || (field.pattern && !field.pattern.test(value))) {
                        element.classList.add('is-invalid');
                        isValid = false;
                        
                        // Show specific error message
                        const feedback = element.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = field.pattern 
                                ? `Please enter a valid ${field.label.toLowerCase()}`
                                : `Please enter the ${field.label.toLowerCase()}`;
                        }
                    } else {
                        element.classList.add('is-valid');
                    }
                }
            });
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all branch information correctly',
                confirmButtonText: 'OK'
            });
        }

        return isValid;
    }

    function validatePaymentInfo() {
        const form = document.getElementById('contractForm');
        let isValid = true;
        let errorMessage = '';

        // Validate contract amount
        const contractAmount = form.querySelector('#Contractamount');
        if (!contractAmount || !contractAmount.value || parseFloat(contractAmount.value) <= 0) {
            contractAmount.classList.add('is-invalid');
            isValid = false;
            errorMessage = 'Please enter a valid contract amount greater than 0';
            return false;
        }
        contractAmount.classList.remove('is-invalid');

        // Get payment type
        const paymentType = form.querySelector('#payment_type');
        if (!paymentType || !paymentType.value || !['prepaid', 'postpaid'].includes(paymentType.value)) {
            paymentType.classList.add('is-invalid');
            isValid = false;
            errorMessage = 'Please select a valid payment type';
            return false;
        }
        paymentType.classList.remove('is-invalid');

        if (paymentType.value === 'postpaid') {
            // Validate number of payments
            const numberOfPayments = form.querySelector('#number_of_payments');
            if (!numberOfPayments || !numberOfPayments.value || 
                parseInt(numberOfPayments.value) < 1 || parseInt(numberOfPayments.value) > 24) {
                numberOfPayments.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please enter a valid number of payments (1-24)';
                return false;
            }
            numberOfPayments.classList.remove('is-invalid');

            // Validate payment schedule
            const paymentSchedule = form.querySelector('#payment_schedule');
            if (!paymentSchedule || !paymentSchedule.value) {
                paymentSchedule.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please select a payment schedule';
                return false;
            }
            paymentSchedule.classList.remove('is-invalid');

            // Validate first payment date
            const firstPaymentDate = form.querySelector('#first_payment_date');
            if (!firstPaymentDate || !firstPaymentDate.value) {
                firstPaymentDate.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please select the first payment date';
                return false;
            }
            firstPaymentDate.classList.remove('is-invalid');

            // Validate custom payment dates if custom schedule is selected
            if (paymentSchedule.value === 'custom') {
                const customDateInputs = form.querySelectorAll('[id^="payment_date_"]');
                let previousDate = new Date(firstPaymentDate.value);

                for (let i = 0; i < customDateInputs.length; i++) {
                    const input = customDateInputs[i];
                    if (!input.value) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        errorMessage = `Please select payment date ${i + 2}`;
                        return false;
                    }

                    const currentDate = new Date(input.value);
                    if (currentDate <= previousDate) {
                        input.classList.add('is-invalid');
                        isValid = false;
                        errorMessage = `Payment date ${i + 2} must be after the previous payment date`;
                        return false;
                    }
                    
                    input.classList.remove('is-invalid');
                    previousDate = currentDate;
                }
            }
        } else {
            // Validate payment date for prepaid
            const paymentDate = form.querySelector('#first_payment_date');
            if (!paymentDate || !paymentDate.value) {
                paymentDate.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please select the payment date';
                return false;
            }
            paymentDate.classList.remove('is-invalid');
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage || 'Please fill in all payment information correctly'
            });
        }

        return isValid;
    }

    function updateSummary() {
        try {
            // Helper function to safely get input value
            const getInputValue = (id) => {
                const element = document.getElementById(id);
                return element ? element.value : '';
            };

            // Helper function to safely get checked radio value
            const getCheckedRadioValue = (name) => {
                const element = document.querySelector(`select[name="${name}"]`);
                return element ? element.value : '';
            };

            // Client Information
            document.getElementById('summary-client-name').textContent = getInputValue('FullName');
            document.getElementById('summary-client-mobile').textContent = getInputValue('Mobile');
            document.getElementById('summary-client-email').textContent = getInputValue('Email');
            document.getElementById('summary-tax-number').textContent = getInputValue('taxNumber');
            document.getElementById('summary-client-city').textContent = document.getElementById('City')?.options[document.getElementById('City')?.selectedIndex]?.text || '';
            document.getElementById('summary-client-address').textContent = getInputValue('Address');

            // Contract Details
            document.getElementById('summary-contract-number').textContent = getInputValue('contractNumber');
            document.getElementById('summary-contract-date').textContent = `${getInputValue('contractstartdate')} to ${getInputValue('contractenddate')}`;
            document.getElementById('summary-warranty').textContent = getInputValue('warranty') + ' months';

            // Get contract type
            const contractTypeSelect = document.getElementById('contract_type_id');
            const contractType = contractTypeSelect ? contractTypeSelect.options[contractTypeSelect.selectedIndex]?.text : '';

            // Payment Information
            const paymentType = getInputValue('payment_type');
            const paymentSchedule = document.querySelector('[name="payment_schedule"]')?.value || 'monthly';
            const numberOfPayments = getInputValue('number_of_payments');
            const contractAmount = parseFloat(getInputValue('Contractamount')) || 0;
            const vatAmount = contractAmount * 0.15;
            const totalAmount = contractAmount * 1.15;

            document.getElementById('summary-payment-type').textContent = paymentType.charAt(0).toUpperCase() + paymentType.slice(1);
            document.getElementById('summary-payment-schedule').textContent = paymentSchedule.charAt(0).toUpperCase() + paymentSchedule.slice(1);
            document.getElementById('summary-number-payments').textContent = numberOfPayments || '1';
            document.getElementById('summary-contract-amount').textContent = contractAmount.toFixed(2) + ' SAR';
            document.getElementById('summary-vat-amount').textContent = vatAmount.toFixed(2) + ' SAR';
            document.getElementById('summary-total-amount').textContent = totalAmount.toFixed(2) + ' SAR';
            
            // Payment Dates
            const firstPaymentDate = getInputValue('first_payment_date');
            document.getElementById('summary-first-payment-date').textContent = firstPaymentDate;

            // Handle payment schedule dates
            const paymentDatesRow = document.getElementById('summary-payment-dates-row');
            const paymentDatesContainer = document.getElementById('summary-payment-dates');
            
            if (paymentType === 'postpaid' && paymentSchedule === 'custom' && numberOfPayments > 1) {
                let paymentDatesHtml = '<ul class="mb-0 list-unstyled">';
                paymentDatesHtml += `<li>Payment 1: ${firstPaymentDate}</li>`;
                
                for (let i = 1; i < parseInt(numberOfPayments); i++) {
                    const date = getInputValue(`payment_date_${i + 1}`);
                    if (date) {
                        paymentDatesHtml += `<li>Payment ${i + 1}: ${date}</li>`;
                    }
                }
                
                paymentDatesHtml += '</ul>';
                paymentDatesContainer.innerHTML = paymentDatesHtml;
                paymentDatesRow.style.display = 'table-row';
            } else {
                paymentDatesRow.style.display = 'none';
            }

            // Branch Information (if applicable)
            const branchsNumber = parseInt(getInputValue('branchs_number')) || 0;
            if (branchsNumber > 1) {
                const branchInfo = document.createElement('div');
                branchInfo.className = 'col-md-12 mt-4';
                branchInfo.innerHTML = `
                    <h6 class="mb-3">Branch Information</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Manager</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Array.from({ length: branchsNumber }, (_, i) => `
                                    <tr>
                                        <td>${getInputValue('branchName' + i)}</td>
                                        <td>${getInputValue('branchmanager' + i)}</td>
                                        <td>${getInputValue('branchphone' + i)}</td>
                                        <td>${document.getElementById('branchcity' + i)?.options[document.getElementById('branchcity' + i)?.selectedIndex]?.text || ''}</td>
                                        <td>${getInputValue('branchAddress' + i)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                
                // Add branch information to the summary
                const summaryContainer = document.querySelector('.card-body');
                if (summaryContainer) {
                    // Remove existing branch information if any
                    const existingBranchInfo = summaryContainer.querySelector('.col-md-12.mt-4');
                    if (existingBranchInfo) {
                        existingBranchInfo.remove();
                    }
                    summaryContainer.appendChild(branchInfo);
                }
            }
        } catch (error) {
            console.error('Error updating summary:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error updating the summary. Please try again.'
            });
        }
    }

    function handleNextStep() {
        let isValid = true;
        const paymentStep = branchCount > 1 ? 3 : 2;
        
        switch(currentStep) {
            case 0:
                isValid = validateClientInfo();
                break;
            case 1:
                isValid = validateContractDetails();
                break;
            case 2:
                if (branchCount > 1) {
                    isValid = validateBranchInfo();
                } else {
                    isValid = validatePaymentInfo();
                }
                break;
            case 3:
                if (branchCount > 1) {
                    isValid = validatePaymentInfo();
                }
                break;
        }

        if (isValid) {
            if (currentStep === paymentStep) {
                updateSummary(); // Update summary before showing the final step
            }
            stepper.next();
            currentStep++;
        }
    }

    function handlePreviousStep() {
        currentStep--;
        stepper.previous();
    }

    function validateForm(event) {
        event.preventDefault();
        
        // Validate all sections
        const validations = [
            validateClientInfo(),
            validateContractDetails(),
            branchCount > 1 ? validateBranchInfo() : true,
            validatePaymentInfo()
        ];

        if (validations.every(v => v === true)) {
            Swal.fire({
                title: 'Confirm Contract Submission',
                text: 'Are you sure you want to submit this contract?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'No, review again'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting Contract',
                        text: 'Please wait while we process your contract...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            document.getElementById('contractForm').submit();
                        }
                    });
                }
            });
            return false;
        }

        Swal.fire({
            icon: 'error',
            title: 'Form Validation Failed',
            text: 'Please check all sections are filled correctly',
            confirmButtonText: 'OK'
        });

        return false;
    }

    function cancelContract() {
        Swal.fire({
            title: 'Cancel Contract?',
            text: 'Are you sure you want to cancel this contract? All entered data will be lost.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep editing'
        }).then((result) => {
            if (result.isConfirmed) {
                // Reset the form
                document.getElementById('contractForm').reset();
                
                // Reset the stepper to the first step
                currentStep = 0;
                stepper.to(1);
                
                // Clear any validation messages or errors
                clearValidationMessages();
                
                // Clear the contract summary if it exists
                const summaryDiv = document.getElementById('contract-summary');
                if (summaryDiv) {
                    summaryDiv.innerHTML = '';
                }

                Swal.fire(
                    'Cancelled!',
                    'Your contract has been cancelled.',
                    'success'
                );
            }
        });
    }

    function handlePaymentScheduleChange() {
        const paymentSchedule = document.querySelector('[name="payment_schedule"]');
        const numberOfPayments = document.querySelector('[name="number_of_payments"]');
        const customDatesContainer = document.getElementById('custom-payment-dates');
        
        if (!customDatesContainer) {
            // Create the container if it doesn't exist
            const container = document.createElement('div');
            container.id = 'custom-payment-dates';
            paymentSchedule.parentElement.after(container);
        }

        if (paymentSchedule.value === 'custom' && numberOfPayments.value) {
            generateCustomPaymentDateFields(parseInt(numberOfPayments.value));
        } else {
            // Clear custom date fields if not using custom schedule
            document.getElementById('custom-payment-dates').innerHTML = '';
        }
    }

    function generateCustomPaymentDateFields(numberOfPayments) {
        const container = document.getElementById('custom-payment-dates');
        const firstPaymentDate = document.getElementById('first_payment_date').value;
        
        if (!firstPaymentDate) {
            Swal.fire({
                icon: 'warning',
                title: 'First Payment Date Required',
                text: 'Please select the first payment date before setting custom dates'
            });
            return;
        }

        container.innerHTML = '';
        
        for (let i = 1; i < numberOfPayments; i++) {
            const dateGroup = document.createElement('div');
            dateGroup.className = 'mb-3';
            
            const label = document.createElement('label');
            label.className = 'form-label';
            label.htmlFor = `payment_date_${i + 1}`;
            label.innerHTML = `Payment Date ${i + 1} <span class="text-danger">*</span>`;
            
            const input = document.createElement('input');
            input.type = 'date';
            input.className = 'form-control';
            input.name = `payment_date_${i + 1}`;
            input.id = `payment_date_${i + 1}`;
            input.required = true;
            input.min = firstPaymentDate;
            
            dateGroup.appendChild(label);
            dateGroup.appendChild(input);
            container.appendChild(dateGroup);
        }
    }
</script>
@endsection