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
        $property_types = ['Residential', 'Commercial', 'Industrial', 'Agricultural', 'Other'];
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

        /* Add loading indicator styles */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4361ee;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Load scripts with defer attribute to prevent blocking rendering -->
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Preload Saudi cities data to avoid processing during page load -->
    <script>
        // Preload Saudi cities data
        window.saudiCities = @json($saudiCities);
    </script>

    <div class="page-content">
        <div class="container">
            <div id="stepper1" class="bs-stepper">
                <div class="card">
                    <div class="card-header">
                        <div class="d-lg-flex flex-lg-row align-items-lg-center justify-content-lg-between" role="tablist">
                            <div class="step" data-target="#test-l-1">
                                <div class="step-trigger" role="tab" id="stepper1trigger1" aria-controls="test-l-1">
                                    <div class="bs-stepper-circle">1</div>
                                    <div class="">
                                        <h5 class="mb-0 steper-title">{{ __('contract_views.personal_information') }}</h5>
                                        <p class="mb-0 steper-sub-title">
                                            {{ __('contract_views.steper_sub_title') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bs-stepper-line"></div>
                            <div class="step" data-target="#test-l-2">
                                <div class="step-trigger" role="tab" id="stepper1trigger2" aria-controls="test-l-2">
                                    <div class="bs-stepper-circle">2</div>
                                    <div class="">
                                        <h5 class="mb-0 steper-title">{{ __('contract_views.contract_details') }}</h5>
                                        <p class="mb-0 steper-sub-title">{{ __('contract_views.steper_sub_title') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="bs-stepper-line"></div>
                            @if ($branches >= 1)
                                <div class="step" data-target="#test-l-3">
                                    <div class="step-trigger" role="tab" id="stepper1trigger3" aria-controls="test-l-3">
                                        <div class="bs-stepper-circle">3</div>
                                        <div class="">
                                            <h5 class="mb-0 steper-title">{{ __('contract_views.branch_information') }}</h5>
                                            <p class="mb-0 steper-sub-title">{{ __('contract_views.steper_sub_title') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="bs-stepper-line"></div>
                            <div class="step" data-target="#test-l-4">
                                <div class="step-trigger" role="tab" id="stepper1trigger4" aria-controls="test-l-4">
                                    <div class="bs-stepper-circle">4</div>
                                    <div class="">
                                        <h5 class="mb-0 steper-title">{{ __('contract_views.payment_information') }}</h5>
                                        <p class="mb-0 steper-sub-title">{{ __('contract_views.steper_sub_title') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bs-stepper-line"></div>
                            <div class="step" data-target="#test-l-5">
                                <div class="step-trigger" role="tab" id="stepper1trigger5" aria-controls="test-l-5">
                                    <div class="bs-stepper-circle">5</div>
                                    <div class="">
                                        <h5 class="mb-0 steper-title">{{ __('contract_views.summary') }}</h5>
                                        <p class="mb-0 steper-sub-title">{{ __('contract_views.steper_sub_title') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body position-relative">
                        <!-- Add loading overlay -->
                        <div id="loading-overlay" class="loading-overlay">
                            <div class="spinner"></div>
                        </div>
                        <div class="bs-stepper-content">
                            <form action="{{ route('contract.create') }}" method="POST" id="contractForm"
                                onsubmit="return validateForm(event)">
                                @csrf
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="contract_type_id" value="{{ $contract_type_id->id }}">
                                <input type="hidden" name="client_id"
                                    value="@isset($client_info){{ $client_info->id }}@endisset">
                                <input type="text" name="is_multi_branch"
                                    @if ($branches > 1) value="yes" @else value="no" @endif hidden readonly>
                                <input type="number" id="branchs_number" name="branchs_number" value={{ $branches }}
                                    hidden readonly>
                                <input type="hidden" name="Property_type" value="{{ $contract_type_id->name }}">
                                <div id="test-l-1" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper1trigger1">
                                    <h5 class="mb-1">{{ __('contract_views.personal_information') }}</h5>
                                    <p class="mb-4">{{ __('contract_views.enter_customer_information') }}</p>
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label for="FullName" class="form-label">{{ __('contract_views.name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="clientName" id="FullName"
                                                placeholder="{{ __('contract_views.name_placeholder') }}" required
                                                @isset($client_info)
                                            value="{{ $client_info->name }}" @endisset>
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_client_name') }}</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="Phone" class="form-label">{{ __('contract_views.phone_number') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="clientPhone" id="Phone"
                                                placeholder="{{ __('contract_views.phone_placeholder') }}" pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$"
                                                required
                                                @isset($client_info) value="{{ $client_info->phone }}" @endisset>
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_phone_number') }}</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="Mobile" class="form-label">{{ __('contract_views.mobile') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="clientMobile"
                                                id="Mobile" placeholder="{{ __('contract_views.phone_placeholder') }}"
                                                pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$" required
                                                @isset($client_info) value="{{ $client_info->mobile }}" @endisset>
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_mobile_number') }}</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="Email" class="form-label">{{ __('contract_views.email') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="clientEmail" id="Email"
                                                placeholder="{{ __('contract_views.email_placeholder') }}" required
                                                @isset($client_info)
                                            value="{{ $client_info->email }}" @endisset>
                                            <div class="invalid-feedback">{{ __('contract_views.invalid_email') }}</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="taxNumber" class="form-label">{{ __('contract_views.tax_number') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="client_tax_number"
                                                id="taxNumber" placeholder="{{ __('contract_views.tax_number_placeholder') }}" pattern="^[0-9]{15}$"
                                                required
                                                @isset($client_info) value="{{ $client_info->tax_number }}" @endisset>
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_tax_number') }}</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="zipCode" class="form-label">{{ __('contract_views.zip_code') }}</label>
                                            <input type="text" class="form-control" name="client_zipcode"
                                                id="zipCode" placeholder="{{ __('contract_views.zip_code_placeholder') }}" pattern="^[0-9]{5}$"
                                                @isset($client_info)
                                            value="{{ $client_info->zip_code }}" @endisset>
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_zipcode') }}</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="City" class="form-label">{{ __('contract_views.city') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" name="clientCity" id="City" required>
                                                <option value="">{{ __('contract_views.select_city') }}</option>
                                                @foreach ($saudiCities as $city)
                                                    <option value="{{ $city }}"
                                                        @isset($client_info) @if ($client_info->city == $city) selected @endif @endisset>
                                                        {{ $city }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">{{ __('contract_views.select_valid_city') }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label for="Address" class="form-label">{{ __('contract_views.address') }} <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="clientAddress" id="Address" rows="3" placeholder="{{ __('contract_views.address_placeholder') }}"
                                                required>
@isset($client_info)
{{ $client_info->address }}
@endisset
</textarea>
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_address') }}</div>
                                        </div>
                                        <div class="mb-3 col-12">
                                            <button type="button" class="px-4 btn btn-primary"
                                                onclick="handleNextStep()">{{ __('contract_views.next') }}<i
                                                    class='bx bx-right-arrow-alt ms-2'></i></button>
                                        </div>
                                    </div>
                                    <!---end row-->

                                </div>
                                <div id="test-l-2" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper1trigger2">

                                    <h5 class="mb-1">{{ __('contract_views.contract_details') }}</h5>
                                    <p class="mb-4">{{ __('contract_views.setup_contract_details') }}</p>

                                    <div class="row g-3" x-data="{
                                        branchCount: {{ $branches }},
                                    }">

                                        <div class="row g-3">
                                            <div class="col-12 col-lg-6">
                                                <label for="contractNumber" class="form-label">{{ __('contract_views.contract_number') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="contractnumber"
                                                    id="contractNumber" placeholder="{{ __('contract_views.contract_number_placeholder') }}"
                                                    value="{{ $contract_number }}" readonly>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <label for="start_date" class="form-label">{{ __('contract_views.start_date') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="contractstartdate"
                                                    id="contractstartdate" required>
                                                <div class="invalid-feedback">{{ __('contract_views.select_valid_start_date') }}</div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <label for="contractenddate" class="form-label">{{ __('contract_views.end_date') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="contractenddate"
                                                    id="contractenddate" required>
                                                <div class="invalid-feedback">{{ __('contract_views.select_valid_end_date') }}</div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <label for="visit_start_date" class="form-label">{{ __('contract_views.visit_start_date') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="visit_start_date"
                                                    id="visit_start_date" required>
                                                <div class="invalid-feedback">{{ __('contract_views.select_valid_visit_start_date') }}</div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <label for="contract_type" class="form-label">{{ __('contract_views.contract_type') }} <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" name="contract_type_id" id="contract_type_id"
                                                    required>
                                                    @foreach ($contract_types as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">{{ __('contract_views.select_contract_type_error') }}</div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <label for="Property_type" class="form-label">{{ __('contract_views.property_type') }} <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" name="Property_type" id="Property_type"
                                                    required>
                                                    @foreach ($property_types as $type)
                                                        <option value="{{ $type }}">{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">{{ __('contract_views.select_property_type_error') }}</div>
                                            </div>

                                            <div class="col-12">
                                                <label for="contractDescription" class="form-label">{{ __('contract_views.contract_description') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <textarea class="form-control" id="contractDescription" name="contract_description"
                                                        placeholder="{{ __('contract_views.enter_contract_description_placeholder') }}" rows="3" required minlength="10"></textarea>
                                                    <div class="invalid-feedback">{{ __('contract_views.provide_valid_description') }}</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <label for="number_of_visits" class="form-label">{{ __('contract_views.number_of_visits') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="number_of_visits"
                                                    id="number_of_visits" min="1" required value="1">
                                                <div class="invalid-feedback">{{ __('contract_views.provide_valid_number_of_visits') }}</div>
                                                <div class="form-text">{{ __('contract_views.minimum_visits_required') }}</div>
                                            </div>

                                            <div class="col-12 col-lg-6">
                                                <label for="warranty" class="form-label">{{ __('contract_views.warranty_period_months') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="warranty"
                                                    id="warranty" min="0" required value="0">
                                                <div class="invalid-feedback">{{ __('contract_views.provide_valid_warranty_period') }}</div>
                                            </div>

                                            <div class="col-12">
                                                <div class="gap-3 d-flex align-items-center">
                                                    <button type="button" class="px-4 btn btn-outline-secondary"
                                                        onclick="handlePreviousStep()"><i
                                                            class='bx bx-left-arrow-alt me-2'></i>{{ __('contract_views.previous') }}</button>
                                                    <button type="button" class="px-4 btn btn-primary"
                                                        onclick="handleNextStep();">{{ __('contract_views.next') }}<i
                                                            class='bx bx-right-arrow-alt ms-2'></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!---end row-->

                                    </div>
                                </div>
                                @if ($branches >= 1)
                                    <div id="test-l-3" role="tabpanel" class="bs-stepper-pane"
                                        aria-labelledby="stepper1trigger3">
                                        <h5 class="mb-1">{{ __('contract_views.branch_information') }}</h5>
                                        @for ($i = 0; $i < $branches; $i++)
                                            <div class="mb-3 card">
                                                <div class="card-header">
                                                    <h5 class="mb-1">{{ __('contract_views.branch') }} {{ $i + 1 }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-lg-6">
                                                            <label for="branchName{{ $i }}"
                                                                class="form-label">{{ __('contract_views.branch_name') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                name="branchName{{ $i }}"
                                                                id="branchName{{ $i }}"
                                                                placeholder="{{ __('contract_views.branch_name') }}" required>
                                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_branch_name') }}</div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <label for="branchmanager{{ $i }}"
                                                                class="form-label">{{ __('contract_views.branch_manager') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                name="branchmanager{{ $i }}"
                                                                id="branchmanager{{ $i }}"
                                                                placeholder="{{ __('contract_views.branch_manager') }}" required>
                                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_branch_manager') }}</div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <label for="branchphone{{ $i }}"
                                                                class="form-label">{{ __('contract_views.branch_phone') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                name="branchphone{{ $i }}"
                                                                id="branchphone{{ $i }}"
                                                                placeholder="{{ __('contract_views.phone_placeholder') }}" required
                                                                pattern="^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$">
                                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_branch_phone') }}</div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <label for="branchAddress{{ $i }}"
                                                                class="form-label">{{ __('contract_views.branch_address') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control"
                                                                name="branchAddress{{ $i }}"
                                                                id="branchAddress{{ $i }}"
                                                                placeholder="{{ __('contract_views.branch_address') }}" required>
                                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_branch_address') }}</div>
                                                        </div>
                                                        <div class="col-12 col-lg-6">
                                                            <label for="branchcity{{ $i }}"
                                                                class="form-label">{{ __('contract_views.city') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select"
                                                                name="branchcity{{ $i }}"
                                                                id="branchcity{{ $i }}" required>
                                                                <option value="">{{ __('contract_views.select_city') }}</option>
                                                                @foreach ($saudiCities as $city)
                                                                    <option value="{{ $city }}">
                                                                        {{ $city }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback">{{ __('contract_views.select_city_error') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="gap-3 d-flex align-items-center">
                                                    <button type="button" class="px-4 btn btn-outline-secondary"
                                                        onclick="handlePreviousStep()"><i
                                                            class='bx bx-left-arrow-alt me-2'></i>{{ __('contract_views.previous') }}</button>
                                                    <button type="button" class="px-4 btn btn-primary"
                                                        onclick="handleNextStep()">{{ __('contract_views.next') }}<i
                                                            class='bx bx-right-arrow-alt ms-2'></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div id="test-l-4" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper1trigger4">
                                    <h5 class="mb-1">{{ __('contract_views.payment_information') }}</h5>
                                    <p class="mb-4">{{ __('contract_views.enter_payment_information') }}</p>

                                    <div class="row g-3" x-data="{
                                        contractAmount: 0,
                                        payment_type: 'prepaid',
                                        payment_schedule: 'monthly',
                                        numberOfPayments: 1,
                                        first_payment_date: '',
                                        vatAmount: 0,
                                        totalAmount: 0,
                                        installmentAmount: 0,
                                        // Defer complex calculations
                                        calculateAmounts() {
                                            this.vatAmount = parseFloat((this.contractAmount * 0.15).toFixed(2));
                                            this.totalAmount = parseFloat((this.contractAmount * 1.15).toFixed(2));
                                            if (this.payment_type === 'postpaid' && this.numberOfPayments > 0) {
                                                this.installmentAmount = parseFloat((this.totalAmount / this.numberOfPayments).toFixed(2));
                                            }
                                        },
                                        initPaymentDates() {
                                            if (this.payment_schedule === 'monthly') {
                                                // Clear custom payment date fields when switching to monthly
                                                const container = document.getElementById('custom-payment-dates');
                                                if (container) {
                                                    container.innerHTML = '';
                                                }
                                            } else if (this.payment_schedule === 'custom') {
                                                // Defer generating custom payment date fields
                                                setTimeout(() => {
                                                    generateCustomPaymentDateFields(this.numberOfPayments);
                                                }, 50);
                                            }
                                        },
                                        // Initialize with a small delay to avoid blocking rendering
                                        init() {
                                            setTimeout(() => {
                                                this.calculateAmounts();
                                            }, 100);
                                        }
                                    }" x-init="init();
                                    $watch('contractAmount', value => calculateAmounts());
                                    $watch('numberOfPayments', value => {
                                        calculateAmounts();
                                        initPaymentDates();
                                    });
                                    $watch('payment_schedule', value => initPaymentDates());
                                    $watch('payment_type', value => calculateAmounts());">
                                        <div class="col-12 col-lg-6">
                                            <label for="Contractamount" class="form-label">{{ __('contract_details_new.contract_info_amount_without_vat') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="contractamount"
                                                id="Contractamount" x-model="contractAmount" required min="0"
                                                step="0.01">
                                            <div class="invalid-feedback">{{ __('contract_views.provide_valid_payment_amount') }}</div>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="first_payment_date" class="form-label">{{ __('contract_views.first_payment_date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="first_payment_date"
                                                id="first_payment_date" x-model="first_payment_date" required
                                                min="{{ date('Y-m-d') }}">
                                            <div class="invalid-feedback">{{ __('contract_views.select_payment_date') }}</div>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="payment_type" class="form-label">{{ __('contract_views.payment_type') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" name="payment_type" id="payment_type"
                                                x-model="payment_type" required>
                                                <option value="prepaid">{{ __('contract_views.prepaid') }}</option>
                                                <option value="postpaid">{{ __('contract_views.postpaid') }}</option>
                                            </select>
                                            <div class="invalid-feedback">{{ __('contract_views.select_payment_type_error') }}</div>
                                        </div>

                                        <template x-if="payment_type === 'postpaid'">
                                            <div class="col-12 col-lg-6">
                                                <label for="number_of_payments" class="form-label">{{ __('contract_views.number_of_payments') }}
                                                    <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="number_of_payments"
                                                    id="number_of_payments" x-model="numberOfPayments" required
                                                    min="1" max="24">
                                                <div class="invalid-feedback">{{ __('contract_views.provide_valid_number_of_payments') }}</div>
                                            </div>
                                        </template>

                                        <template x-if="payment_type === 'postpaid'">
                                            <div class="col-12 col-lg-6">
                                                <label for="payment_schedule" class="form-label">{{ __('contract_views.payment_schedule') }} <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" name="payment_schedule" id="payment_schedule"
                                                    x-model="payment_schedule" required>
                                                    <option value="monthly">{{ __('contract_views.monthly') }}</option>
                                                    <option value="custom">{{ __('contract_views.custom_dates') }}</option>
                                                </select>
                                                <div class="invalid-feedback">{{ __('contract_views.select_payment_schedule_error') }}</div>
                                            </div>
                                        </template>

                                        <template x-if="payment_type === 'postpaid'">
                                            <div class="col-12 col-lg-6">
                                                <label for="first_payment_date" class="form-label">{{ __('contract_views.first_payment_date') }}
                                                    <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="first_payment_date"
                                                    id="first_payment_date" x-model="first_payment_date" required
                                                    min="{{ date('Y-m-d') }}">
                                                <div class="invalid-feedback">{{ __('contract_views.select_payment_date') }}</div>
                                            </div>
                                        </template>

                                        <template x-if="payment_type === 'postpaid' && payment_schedule === 'custom'">
                                            <div class="col-12">
                                                <div class="p-3 rounded border">
                                                    <h6 class="mb-3">{{ __('contract_views.custom_dates') }}</h6>
                                                    <div id="custom-payment-dates">
                                                        <!-- Custom payment date fields will be generated here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <strong>{{ __('contract_details_new.vat_amount') }}:</strong> <span x-text="vatAmount.toFixed(2)"></span>
                                                {{ __('contract_views.currency') }}<br>
                                                <strong>{{ __('contract_views.total_amount') }}:</strong> <span
                                                    x-text="totalAmount.toFixed(2)"></span>
                                                {{ __('contract_views.currency') }}
                                                <template x-if="payment_type === 'postpaid'">
                                                    <div>
                                                        <strong>{{ __('contract_views.payment_amount') }}:</strong> <span
                                                            x-text="installmentAmount.toFixed(2)"></span>
                                                        {{ __('contract_views.currency') }}
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="gap-3 d-flex align-items-center">
                                            <button type="button" class="px-4 btn btn-outline-secondary"
                                                onclick="handlePreviousStep()">
                                                <i class='bx bx-left-arrow-alt me-2'></i>{{ __('contract_views.previous') }}
                                            </button>
                                            <button type="button" class="px-4 btn btn-primary"
                                                onclick="handleNextStep()">
                                                {{ __('contract_views.next') }}<i class='bx bx-right-arrow-alt ms-2'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="test-l-5" role="tabpanel" class="bs-stepper-pane"
                                    aria-labelledby="stepper1trigger5">
                                    <h5 class="mb-1">{{ __('contract_views.summary') }}</h5>
                                    <p class="mb-4">{{ __('contract_views.review_information') }}</p>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="mb-4 card-title">{{ __('contract_views.summary') }}</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 class="mb-3">{{ __('contract_views.client_information') }}</h6>
                                                            <table class="table table-borderless">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.name') }}:</strong></td>
                                                                        <td id="summary-client-name"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.mobile') }}:</strong></td>
                                                                        <td id="summary-client-mobile"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.email') }}:</strong></td>
                                                                        <td id="summary-client-email"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.tax_number') }}:</strong></td>
                                                                        <td id="summary-tax-number"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.city') }}:</strong></td>
                                                                        <td id="summary-client-city"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.address') }}:</strong></td>
                                                                        <td id="summary-client-address"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 class="mb-3">{{ __('contract_views.contract_details') }}</h6>
                                                            <table class="table table-borderless">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_details_new.contract_info_number') }}:</strong></td>
                                                                        <td id="summary-contract-number"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.start_date') }}:</strong></td>
                                                                        <td id="summary-contract-date-start"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.end_date') }}:</strong></td>
                                                                        <td id="summary-contract-date-end"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.visit_start_date') }}:</strong></td>
                                                                        <td id="summary-contract-date-visit-start"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.warranty') }}:</strong></td>
                                                                        <td id="summary-warranty"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 row">
                                                        <div class="col-md-6">
                                                            <h6 class="mb-3">{{ __('contract_views.payment_information') }}</h6>
                                                            <table class="table table-borderless">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.payment_type') }}:</strong></td>
                                                                        <td id="summary-payment-type"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.payment_schedule') }}:</strong></td>
                                                                        <td id="summary-payment-schedule"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.number_of_payments') }}:</strong></td>
                                                                        <td id="summary-number-payments"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_details_new.contract_info_amount_without_vat') }}:</strong></td>
                                                                        <td id="summary-contract-amount"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_details_new.vat_amount') }}:</strong></td>
                                                                        <td id="summary-vat-amount"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.total_amount') }}:</strong></td>
                                                                        <td id="summary-total-amount"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>{{ __('contract_views.first_payment_date') }}:</strong></td>
                                                                        <td id="summary-first-payment-date"></td>
                                                                    </tr>
                                                                    <tr id="summary-payment-dates-row"
                                                                        style="display: none;">
                                                                        <td colspan="2">
                                                                            <strong>{{ __('contract_views.payment_schedule') }}:</strong>
                                                                            <div id="summary-payment-dates"
                                                                                class="mt-2">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6" id="summary-payment-dates">
                                                            <h6 class="mb-3">{{ __('contract_views.payment_schedule') }}</h6>
                                                            <div id="payment-dates-list"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mt-4 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="handlePreviousStep()">{{ __('contract_views.edit') }}</button>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="cancelContract()">{{ __('contract_views.cancel') }}</button>
                                                <button type="submit" class="btn btn-success">{{ __('contract_views.save') }}</button>
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
    </div>
@endsection
@push('scripts')
    <script>
        var stepper;
        var currentStep = 0;
        var branchCount = 0;
        var validationCache = {}; // Cache validation results

        // Initialize only what's needed immediately
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading overlay when DOM is ready
            setTimeout(function() {
                const loadingOverlay = document.getElementById('loading-overlay');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }, 300); // Small delay to ensure UI is ready

            // Initialize variables after DOM is loaded
            branchCount = document.getElementById('branchs_number')?.value || 0;

            // Initialize stepper
            stepper = new Stepper(document.querySelector('.bs-stepper'), {
                linear: true,
                animation: true
            });

            // Set min date for date inputs to today - only for visible inputs
            const today = new Date().toISOString().split('T')[0];
            const visibleDateInputs = document.querySelectorAll(
                '#test-l-1 input[type="date"], #test-l-2 input[type="date"]');
            visibleDateInputs.forEach(input => {
                if (!input.min) {
                    input.min = today;
                }
            });

            // Lazy load other steps
            lazyLoadStepContent();
        });

        // Function to lazy load content for other steps
        function lazyLoadStepContent() {
            // Get all step content elements directly instead of using stepper.stepContent
            const stepContents = document.querySelectorAll('.bs-stepper-pane');

            if (stepContents && stepContents.length > 0) {
                stepContents.forEach((content, index) => {
                    content.addEventListener('transitionend', function(e) {
                        if (e.propertyName === 'transform' && window.getComputedStyle(content).display !==
                            'none') {
                            const dateInputs = content.querySelectorAll('input[type="date"]');
                            const today = new Date().toISOString().split('T')[0];
                            dateInputs.forEach(input => {
                                if (!input.min) {
                                    input.min = today;
                                }
                            });
                        }
                    });
                });
            }
        }

        // Add function to check for duplicates with caching
        async function checkDuplicateField(field, value) {
            // Check cache first
            const cacheKey = `${field}:${value}`;
            if (validationCache[cacheKey] !== undefined) {
                return validationCache[cacheKey];
            }

            try {
                const response = await fetch(
                    `{{ route('check.duplicate') }}?field=${field}&value=${encodeURIComponent(value)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                // Cache the result
                validationCache[cacheKey] = data.exists;
                return data.exists;
            } catch (error) {
                console.error('Error checking duplicate:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('contract_views.error') }}',
                    text: '{{ __('contract_views.duplicate_check_error') }}'
                });
                return false;
            }
        }

        async function validateClientInfo() {
            const form = document.getElementById('contractForm');
            const requiredFields = [{
                    name: 'clientName',
                    id: 'FullName',
                    pattern: /.+/,
                    message: '{{ __('contract_views.provide_valid_name') }}'
                },
                {
                    name: 'clientPhone',
                    id: 'Phone',
                    pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/,
                    message: '{{ __('contract_views.provide_valid_phone') }}'
                },
                {
                    name: 'clientMobile',
                    id: 'Mobile',
                    pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/,
                    message: '{{ __('contract_views.provide_valid_mobile') }}'
                },
                {
                    name: 'clientEmail',
                    id: 'Email',
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: '{{ __('contract_views.provide_valid_email') }}'
                },
                {
                    name: 'clientCity',
                    id: 'City',
                    pattern: /.+/,
                    message: '{{ __('contract_views.provide_valid_city') }}'
                },
                {
                    name: 'clientAddress',
                    id: 'Address',
                    pattern: /.+/,
                    message: '{{ __('contract_views.provide_valid_address') }}'
                },
                {
                    name: 'client_tax_number',
                    id: 'taxNumber',
                    pattern: /^[0-9]{15}$/,
                    message: '{{ __('contract_views.provide_valid_tax_number') }}'
                }
            ];

            let isValid = true;
            let errorMessages = [];

            // Reset validation state
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    element.classList.remove('is-invalid', 'is-valid');
                }
            });

            // Validate each field - use a more efficient approach
            const validationPromises = [];

            for (const field of requiredFields) {
                const element = document.getElementById(field.id);
                if (!element) continue;

                const value = element.value.trim();
                if (!value || !field.pattern.test(value)) {
                    element.classList.add('is-invalid');
                    isValid = false;

                    // Show specific error message
                    const feedback = element.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = field.message;
                    }
                    errorMessages.push(field.message);
                } else {
                    element.classList.add('is-valid');
                }
            }

            // If basic validation fails, show error message and return
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('contract_views.validation_error') }}',
                    html: errorMessages.join('<br>')
                });
                return false;
            }

            // If basic validation passes, check for duplicates if the client is new
            const client_id = document.querySelector('input[name="client_id"]').value;
            if (!client_id) {
                try {
                    const email = document.getElementById('Email').value;
                    const phone = document.getElementById('Phone').value;
                    const mobile = document.getElementById('Mobile').value;

                    // Check duplicates in parallel
                    const [emailExists, phoneExists, mobileExists] = await Promise.all([
                        checkDuplicateField('email', email),
                        checkDuplicateField('phone', phone),
                        checkDuplicateField('mobile', mobile)
                    ]);

                    let duplicateErrors = [];

                    if (emailExists) {
                        document.getElementById('Email').classList.add('is-invalid');
                        duplicateErrors.push('{{ __('contract_views.email_already_registered') }}');
                        isValid = false;
                    }

                    if (phoneExists) {
                        document.getElementById('Phone').classList.add('is-invalid');
                        duplicateErrors.push('{{ __('contract_views.phone_already_registered') }}');
                        isValid = false;
                    }

                    if (mobileExists) {
                        document.getElementById('Mobile').classList.add('is-invalid');
                        duplicateErrors.push('{{ __('contract_views.mobile_already_registered') }}');
                        isValid = false;
                    }

                    if (duplicateErrors.length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('contract_views.duplicate_entry') }}',
                            html: duplicateErrors.join('<br>')
                        });
                        return false;
                    }
                } catch (error) {
                    console.error('Error in duplicate check:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('contract_views.error') }}',
                        text: '{{ __('contract_views.validation_error_message') }}'
                    });
                    return false;
                }
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
            const visitStartDate = form.querySelector('[name="visit_start_date"]');
            const warranty = form.querySelector('[name="warranty"]');
            const contractType = form.querySelector('[name="contract_type_id"]');
            const propertyType = form.querySelector('[name="Property_type"]');
            const description = form.querySelector('[name="contract_description"]');
            const visits = form.querySelector('[name="number_of_visits"]');

            // Reset validation states
            [contractNumber, startDate, endDate, visitStartDate, warranty, contractType, propertyType, description, visits]
            .forEach(
                field => {
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

            // Validate visit start date
            if (!visitStartDate || !visitStartDate.value.trim()) {
                if (visitStartDate) {
                    visitStartDate.classList.add('is-invalid');
                    const feedback = visitStartDate.nextElementSibling;
                    if (feedback) feedback.textContent = '{{ __('contract_views.select_valid_visit_start_date') }}';
                }
                isValid = false;
            } else {
                visitStartDate.classList.add('is-valid');
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

                // Validate visit start date is between contract start and end dates
                const visitStart = document.getElementById('visit_start_date');
                if (visitStart && visitStart.value) {
                    const visitStartValue = new Date(visitStart.value);

                    if (visitStartValue < start) {
                        visitStart.classList.add('is-invalid');
                        const feedback = visitStart.nextElementSibling;
                        if (feedback) feedback.textContent = 'Visit start date must be on or after contract start date';
                        isValid = false;
                    } else if (visitStartValue > end) {
                        visitStart.classList.add('is-invalid');
                        const feedback = visitStart.nextElementSibling;
                        if (feedback) feedback.textContent = 'Visit start date must be on or before contract end date';
                        isValid = false;
                    }
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

        async function validateBranchInfo() {
            const form = document.getElementById('contractForm');
            let isValid = true;

            // Check if there are branches to validate
            if (branchCount < 1) {
                return true;
            }

            for (let i = 0; i < branchCount; i++) {
                const fields = [{
                        id: `branchName${i}`,
                        label: '{{ __('contract_views.branch_name') }}'
                    },
                    {
                        id: `branchmanager${i}`,
                        label: '{{ __('contract_views.branch_manager') }}'
                    },
                    {
                        id: `branchphone${i}`,
                        label: '{{ __('contract_views.branch_phone') }}',
                        pattern: /^(05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/
                    },
                    {
                        id: `branchAddress${i}`,
                        label: '{{ __('contract_views.branch_address') }}'
                    },
                    {
                        id: `branchcity${i}`,
                        label: '{{ __('contract_views.branch_city') }}'
                    }
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
                                feedback.textContent = field.pattern ?
                                    `Please enter a valid ${field.label.toLowerCase()}` :
                                    `Please enter the ${field.label.toLowerCase()}`;
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
                    text: '{{ __('contract_views.fill_in_all_branch_information_correctly') }}',
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
            if (!contractAmount || contractAmount.value === '' || parseFloat(contractAmount.value) < 0) {
                if (contractAmount) contractAmount.classList.add('is-invalid');
                isValid = false;
                errorMessage = '{{ __('contract_views.provide_valid_contract_amount') }}';
                return false;
            }
            if (contractAmount) contractAmount.classList.remove('is-invalid');

            // Get payment type
            const paymentType = form.querySelector('#payment_type');
            if (!paymentType || !paymentType.value || !['prepaid', 'postpaid'].includes(paymentType.value)) {
                if (paymentType) paymentType.classList.add('is-invalid');
                isValid = false;
                errorMessage = '{{ __('contract_views.provide_valid_payment_type') }}';
                return false;
            }
            if (paymentType) paymentType.classList.remove('is-invalid');

            // Validate first payment date (required for both prepaid and postpaid)
            const firstPaymentDate = form.querySelector('#first_payment_date');
            if (!firstPaymentDate || !firstPaymentDate.value) {
                if (firstPaymentDate) firstPaymentDate.classList.add('is-invalid');
                isValid = false;
                errorMessage = '{{ __('contract_views.provide_valid_payment_date') }}';
                return false;
            }
            if (firstPaymentDate) firstPaymentDate.classList.remove('is-invalid');

            if (paymentType.value === 'postpaid') {
                // Validate number of payments
                const numberOfPayments = form.querySelector('#number_of_payments');
                if (!numberOfPayments || !numberOfPayments.value ||
                    parseInt(numberOfPayments.value) < 1 || parseInt(numberOfPayments.value) > 24) {
                    if (numberOfPayments) numberOfPayments.classList.add('is-invalid');
                    isValid = false;
                    errorMessage = '{{ __('contract_views.provide_valid_number_of_payments') }}';
                    return false;
                }
                if (numberOfPayments) numberOfPayments.classList.remove('is-invalid');

                // Validate payment schedule
                const paymentSchedule = form.querySelector('#payment_schedule');
                if (!paymentSchedule || !paymentSchedule.value) {
                    if (paymentSchedule) paymentSchedule.classList.add('is-invalid');
                    isValid = false;
                    errorMessage = '{{ __('contract_views.provide_valid_payment_schedule') }}';
                    return false;
                }
                if (paymentSchedule) paymentSchedule.classList.remove('is-invalid');

                // Validate custom payment dates if custom schedule is selected
                if (paymentSchedule.value === 'custom') {
                    const customDateInputs = form.querySelectorAll('[id^="payment_date_"]');
                    if (customDateInputs && customDateInputs.length > 0) {
                        let previousDate = new Date(firstPaymentDate.value);

                        for (let i = 0; i < customDateInputs.length; i++) {
                            const input = customDateInputs[i];
                            if (!input.value) {
                                input.classList.add('is-invalid');
                                isValid = false;
                                errorMessage = '{{ __('contract_views.provide_valid_payment_dates') }}';
                                return false;
                            }

                            const currentDate = new Date(input.value);
                            if (currentDate <= previousDate) {
                                input.classList.add('is-invalid');
                                isValid = false;
                                errorMessage = '{{ __('contract_views.provide_valid_payment_dates') }}';
                                return false;
                            }

                            input.classList.remove('is-invalid');
                            previousDate = currentDate;
                        }
                    }
                }
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage || '{{ __('contract_views.fill_in_all_payment_information_correctly') }}'
                });
            }

            return isValid;
        }

        async function handleNextStep() {
            let isValid = true;

            switch (currentStep) {
                case 0:
                    isValid = await validateClientInfo();
                    break;
                case 1:
                    isValid = await validateContractDetails();
                    break;
                case 2:
                    isValid = await validateBranchInfo();
                    break;
                case 3:
                    isValid = await validatePaymentInfo();
                    break;
                default:
                    break;
            }

            if (isValid) {
                if (currentStep === 3) {
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

        async function validateForm(event) {
            event.preventDefault();

            try {
                // Validate client info first (this includes duplicate checks)
                const clientInfoValid = await validateClientInfo();
                if (!clientInfoValid) {
                    stepper.to(0); // Go to client info step
                    return false;
                }

                // Validate contract details
                const contractDetailsValid = await validateContractDetails();
                if (!contractDetailsValid) {
                    stepper.to(1); // Go to contract details step
                    return false;
                }

                // Validate branch info if applicable
                const branchInfoValid = await validateBranchInfo();
                if (!branchInfoValid) {
                    stepper.to(2); // Go to branch info step
                    return false;
                }

                // Validate payment info
                const paymentInfoValid = await validatePaymentInfo();
                if (!paymentInfoValid) {
                    stepper.to(3); // Go to payment info step
                    return false;
                }

                // If all validations pass, show confirmation dialog
                Swal.fire({
                    title: '{{ __('contract_views.confirm_contract_submission') }}',
                    text: '{{ __('contract_views.are_you_sure_you_want_to_submit_this_contract') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('contract_views.yes_submit_it') }}',
                    cancelButtonText: 'No, review again'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        document.getElementById('contractForm').submit();
                    }
                });
            } catch (error) {
                console.error('Validation error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('contract_views.error') }}',
                    text: '{{ __('contract_views.error_occurred_during_validation') }}'
                });
                return false;
            }

            return false; // Prevent form submission until confirmation
        }

        function cancelContract() {
            Swal.fire({
                title: '{{ __('contract_views.cancel_contract') }}',
                text: '{{ __('contract_views.are_you_sure_you_want_to_cancel_this_contract') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('contract_views.yes_cancel_it') }}',
                cancelButtonText: '{{ __('contract_views.no_keep_editing') }}'
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
                        '{{ __('contract_views.contract_cancelled') }}',
                        '{{ __('contract_views.contract_has_been_cancelled') }}',
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
                    title: '{{ __('contract_views.first_payment_date_required') }}',
                    text: '{{ __('contract_views.please_select_first_payment_date_before_setting_custom_dates') }}'
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
                label.innerHTML = `{{ __('contract_views.payment_date') }} ${i + 1} <span class="text-danger">*</span>`;

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
                document.getElementById('summary-client-city').textContent = document.getElementById('City')?.options[
                    document.getElementById('City')?.selectedIndex]?.text || '';
                document.getElementById('summary-client-address').textContent = getInputValue('Address');

                // Contract Details
                document.getElementById('summary-contract-number').textContent = getInputValue('contractNumber');
                document.getElementById('summary-contract-date-start').textContent = getInputValue('contractstartdate');
                document.getElementById('summary-contract-date-end').textContent = getInputValue('contractenddate');
                document.getElementById('summary-contract-date-visit-start').textContent = getInputValue(
                    'visit_start_date');
                document.getElementById('summary-warranty').textContent = getInputValue('warranty') + ' months';

                // Get contract type
                const contractTypeSelect = document.getElementById('contract_type_id');
                const contractType = contractTypeSelect ? contractTypeSelect.options[contractTypeSelect.selectedIndex]
                    ?.text : '';

                // Payment Information
                const paymentType = getInputValue('payment_type');
                const paymentSchedule = document.querySelector('[name="payment_schedule"]')?.value || 'monthly';
                const numberOfPayments = getInputValue('number_of_payments');
                const contractAmount = parseFloat(getInputValue('Contractamount')) || 0;
                const vatAmount = contractAmount * 0.15;
                const totalAmount = contractAmount * 1.15;

                document.getElementById('summary-payment-type').textContent = paymentType.charAt(0).toUpperCase() +
                    paymentType.slice(1);
                document.getElementById('summary-payment-schedule').textContent = paymentSchedule.charAt(0).toUpperCase() +
                    paymentSchedule.slice(1);
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
                if (branchsNumber >= 1) {
                    const branchInfo = document.createElement('div');
                    branchInfo.className = 'col-md-12 mt-4';

                    let branchTableHtml = `
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
                            <tbody>`;

                    // Generate table rows properly
                    for (let i = 0; i < branchsNumber; i++) {
                        const branchName = getInputValue('branchName' + i);
                        const branchManager = getInputValue('branchmanager' + i);
                        const branchPhone = getInputValue('branchphone' + i);
                        const branchCity = document.getElementById('branchcity' + i)?.options[
                            document.getElementById('branchcity' + i)?.selectedIndex]?.text || '';
                        const branchAddress = getInputValue('branchAddress' + i);

                        branchTableHtml += `
                                <tr>
                                    <td>${branchName}</td>
                                    <td>${branchManager}</td>
                                    <td>${branchPhone}</td>
                                    <td>${branchCity}</td>
                                    <td>${branchAddress}</td>
                                </tr>`;
                    }

                    branchTableHtml += `
                            </tbody>
                        </table>
                    </div>`;

                    branchInfo.innerHTML = branchTableHtml;

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
                    title: '{{ __('contract_views.error') }}',
                    text: '{{ __('contract_views.error_occurred_during_summary_update') }}'
                });
            }
        }

        function clearValidationMessages() {
            // Clear all validation states and messages
            document.querySelectorAll('.is-invalid, .is-valid').forEach(element => {
                element.classList.remove('is-invalid', 'is-valid');
            });

            // Clear all error messages
            document.querySelectorAll('.invalid-feedback').forEach(element => {
                element.textContent = '';
            });

            // Reset validation cache
            validationCache = {};
        }
    </script>
@endpush
