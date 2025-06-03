@extends('shared.dashboard')
@push('style')
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
@endpush
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

    <div class="page-content">
        @if (session('error'))
            <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
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
                    <i class="bx bx-arrow-back"></i> {{ __('sales_views.back') }}
                </a>
                <h4 class="mb-0 text-primary"><i class="bx bx-file-plus"></i>
                    {{ __('sales_views.create_equipment_contract') }}</h4>
            </div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ __('sales_views.create_equipment_contract') }}</li>
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
                                    <h5 class="mb-0 steper-title">{{ __('sales_views.customer_information') }}</h5>
                                    <p class="mb-0 steper-sub-title">{{ __('sales_views.enter_customer_details') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#test-l-2">
                            <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                                <div class="bs-stepper-circle">2</div>
                                <div class="">
                                    <h5 class="mb-0 steper-title">{{ __('sales_views.equipment_details') }}</h5>
                                    <p class="mb-0 steper-sub-title">{{ __('sales_views.enter_equipment_details') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#test-l-3">
                            <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                                <div class="bs-stepper-circle">3</div>
                                <div class="">
                                    <h5 class="mb-0 steper-title">{{ __('sales_views.branch_information') }}</h5>
                                    <p class="mb-0 steper-sub-title">{{ __('sales_views.enter_branch_details') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#test-l-4">
                            <div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="test-l-4">
                                <div class="bs-stepper-circle">4</div>
                                <div class="">
                                    <h5 class="mb-0 steper-title">{{ __('sales_views.payment_information') }}</h5>
                                    <p class="mb-0 steper-sub-title">{{ __('sales_views.enter_payment_details') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bs-stepper-line"></div>
                        <div class="step" data-target="#test-l-5">
                            <div class="step-trigger" role="tab" id="stepper1trigger5" aria-controls="test-l-5">
                                <div class="bs-stepper-circle">5</div>
                                <div class="">
                                    <h5 class="mb-0 steper-title">{{ __('sales_views.summary') }}</h5>
                                    <p class="mb-0 steper-sub-title">{{ __('sales_views.review_and_submit') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="bs-stepper-content">
                        <form action="{{ route('equipment.contract.store') }}" method="POST" id="contractForm"
                            x-data="contractData">
                            @csrf
                            <input type="hidden" name="contract_number" value="{{ $contract_number }}">
                            <div id="test-l-1" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger1">
                                <h5 class="mb-1">{{ __('sales_views.customer_information') }}</h5>
                                <p class="mb-4">{{ __('sales_views.enter_customer_details') }}</p>
                                <div class="row g-3">
                                    @if (request()->has('existing'))
                                        <div class="col-12">
                                            <label for="client_id"
                                                class="form-label">{{ __('sales_views.select_existing_client') }}</label>
                                            <select class="form-select" name="client_id" id="client_id" required>
                                                <option value="">{{ __('sales_views.choose_a_client') }}</option>
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->id }}">{{ $client->name }}
                                                        <small class="text-muted">({{ $client->email }})</small>
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <label for="customer_name"
                                                class="form-label">{{ __('sales_views.customer_name') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name"
                                                name="customer_name" required>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_customer_name') }}
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="customer_mobile" class="form-label">{{ __('sales_views.phone') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_mobile"
                                                name="customer_mobile" pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$"
                                                required>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_valid_saudi_mobile_number') }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="customer_email" class="form-label">{{ __('sales_views.email') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="customer_email"
                                                name="customer_email" required>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_valid_email_address') }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="customer_address"
                                                class="form-label">{{ __('sales_views.address') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_address"
                                                name="customer_address" required>
                                            <div class="invalid-feedback">Please enter the customer address</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="customer_city" class="form-label">{{ __('sales_views.city') }}
                                                <span class="text-danger">*</span></label>
                                            <select class="form-select" id="customer_city" name="customer_city" required>
                                                <option value="">Select a city...</option>
                                                @foreach ($saudiCities as $city)
                                                    <option value="{{ $city }}">{{ $city }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select the customer city</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="customer_zip_code"
                                                class="form-label">{{ __('sales_views.zip_code') }}</label>
                                            <input type="text" class="form-control" id="customer_zip_code"
                                                name="customer_zip_code">
                                            <div class="invalid-feedback">Please enter a valid ZIP code</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="customer_tax_number"
                                                class="form-label">{{ __('sales_views.tax_number') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_tax_number"
                                                name="customer_tax_number" required>
                                            <div class="invalid-feedback">Please enter the tax number</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary"
                                        onclick="handleNextStep()">{{ __('sales_views.next') }}</button>
                                </div>
                            </div>

                            <div id="test-l-2" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger2">
                                <div class="step-indicator">
                                    <h5 class="mb-1">{{ __('sales_views.equipment_details') }}</h5>
                                </div>
                                <p class="mb-4">{{ __('sales_views.enter_equipment_details') }}</p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="equipment_type_id" name="equipment_type_id"
                                                required>
                                                <option value="">{{ __('sales_views.select_equipment_type') }}
                                                </option>
                                                @foreach ($equipment_types as $type)
                                                    <option value="{{ $type->id }}"
                                                        data-price="{{ $type->default_price }}">{{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="equipment_type_id">{{ __('sales_views.equipment_type') }} <i
                                                    class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                                    title="{{ __('sales_views.equipment_type_tooltip') }}"></i></label>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_select_equipment_type') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="equipment_model"
                                                name="equipment_model" placeholder="{{ __('sales_views.enter_model') }}"
                                                required>
                                            <label for="equipment_model">{{ __('sales_views.equipment_model') }} <i
                                                    class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                                    title="{{ __('sales_views.equipment_model_tooltip') }}"></i></label>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_equipment_model') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="equipment_quantity"
                                                name="equipment_quantity"
                                                placeholder="{{ __('sales_views.enter_quantity') }}" required
                                                min="1">
                                            <label for="equipment_quantity">{{ __('sales_views.quantity') }} <i
                                                    class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                                    title="{{ __('sales_views.quantity_tooltip') }}"></i></label>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_valid_quantity') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="warranty" name="warranty"
                                                placeholder="{{ __('sales_views.enter_warranty_period') }}" required
                                                min="0" value="0">
                                            <label for="warranty">{{ __('sales_views.warranty_period_months') }} <i
                                                    class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                                    title="{{ __('sales_views.warranty_period_tooltip') }}"></i></label>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_valid_warranty_period') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="equipment_description" name="equipment_description"
                                                placeholder="{{ __('sales_views.enter_description') }}" style="height: 100px" required></textarea>
                                            <label for="equipment_description">{{ __('sales_views.description') }} <i
                                                    class="bx bx-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                                    title="{{ __('sales_views.description_tooltip') }}"></i></label>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_equipment_description') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i
                                            class='bx bx-left-arrow-alt me-2'></i>{{ __('sales_views.previous') }}</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="handleNextStep()">{{ __('sales_views.next') }}<i
                                            class='bx bx-right-arrow-alt ms-2'></i></button>
                                </div>
                            </div>

                            <div id="test-l-3" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger3">
                                <div class="step-indicator">
                                    <h5 class="mb-1">{{ __('sales_views.branch_information') }}</h5>
                                </div>
                                <p class="mb-4">{{ __('sales_views.enter_branch_details') }}</p>

                                <div id="branches-container">
                                    <!-- Initial branch form -->
                                    <div class="p-3 mb-4 rounded border branch-form" data-branch-index="0">
                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ __('sales_views.branch_number', ['number' => 1]) }}</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-branch-btn"
                                                style="display: none;">
                                                <i class='bx bx-trash'></i> {{ __('sales_views.remove') }}
                                            </button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="branchName0"
                                                    class="form-label">{{ __('sales_views.branch_name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="branchName0"
                                                    name="branchName[0]" required>
                                                <div class="invalid-feedback">
                                                    {{ __('sales_views.please_enter_branch_name') }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="branchmanager0"
                                                    class="form-label">{{ __('sales_views.branch_manager_name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="branchmanager0"
                                                    name="branchmanager[0]" required>
                                                <div class="invalid-feedback">
                                                    {{ __('sales_views.please_enter_branch_manager_name') }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="branchmanagerPhone0"
                                                    class="form-label">{{ __('sales_views.manager_phone') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="branchmanagerPhone0"
                                                    name="branchmanagerPhone[0]"
                                                    pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required>
                                                <div class="invalid-feedback">
                                                    {{ __('sales_views.please_enter_valid_saudi_mobile_number') }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="branchCity0" class="form-label">{{ __('sales_views.city') }}
                                                    <span class="text-danger">*</span></label>
                                                <select class="form-select" id="branchCity0" name="branchCity[0]"
                                                    required>
                                                    <option value="">{{ __('sales_views.select_city') }}</option>
                                                    @foreach ($saudiCities as $city)
                                                        <option value="{{ $city }}">{{ $city }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    {{ __('sales_views.please_select_branch_city') }}</div>
                                            </div>
                                            <div class="col-12">
                                                <label for="branchAddress0"
                                                    class="form-label">{{ __('sales_views.address') }} <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control" id="branchAddress0" name="branchAddress[0]" rows="2" required></textarea>
                                                <div class="invalid-feedback">
                                                    {{ __('sales_views.please_enter_branch_address') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-primary" id="add-branch-btn">
                                        <i class='bx bx-plus me-1'></i> {{ __('sales_views.add_another_branch') }}
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i
                                            class='bx bx-left-arrow-alt me-2'></i>{{ __('sales_views.previous') }}</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="handleNextStep()">{{ __('sales_views.next') }}<i
                                            class='bx bx-right-arrow-alt ms-2'></i></button>
                                </div>
                            </div>

                            <div id="test-l-4" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger4">
                                <h5 class="mb-1">{{ __('sales_views.payment_information') }}</h5>
                                <p class="mb-4">{{ __('sales_views.enter_payment_details') }}</p>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label for="Contractamount"
                                            class="form-label">{{ __('sales_views.contract_amount_without_vat') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="contractamount"
                                            id="Contractamount" x-model="contractAmount" required min="0"
                                            step="0.01">
                                        <div class="invalid-feedback">
                                            {{ __('sales_views.please_enter_valid_contract_amount') }}</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="first_payment_date"
                                            class="form-label">{{ __('sales_views.first_payment_date') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="first_payment_date"
                                            id="first_payment_date" x-model="first_payment_date" required
                                            min="{{ date('Y-m-d') }}">
                                        <div class="invalid-feedback">
                                            {{ __('sales_views.please_select_valid_payment_date') }}</div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="payment_type" class="form-label">{{ __('sales_views.payment_type') }}
                                            <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_type" id="payment_type"
                                            x-model="payment_type" required>
                                            <option value="prepaid">{{ __('sales_views.prepaid_full_amount') }}</option>
                                            <option value="postpaid">{{ __('sales_views.postpaid_installments') }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">{{ __('sales_views.please_select_payment_type') }}
                                        </div>
                                    </div>

                                    <template x-if="payment_type === 'postpaid'">
                                        <div class="col-12 col-lg-6 postpaid-field">
                                            <label for="number_of_payments"
                                                class="form-label">{{ __('sales_views.number_of_payments') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="number_of_payments"
                                                id="number_of_payments" x-model="numberOfPayments" required
                                                min="1" max="24">
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_enter_valid_number_of_payments') }}
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="payment_type === 'postpaid'">
                                        <div class="col-12 col-lg-6 postpaid-field">
                                            <label for="payment_schedule"
                                                class="form-label">{{ __('sales_views.payment_schedule') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" name="payment_schedule" id="payment_schedule"
                                                x-model="payment_schedule" required>
                                                <option value="monthly">{{ __('sales_views.monthly') }}</option>
                                                <option value="custom">{{ __('sales_views.custom_dates') }}</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ __('sales_views.please_select_payment_schedule') }}</div>
                                        </div>
                                    </template>

                                    <template x-if="payment_type === 'postpaid' && payment_schedule === 'custom'">
                                        <div class="col-12 postpaid-field custom-schedule-field">
                                            <div class="p-3 rounded border">
                                                <h6 class="mb-3">{{ __('sales_views.custom_payment_dates') }}</h6>
                                                <div id="custom-payment-dates">
                                                    <!-- Custom payment date fields will be generated here -->
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <div class="row">
                                                <div class="col-6">{{ __('sales_views.contract_amount') }}:</div>
                                                <div class="col-6 text-end"
                                                    x-text="'SAR ' + Number(contractAmount).toFixed(2)"></div>
                                                <div class="col-6">
                                                    {{ __('sales_views.vat_percentage', ['percentage' => '15']) }}:
                                                </div>
                                                <div class="col-6 text-end" x-text="'SAR ' + vatAmount.toFixed(2)"></div>
                                                <div class="col-6">
                                                    <strong>{{ __('sales_views.total_amount') }}:</strong>
                                                </div>
                                                <div class="col-6 text-end"><strong
                                                        x-text="'SAR ' + totalAmount.toFixed(2)"></strong></div>
                                                <template x-if="payment_type === 'postpaid'">
                                                    <div class="mt-2 col-12">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                {{ __('sales_views.payment_per_installment') }}:</div>
                                                            <div class="col-6 text-end"
                                                                x-text="'SAR ' + installmentAmount.toFixed(2)"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i
                                            class='bx bx-left-arrow-alt me-2'></i>{{ __('sales_views.previous') }}</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="handleNextStep()">{{ __('sales_views.next') }}<i
                                            class='bx bx-right-arrow-alt ms-2'></i></button>
                                </div>
                            </div>

                            <div id="test-l-5" role="tabpanel" class="bs-stepper-pane"
                                aria-labelledby="stepper1trigger5">
                                <div class="step-indicator">
                                    <h5 class="mb-1">{{ __('sales_views.summary') }}</h5>
                                </div>
                                <p class="mb-4">{{ __('sales_views.review_contract_details') }}</p>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="mb-4 card-title">{{ __('sales_views.contract_summary') }}</h5>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-3">{{ __('sales_views.equipment_information') }}
                                                        </h6>
                                                        <table class="table table-borderless">
                                                            <tbody id="equipment-summary">
                                                                <!-- Will be populated by JavaScript -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="mb-3">{{ __('sales_views.payment_information') }}
                                                        </h6>
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
                                                <h5 class="mb-4 card-title">{{ __('sales_views.branch_information') }}
                                                </h5>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h6 class="mb-3">{{ __('sales_views.branch_details') }}</h6>
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
                                    <button type="button" class="btn btn-primary" onclick="handlePreviousStep()"><i
                                            class='bx bx-left-arrow-alt me-2'></i>{{ __('sales_views.previous') }}</button>
                                    <button type="submit" class="btn btn-success"><i
                                            class='bx bx-check me-2'></i>{{ __('sales_views.submit_contract') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        let stepper;
        let currentStep = 0;
        let emailExists = false;
        let branchCounter = 1;

        // Email validation function
        function checkEmailExists(email) {
            if (!email) return;

            fetch(`/api/check-email-exists?email=${encodeURIComponent(email)}`)
                .then(response => response.json())
                .then(data => {
                    const emailField = document.getElementById('customer_email');
                    const feedbackElement = emailField.nextElementSibling;

                    if (data.exists) {
                        emailExists = true;
                        emailField.classList.add('is-invalid');
                        feedbackElement.textContent =
                            'This email is already registered. Please use a different email or choose an existing client.';
                    } else {
                        emailExists = false;
                        emailField.classList.remove('is-invalid');
                    }
                })
                .catch(error => {
                    console.error('Error checking email:', error);
                });
        }

        // Validation functions
        function validateStep1() {
            const form = document.getElementById('contractForm');
            if (form.querySelector('[name="client_id"]')) {
                // For existing client
                const clientId = form.querySelector('[name="client_id"]').value;
                return clientId ? true : false;
            } else {
                // For new client
                const fields = [{
                        id: 'customer_name',
                        regex: new RegExp('.+')
                    },
                    {
                        id: 'customer_mobile',
                        regex: new RegExp('^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$')
                    },
                    {
                        id: 'customer_email',
                        regex: new RegExp('^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$')
                    },
                    {
                        id: 'customer_address',
                        regex: new RegExp('.+')
                    },
                    {
                        id: 'customer_city',
                        regex: new RegExp('.+')
                    },
                    {
                        id: 'customer_tax_number',
                        regex: new RegExp('^[0-9]{10,15}$')
                    }
                ];

                // Check if email already exists
                if (emailExists) {
                    const emailField = document.getElementById('customer_email');
                    emailField.classList.add('is-invalid');
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Already Registered',
                        text: 'This email is already registered. Please use a different email or choose an existing client.'
                    });
                    return false;
                }

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
            const fields = [{
                    id: 'equipment_type_id',
                    regex: new RegExp('.+')
                },
                {
                    id: 'equipment_model',
                    regex: new RegExp('.+')
                },
                {
                    id: 'equipment_quantity',
                    regex: new RegExp('^[1-9]\\d*$')
                },
                {
                    id: 'equipment_description',
                    regex: new RegExp('.+')
                },
                {
                    id: 'warranty',
                    regex: new RegExp('^[0-9]+$')
                }
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
                const branchName = branch.querySelector('input[id^="branchName"]') || branch.querySelector(
                    'input[name^="branchName"]');
                const branchManager = branch.querySelector('input[id^="branchmanager"]') || branch.querySelector(
                    'input[name^="branchmanager"]');
                const branchManagerPhone = branch.querySelector('input[id^="branchmanagerPhone"]') || branch
                    .querySelector('input[name^="branchmanagerPhone"]');
                const branchCity = branch.querySelector('select[id^="branchCity"]') || branch.querySelector(
                    'select[name^="branchCity"]');
                const branchAddress = branch.querySelector('textarea[id^="branchAddress"]') || branch.querySelector(
                    'textarea[name^="branchAddress"]');

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
            if (!contractAmount || contractAmount.value === '' || parseFloat(contractAmount.value) < 0) {
                if (contractAmount) contractAmount.classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please enter a valid contract amount (0 or greater)';
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
            switch (currentStep) {
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

        // Function to update the summary step information
        function updateSummary() {
            // Update equipment summary
            const equipmentSummary = document.getElementById('equipment-summary');
            if (equipmentSummary) {
                const equipmentType = document.getElementById('equipment_type_id');
                const equipmentModel = document.getElementById('equipment_model');
                const equipmentQuantity = document.getElementById('equipment_quantity');
                const equipmentDescription = document.getElementById('equipment_description');
                const warranty = document.getElementById('warranty');

                const equipmentTypeText = equipmentType && equipmentType.selectedIndex !== -1 ?
                    equipmentType.options[equipmentType.selectedIndex].text : '';

                // Get translations for equipment details
                const equipmentTypeLabel = '{{ __('sales_views.equipment_type') }}';
                const modelLabel = '{{ __('sales_views.equipment_model') }}';
                const quantityLabel = '{{ __('sales_views.quantity') }}';
                const descriptionLabel = '{{ __('sales_views.description') }}';
                const warrantyLabel = '{{ __('sales_views.warranty_period_months') }}';

                let equipmentDetails = `
                    <tr>
                        <td><strong>${equipmentTypeLabel}:</strong></td>
                        <td>${equipmentTypeText}</td>
                    </tr>
                    <tr>
                        <td><strong>${modelLabel}:</strong></td>
                        <td>${equipmentModel ? equipmentModel.value : ''}</td>
                    </tr>
                    <tr>
                        <td><strong>${quantityLabel}:</strong></td>
                        <td>${equipmentQuantity ? equipmentQuantity.value : ''}</td>
                    </tr>
                    <tr>
                        <td><strong>${descriptionLabel}:</strong></td>
                        <td>${equipmentDescription ? equipmentDescription.value : ''}</td>
                    </tr>
                    <tr>
                        <td><strong>${warrantyLabel}:</strong></td>
                        <td>${warranty ? warranty.value : ''}</td>
                    </tr>
                `;

                equipmentSummary.innerHTML = equipmentDetails;
            }

            // Update payment summary
            const paymentSummary = document.getElementById('payment-summary');
            if (paymentSummary) {
                const contractAmount = document.getElementById('Contractamount');
                const vatAmount = contractAmount ? parseFloat(contractAmount.value) * 0.15 : 0;
                const totalAmount = contractAmount ? parseFloat(contractAmount.value) + vatAmount : 0;

                const paymentType = document.getElementById('payment_type');
                const paymentTypeValue = paymentType ? paymentType.value : 'prepaid';

                const numberOfPayments = document.getElementById('number_of_payments');
                const numberOfPaymentsValue = numberOfPayments ? numberOfPayments.value : '1';

                const paymentSchedule = document.getElementById('payment_schedule');
                const paymentScheduleValue = paymentSchedule ? paymentSchedule.value : 'monthly';

                const firstPaymentDate = document.getElementById('first_payment_date');
                const firstPaymentDateValue = firstPaymentDate ? firstPaymentDate.value : '';

                // Get translations for payment details
                const contractAmountLabel = '{{ __('sales_views.contract_amount') }}';
                const vatLabel = '{{ __('sales_views.vat_percentage', ['percentage' => '15']) }}';
                const totalAmountLabel = '{{ __('sales_views.total_amount') }}';
                const paymentTypeLabel = '{{ __('sales_views.payment_type') }}';
                const prepaidText = '{{ __('sales_views.prepaid_full_amount') }}';
                const postpaidText = '{{ __('sales_views.postpaid_installments') }}';
                const numberOfPaymentsLabel = '{{ __('sales_views.number_of_payments') }}';
                const paymentScheduleLabel = '{{ __('sales_views.payment_schedule') }}';
                const monthlyText = '{{ __('sales_views.monthly') }}';
                const customText = '{{ __('sales_views.custom_dates') }}';
                const firstPaymentLabel = '{{ __('sales_views.first_payment_date') }}';

                let paymentDetails = `
                    <tr>
                        <td><strong>${contractAmountLabel}:</strong></td>
                        <td>SAR ${contractAmount ? parseFloat(contractAmount.value).toFixed(2) : '0.00'}</td>
                    </tr>
                    <tr>
                        <td><strong>${vatLabel}:</strong></td>
                        <td>SAR ${vatAmount.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><strong>${totalAmountLabel}:</strong></td>
                        <td>SAR ${totalAmount.toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><strong>${paymentTypeLabel}:</strong></td>
                        <td>${paymentTypeValue === 'prepaid' ? prepaidText : postpaidText}</td>
                    </tr>
                `;

                if (paymentTypeValue === 'postpaid') {
                    paymentDetails += `
                    <tr>
                        <td><strong>${numberOfPaymentsLabel}:</strong></td>
                        <td>${numberOfPaymentsValue}</td>
                    </tr>
                    <tr>
                        <td><strong>${paymentScheduleLabel}:</strong></td>
                        <td>${paymentScheduleValue === 'monthly' ? monthlyText : customText}</td>
                    </tr>
                    <tr>
                        <td><strong>${firstPaymentLabel}:</strong></td>
                        <td>${firstPaymentDateValue}</td>
                    </tr>
                    `;

                    // Add custom payment dates if applicable
                    if (paymentScheduleValue === 'custom') {
                        for (let i = 2; i <= parseInt(numberOfPaymentsValue); i++) {
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
                    const paymentDateText = '{{ __('sales_views.payment_date') }}';
                    paymentDetails += `
                    <tr>
                        <td><strong>${paymentDateText}:</strong></td>
                        <td>${firstPaymentDateValue}</td>
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
                    const branchName = branch.querySelector('input[id^="branchName"]') || branch.querySelector(
                        'input[name^="branchName"]');
                    const branchManager = branch.querySelector('input[id^="branchmanager"]') || branch
                        .querySelector('input[name^="branchmanager"]');
                    const branchManagerPhone = branch.querySelector('input[id^="branchmanagerPhone"]') || branch
                        .querySelector('input[name^="branchmanagerPhone"]');
                    const branchCity = branch.querySelector('select[id^="branchCity"]') || branch.querySelector(
                        'select[name^="branchCity"]');
                    const branchAddress = branch.querySelector('textarea[id^="branchAddress"]') || branch
                        .querySelector('textarea[name^="branchAddress"]');

                    // Only add to the summary if all values exist
                    if (branchName?.value && branchManager?.value && branchManagerPhone?.value &&
                        branchCity?.value && branchAddress?.value) {

                        // For city, get the selected option text
                        let cityText = branchCity.value;
                        if (branchCity.selectedIndex !== -1) {
                            cityText = branchCity.options[branchCity.selectedIndex].text;
                        }

                        // Get translations for branch details
                        const branchNumberText = '{{ __('sales_views.branch_number_summary') }}';
                        const branchNameText = '{{ __('sales_views.branch_name') }}';
                        const branchManagerText = '{{ __('sales_views.branch_manager_name') }}';
                        const managerPhoneText = '{{ __('sales_views.manager_phone') }}';
                        const cityLabel = '{{ __('sales_views.city') }}';
                        const addressText = '{{ __('sales_views.address') }}';

                        branchDetails += `
                        <tr>
                            <td colspan="2"><strong>${branchNumberText.replace(':number', index + 1)}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>${branchNameText}:</strong></td>
                            <td>${branchName.value}</td>
                        </tr>
                        <tr>
                            <td><strong>${branchManagerText}:</strong></td>
                            <td>${branchManager.value}</td>
                        </tr>
                        <tr>
                            <td><strong>${managerPhoneText}:</strong></td>
                            <td>${branchManagerPhone.value}</td>
                        </tr>
                        <tr>
                            <td><strong>${cityLabel}:</strong></td>
                            <td>${cityText}</td>
                        </tr>
                        <tr>
                            <td><strong>${addressText}:</strong></td>
                            <td>${branchAddress.value}</td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                    `;
                    }
                });

                const noBranchInfoText = '{{ __('sales_views.no_branch_information_available') }}';
                branchSummary.innerHTML = branchDetails || `<tr><td colspan="2">${noBranchInfoText}</td></tr>`;
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

        // Function to handle branch operations
        function setupBranchHandling() {
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
            }

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

            // Add Branch button functionality
            const addBranchBtn = document.getElementById('add-branch-btn');
            if (addBranchBtn) {
                addBranchBtn.addEventListener('click', function() {
                    const branchesContainer = document.getElementById('branches-container');
                    if (!branchesContainer) return;

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
            }

            // Add event listener to the first branch's remove button
            const firstBranchRemoveBtn = document.querySelector('.branch-form .remove-branch-btn');
            if (firstBranchRemoveBtn) {
                firstBranchRemoveBtn.addEventListener('click', function() {
                    const branches = document.querySelectorAll('.branch-form');
                    // Only remove if there's more than one branch
                    if (branches.length > 1) {
                        this.closest('.branch-form').remove();
                        reindexBranches();
                        updateRemoveButtons();
                    }
                });
            }
        }

        // Initialize stepper when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            stepper = new Stepper(document.querySelector('#stepper1'), {
                linear: true,
                animation: true
            });

            // Setup branch handling
            setupBranchHandling();

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

            // Handle payment type and schedule changes
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
                const numberOfPayments = parseInt(document.getElementById('number_of_payments').value || '0');

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

            // Add event listeners for payment type and schedule changes
            const paymentTypeSelect = document.getElementById('payment_type');
            if (paymentTypeSelect) {
                paymentTypeSelect.addEventListener('change', handlePaymentTypeChange);
                // Initial setup
                handlePaymentTypeChange();
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

            // Add email validation
            const emailField = document.getElementById('customer_email');
            if (emailField) {
                emailField.addEventListener('blur', function() {
                    checkEmailExists(this.value);
                });
            }
        });
    </script>

    <script>
        document.addEventListener('alpine:init', function() {
            Alpine.data('contractData', () => ({
                contractAmount: 0,
                first_payment_date: '',
                payment_type: 'prepaid',
                numberOfPayments: 1,
                payment_schedule: 'monthly',

                // Computed properties
                get vatAmount() {
                    return this.contractAmount ? parseFloat(this.contractAmount) * 0.15 : 0;
                },

                get totalAmount() {
                    return this.contractAmount ? parseFloat(this.contractAmount) + this.vatAmount :
                        0;
                },

                get installmentAmount() {
                    return this.numberOfPayments > 0 ? this.totalAmount / this.numberOfPayments : 0;
                }
            }));
        });
    </script>
@endpush
