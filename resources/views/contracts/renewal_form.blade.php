@extends('shared.dashboard')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    .page-content {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 1.5rem;
    }

    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.12);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.625rem 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4c6fff;
        box-shadow: 0 0 0 3px rgba(76, 111, 255, 0.1);
    }

    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(45deg, #4c6fff, #6e8fff);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #3955d8, #5d7fff);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f0f2f5;
        color: #4a5568;
        border: none;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #2d3748;
    }

    .alert {
        border-radius: 8px;
        border: none;
    }

    .alert-success {
        background-color: #e6f6e6;
        color: #276749;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #9b2c2c;
    }

    .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
    }

    legend {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
        width: 100%;
    }

    fieldset {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
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

    <div class="container mt-4">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h2>{{ __('contract_views.renew_contract') }} #{{ $contract->contract_number }}</h2>
            <a href="{{ route('completed.show.all') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('contract_views.back_to_completed_contracts') }}
            </a>
        </div>

        <div class="mb-4 alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            {{ __('contract_views.renewing_contract_info', ['number' => $contract->contract_number, 'name' => $contract->customer->name]) }}.
            {{ __('contract_views.new_contract_info') }}
        </div>

        <form action="{{ route('contract.renewal.process', $contract->id) }}" method="POST" id="contract-form">
            @csrf
            <fieldset class="mb-4">
                <legend>{{ __('contract_views.contract_information') }}</legend>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="contract_type">{{ __('contract_views.contract_type') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.select_contract_type_tooltip') }}">(?)</span></label>
                            <select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type"
                                name="contract_type" required>
                                <option value="">{{ __('contract_views.select_contract_type') }}</option>
                                @foreach($contract_types as $type)
                                <option value="{{ $type->id }}" {{ $contract->contract_type == $type->id ? 'selected' :
                                    '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('contract_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="Property_type">{{ __('contract_views.property_type') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.select_property_type_tooltip') }}">(?)</span></label>
                            <select class="form-select @error('Property_type') is-invalid @enderror" id="Property_type"
                                name="Property_type" required>
                                <option value="">{{ __('contract_views.select_property_type') }}</option>
                                @foreach($property_types as $type)
                                <option value="{{ $type }}" {{ $contract->Property_type == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                                @endforeach
                            </select>
                            @error('Property_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3 form-group">
                    <label for="contract_description">{{ __('contract_views.contract_description') }} <span data-toggle="tooltip"
                            title="{{ __('contract_views.contract_description_tooltip') }}">(?)</span></label>
                    <textarea class="form-control @error('contract_description') is-invalid @enderror"
                        id="contract_description" name="contract_description" rows="4"
                        required>{{ $contract->contract_description }}</textarea>
                    @error('contract_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="mb-3 form-group">
                            <label for="contract_start_date">{{ __('contract_views.start_date') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.start_date_tooltip') }}">(?)</span></label>
                            <input type="date" class="form-control @error('contract_start_date') is-invalid @enderror"
                                id="contract_start_date" name="contract_start_date" value="{{ date('Y-m-d') }}"
                                required>
                            @error('contract_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 form-group">
                            <label for="contract_end_date">{{ __('contract_views.end_date') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.end_date_tooltip') }}">(?)</span></label>
                            <input type="date" class="form-control @error('contract_end_date') is-invalid @enderror"
                                id="contract_end_date" name="contract_end_date"
                                value="{{ date('Y-m-d', strtotime('+1 year')) }}" required>
                            @error('contract_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 form-group">
                            <label for="visit_start_date">{{ __('contract_views.visit_start_date') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.visit_start_date_tooltip') }}">(?)</span></label>
                            <input type="date" class="form-control @error('visit_start_date') is-invalid @enderror"
                                id="visit_start_date" name="visit_start_date"
                                value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            @error('visit_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="mb-3 form-group">
                            <label for="warranty">{{ __('contract_views.warranty_period_months') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.warranty_period_tooltip') }}">(?)</span></label>
                            <input type="number" class="form-control @error('warranty') is-invalid @enderror"
                                id="warranty" name="warranty" value="{{ $contract->warranty }}" min="0" required>
                            @error('warranty')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 form-group">
                            <label for="number_of_visits">{{ __('contract_views.number_of_visits') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.number_of_visits_tooltip') }}">(?)</span></label>
                            <input type="number" class="form-control @error('number_of_visits') is-invalid @enderror"
                                id="number_of_visits" name="number_of_visits" value="{{ $contract->number_of_visits }}"
                                min="1" required>
                            @error('number_of_visits')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 form-group">
                            <label for="contract_price">{{ __('contract_views.contract_price') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.contract_price_tooltip') }}">(?)</span></label>
                            <input type="number" class="form-control @error('contract_price') is-invalid @enderror"
                                id="contract_price" name="contract_price" value="{{ $contract->contract_price / 1.15 }}"
                                min="0" step="0.01" required>
                            <small class="text-muted">{{ __('contract_details_new.vat_amount') }}: <span id="vatAmount">{{
                                    number_format($contract->contract_price * 0.15 / 1.15, 2) }}</span> {{ __('contract_views.currency') }}</small>
                            <small class="d-block text-muted">{{ __('contract_views.total_with_vat') }}: <span id="totalWithVat">{{
                                    number_format($contract->contract_price, 2) }}</span> {{ __('contract_views.currency') }}</small>
                            @error('contract_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="mb-4" id="buy-equipment-fields" style="display: none;">
                <legend>{{ __('contract_views.equipment_information') }}</legend>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="equipment_type_id">{{ __('contract_views.equipment_type') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.equipment_type_tooltip') }}">(?)</span></label>
                            <select class="form-select @error('equipment_type_id') is-invalid @enderror"
                                id="equipment_type_id" name="equipment_type_id"
                                value="{{ $contract->equipment_type_id ?? '' }}">
                                <option value="">{{ __('contract_views.select_equipment_type') }}</option>
                                @foreach($equipment_types ?? [] as $type)
                                <option value="{{ $type->id }}" {{ isset($contract->equipment_type_id) &&
                                    $contract->equipment_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('equipment_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="equipment_model">{{ __('contract_views.equipment_model') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.equipment_model_tooltip') }}">(?)</span></label>
                            <input type="text" class="form-control @error('equipment_model') is-invalid @enderror"
                                id="equipment_model" name="equipment_model"
                                value="{{ $contract->equipment_model ?? '' }}">
                            @error('equipment_model')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="equipment_quantity">{{ __('contract_views.equipment_quantity') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.equipment_quantity_tooltip') }}">(?)</span></label>
                            <input type="number" class="form-control @error('equipment_quantity') is-invalid @enderror"
                                id="equipment_quantity" name="equipment_quantity"
                                value="{{ $contract->equipment_quantity ?? 1 }}" min="1">
                            @error('equipment_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="warranty">Warranty Period (Months) <span data-toggle="tooltip"
                                    title="Enter the warranty period in months">(?)</span></label>
                            <input type="number" class="form-control @error('warranty') is-invalid @enderror"
                                id="warranty" name="warranty" value="{{ $contract->warranty ?? 0 }}" min="0">
                            @error('warranty')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3 form-group">
                    <label for="equipment_description">Equipment Description <span data-toggle="tooltip"
                            title="Provide a detailed description of the equipment">(?)</span></label>
                    <textarea class="form-control @error('equipment_description') is-invalid @enderror"
                        id="equipment_description" name="equipment_description"
                        rows="4">{{ $contract->equipment_description ?? '' }}</textarea>
                    @error('equipment_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </fieldset>

            <fieldset class="mb-4">
                <legend>{{ __('contract_views.client_information') }}</legend>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="client_name">{{ __('contract_views.client_name') }}</label>
                            <input type="text" class="form-control" id="client_name"
                                value="{{ $contract->customer->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="client_email">{{ __('contract_views.email') }}</label>
                            <input type="email" class="form-control" id="client_email"
                                value="{{ $contract->customer->email }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="client_phone">{{ __('contract_views.phone') }}</label>
                            <input type="text" class="form-control" id="client_phone"
                                value="{{ $contract->customer->phone }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="client_address">{{ __('contract_views.address') }}</label>
                            <input type="text" class="form-control" id="client_address"
                                value="{{ $contract->customer->address }}" readonly>
                        </div>
                    </div>
                </div>
            </fieldset>

            @if($contract->branchs && $contract->branchs->count() > 0)
            <fieldset class="mb-4">
                <legend>{{ __('contract_views.branch_information') }}</legend>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('contract_views.contract_has_branches', ['count' => $contract->branchs->count()]) }} {{ $contract->branchs->count() == 1 ? __('contract_views.branch') : __('contract_views.branches') }}. {{ __('contract_views.branch_copy_info') }}
                </div>

                <div id="branches-container">
                    @foreach($contract->branchs as $index => $branch)
                    <div class="mb-4 card branch-card" data-branch-id="{{ $branch->id }}">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-building me-2"></i>
                                {{ __('contract_views.branch') }} #{{ $index + 1 }}: {{ $branch->branch_name }}
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input branch-include" type="checkbox"
                                    id="include_branch_{{ $branch->id }}" name="include_branches[]"
                                    value="{{ $branch->id }}" checked>
                                <label class="form-check-label" for="include_branch_{{ $branch->id }}">{{ __('contract_views.include_in_renewal') }}</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <label>{{ __('contract_views.branch_name') }}</label>
                                        <input type="text" class="form-control" value="{{ $branch->branch_name }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <label>{{ __('contract_views.branch_manager') }}</label>
                                        <input type="text" class="form-control"
                                            value="{{ $branch->branch_manager_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <label>{{ __('contract_views.phone') }}</label>
                                        <input type="text" class="form-control"
                                            value="{{ $branch->branch_manager_phone }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 form-group">
                                        <label>{{ __('contract_views.city') }}</label>
                                        <input type="text" class="form-control" value="{{ $branch->branch_city }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 form-group">
                                <label>{{ __('contract_views.address') }}</label>
                                <input type="text" class="form-control" value="{{ $branch->branch_address }}" readonly>
                            </div>

                            <div class="branch-edit-container" style="display: none;">
                                <hr>
                                <h6 class="mb-3">{{ __('contract_views.edit_branch_information') }}</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3 form-group">
                                            <label for="branch_name_{{ $branch->id }}">{{ __('contract_views.branch_name') }}</label>
                                            <input type="text" class="form-control" id="branch_name_{{ $branch->id }}"
                                                name="branch_data[{{ $branch->id }}][branch_name]"
                                                value="{{ $branch->branch_name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-group">
                                            <label for="branch_manager_{{ $branch->id }}">{{ __('contract_views.branch_manager') }}</label>
                                            <input type="text" class="form-control"
                                                id="branch_manager_{{ $branch->id }}"
                                                name="branch_data[{{ $branch->id }}][branch_manager_name]"
                                                value="{{ $branch->branch_manager_name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3 form-group">
                                            <label for="branch_phone_{{ $branch->id }}">{{ __('contract_views.phone') }}</label>
                                            <input type="text" class="form-control" id="branch_phone_{{ $branch->id }}"
                                                name="branch_data[{{ $branch->id }}][branch_manager_phone]"
                                                value="{{ $branch->branch_manager_phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-group">
                                            <label for="branch_city_{{ $branch->id }}">{{ __('contract_views.city') }}</label>
                                            <select class="form-select" id="branch_city_{{ $branch->id }}"
                                                name="branch_data[{{ $branch->id }}][branch_city]">
                                                @foreach($saudiCities as $city)
                                                <option value="{{ $city }}" {{ $branch->branch_city == $city ?
                                                    'selected' : '' }}>{{ $city }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="branch_address_{{ $branch->id }}">{{ __('contract_views.address') }}</label>
                                    <input type="text" class="form-control" id="branch_address_{{ $branch->id }}"
                                        name="branch_data[{{ $branch->id }}][branch_address]"
                                        value="{{ $branch->branch_address }}">
                                </div>
                            </div>

                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm toggle-edit-branch"
                                    data-branch-id="{{ $branch->id }}">
                                    <i class="fas fa-edit me-1"></i> {{ __('contract_views.edit_branch') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <button type="button" id="add-new-branch" class="btn btn-outline-success">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('contract_views.add_new_branch') }}
                    </button>
                </div>
            </fieldset>
            @endif

            <fieldset class="mb-4">
                <legend>{{ __('contract_views.payment_information') }}</legend>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="payment_type">{{ __('contract_views.payment_type') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.payment_type_tooltip') }}">(?)</span></label>
                            <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type"
                                name="payment_type" required>
                                <option value="prepaid" {{ $contract->payment_type == 'prepaid' ? 'selected' : ''
                                    }}>{{ __('contract_views.prepaid_full') }}</option>
                                <option value="postpaid" {{ $contract->payment_type == 'postpaid' ? 'selected' : ''
                                    }}>{{ __('contract_views.postpaid_installments') }}</option>
                            </select>
                            @error('payment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 payment-details" id="postpaid-details"
                        style="{{ $contract->payment_type == 'postpaid' ? '' : 'display: none;' }}">
                        <div class="mb-3 form-group">
                            <label for="number_of_payments">{{ __('contract_views.number_of_payments') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.number_of_payments_tooltip') }}">(?)</span></label>
                            <input type="number" class="form-control @error('number_of_payments') is-invalid @enderror"
                                id="number_of_payments" name="number_of_payments"
                                value="{{ $contract->number_Payments ?? 3 }}" min="1" max="12">
                            @error('number_of_payments')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3 form-group">
                            <label for="first_payment_date">{{ __('contract_views.first_payment_date') }} <span data-toggle="tooltip"
                                    title="{{ __('contract_views.first_payment_date_tooltip') }}">(?)</span></label>
                            <input type="date" class="form-control @error('first_payment_date') is-invalid @enderror"
                                id="first_payment_date" name="first_payment_date" value="{{ date('Y-m-d') }}" required>
                            @error('first_payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('completed.show.all') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> {{ __('contract_views.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-1"></i> {{ __('contract_views.renew_contract') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Contract type toggle for Buy equipment
        const contractTypeSelect = document.getElementById('contract_type');
        const buyEquipmentFields = document.getElementById('buy-equipment-fields');
        const visitFields = document.getElementById('number_of_visits').closest('.col-md-4');
        
        // Check initial state
        checkContractType();
        
        // Add event listener for contract type changes
        contractTypeSelect.addEventListener('change', checkContractType);
        
        function checkContractType() {
            // Find the selected option text
            const selectedOption = contractTypeSelect.options[contractTypeSelect.selectedIndex];
            const contractTypeName = selectedOption.textContent.trim();
            
            if (contractTypeName === 'Buy equipment') {
                buyEquipmentFields.style.display = 'block';
                visitFields.style.display = 'none'; // Hide visits field for Buy equipment
                document.getElementById('number_of_visits').removeAttribute('required');
                document.getElementById('equipment_type_id').setAttribute('required', 'required');
                document.getElementById('equipment_model').setAttribute('required', 'required');
                document.getElementById('equipment_quantity').setAttribute('required', 'required');
                document.getElementById('equipment_description').setAttribute('required', 'required');
            } else {
                buyEquipmentFields.style.display = 'none';
                visitFields.style.display = 'block'; // Show visits field for other contract types
                document.getElementById('number_of_visits').setAttribute('required', 'required');
                document.getElementById('equipment_type_id').removeAttribute('required');
                document.getElementById('equipment_model').removeAttribute('required');
                document.getElementById('equipment_quantity').removeAttribute('required');
                document.getElementById('equipment_description').removeAttribute('required');
            }
        }
        
        // Payment type toggle
        const paymentTypeSelect = document.getElementById('payment_type');
        const postpaidDetails = document.getElementById('postpaid-details');

        paymentTypeSelect.addEventListener('change', function() {
            if (this.value === 'postpaid') {
                postpaidDetails.style.display = '';
                document.getElementById('number_of_payments').setAttribute('required', 'required');
            } else {
                postpaidDetails.style.display = 'none';
                document.getElementById('number_of_payments').removeAttribute('required');
            }
        });

        // Contract price calculation
        const contractPriceInput = document.getElementById('contract_price');
        const vatAmountSpan = document.getElementById('vatAmount');
        const totalWithVatSpan = document.getElementById('totalWithVat');

        contractPriceInput.addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const vat = price * 0.15;
            const total = price + vat;

            vatAmountSpan.textContent = vat.toFixed(2);
            totalWithVatSpan.textContent = total.toFixed(2);
        });

        // Date validation
        const startDateInput = document.getElementById('contract_start_date');
        const endDateInput = document.getElementById('contract_end_date');
        const visitStartDateInput = document.getElementById('visit_start_date');

        startDateInput.addEventListener('change', function() {
            const startDate = new Date(this.value);
            
            // End date must be after start date
            const endDate = new Date(endDateInput.value);
            if (endDate <= startDate) {
                const newEndDate = new Date(startDate);
                newEndDate.setFullYear(newEndDate.getFullYear() + 1);
                endDateInput.value = newEndDate.toISOString().split('T')[0];
            }
            
            // Visit start date must be between start and end dates
            const visitStartDate = new Date(visitStartDateInput.value);
            if (visitStartDate < startDate) {
                visitStartDateInput.value = this.value;
            }
            
            // Set min attribute for end date and visit start date
            endDateInput.min = this.value;
            visitStartDateInput.min = this.value;
        });

        endDateInput.addEventListener('change', function() {
            const endDate = new Date(this.value);
            const visitStartDate = new Date(visitStartDateInput.value);
            
            // Visit start date must be before end date
            if (visitStartDate > endDate) {
                visitStartDateInput.value = this.value;
            }
            
            // Set max attribute for visit start date
            visitStartDateInput.max = this.value;
        });

        // Branch management
        const branchesContainer = document.getElementById('branches-container');
        const addNewBranchBtn = document.getElementById('add-new-branch');
        
        if (branchesContainer && addNewBranchBtn) {
            // Toggle branch edit mode
            document.querySelectorAll('.toggle-edit-branch').forEach(button => {
                button.addEventListener('click', function() {
                    const branchId = this.getAttribute('data-branch-id');
                    const branchCard = document.querySelector(`.branch-card[data-branch-id="${branchId}"]`);
                    const editContainer = branchCard.querySelector('.branch-edit-container');
                    
                    if (editContainer.style.display === 'none') {
                        editContainer.style.display = 'block';
                        this.innerHTML = '<i class="fas fa-times me-1"></i> {{ __('contract_views.cancel_edit') }}';
                        this.classList.replace('btn-outline-primary', 'btn-outline-danger');
                    } else {
                        editContainer.style.display = 'none';
                        this.innerHTML = '<i class="fas fa-edit me-1"></i> {{ __('contract_views.edit_branch') }}';
                        this.classList.replace('btn-outline-danger', 'btn-outline-primary');
                    }
                });
            });
            
            // Branch inclusion toggle
            document.querySelectorAll('.branch-include').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const branchId = this.getAttribute('value');
                    const branchCard = document.querySelector(`.branch-card[data-branch-id="${branchId}"]`);
                    
                    if (this.checked) {
                        branchCard.style.opacity = '1';
                        branchCard.querySelectorAll('input, select').forEach(input => {
                            if (input !== this) {
                                input.removeAttribute('disabled');
                            }
                        });
                    } else {
                        branchCard.style.opacity = '0.6';
                        branchCard.querySelectorAll('input, select').forEach(input => {
                            if (input !== this) {
                                input.setAttribute('disabled', 'disabled');
                            }
                        });
                    }
                });
            });
            
            // Add new branch
            let newBranchCounter = 0;
            
            addNewBranchBtn.addEventListener('click', function() {
                newBranchCounter++;
                const newBranchId = `new_${newBranchCounter}`;
                
                const branchTemplate = `
                <div class="mb-4 card branch-card" data-branch-id="${newBranchId}">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            {{ __('contract_views.new_branch') }}
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input branch-include" type="checkbox" id="include_branch_${newBranchId}" name="include_branches[]" value="${newBranchId}" checked>
                            <label class="form-check-label" for="include_branch_${newBranchId}">{{ __('contract_views.include_in_renewal') }}</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="branch_name_${newBranchId}">{{ __('contract_views.branch_name') }}</label>
                                    <input type="text" class="form-control" id="branch_name_${newBranchId}" name="new_branch_data[${newBranchId}][branch_name]" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="branch_manager_${newBranchId}">{{ __('contract_views.branch_manager') }}</label>
                                    <input type="text" class="form-control" id="branch_manager_${newBranchId}" name="new_branch_data[${newBranchId}][branch_manager_name]" required>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="branch_phone_${newBranchId}">{{ __('contract_views.phone') }}</label>
                                    <input type="text" class="form-control" id="branch_phone_${newBranchId}" name="new_branch_data[${newBranchId}][branch_manager_phone]" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="branch_city_${newBranchId}">{{ __('contract_views.city') }}</label>
                                    <select class="form-select" id="branch_city_${newBranchId}" name="new_branch_data[${newBranchId}][branch_city]" required>
                                        <option value="">{{ __('contract_views.select_city') }}</option>
                                        ${getSaudiCitiesOptions()}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="branch_address_${newBranchId}">{{ __('contract_views.address') }}</label>
                            <input type="text" class="form-control" id="branch_address_${newBranchId}" name="new_branch_data[${newBranchId}][branch_address]" required>
                        </div>
                        
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-branch" data-branch-id="${newBranchId}">
                                <i class="fas fa-trash me-1"></i> {{ __('contract_views.remove_branch') }}
                            </button>
                        </div>
                    </div>
                </div>
                `;
                
                branchesContainer.insertAdjacentHTML('beforeend', branchTemplate);
                
                // Add event listener to the new remove button
                const removeBtn = branchesContainer.querySelector(`.remove-branch[data-branch-id="${newBranchId}"]`);
                removeBtn.addEventListener('click', function() {
                    const branchId = this.getAttribute('data-branch-id');
                    const branchCard = document.querySelector(`.branch-card[data-branch-id="${branchId}"]`);
                    branchCard.remove();
                });
                
                // Add event listener to the new include checkbox
                const includeCheckbox = branchesContainer.querySelector(`#include_branch_${newBranchId}`);
                includeCheckbox.addEventListener('change', function() {
                    const branchId = this.getAttribute('value');
                    const branchCard = document.querySelector(`.branch-card[data-branch-id="${branchId}"]`);
                    
                    if (this.checked) {
                        branchCard.style.opacity = '1';
                        branchCard.querySelectorAll('input, select').forEach(input => {
                            if (input !== this) {
                                input.removeAttribute('disabled');
                            }
                        });
                    } else {
                        branchCard.style.opacity = '0.6';
                        branchCard.querySelectorAll('input, select').forEach(input => {
                            if (input !== this) {
                                input.setAttribute('disabled', 'disabled');
                            }
                        });
                    }
                });
            });
            
            // Helper function to generate Saudi cities options
            function getSaudiCitiesOptions() {
                const cities = [
                    'Riyadh', 'Jeddah', 'Mecca', 'Medina', 'Dammam', 'Taif', 'Tabuk', 'Buraidah', 
                    'Khamis Mushait', 'Abha', 'Al-Khobar', 'Al-Ahsa', 'Najran', 'Yanbu', 'Al-Qatif', 
                    'Al-Jubail', "Ha'il", 'Al-Hofuf', 'Al-Mubarraz', 'Kharj', 'Qurayyat', 'Hafr Al-Batin', 
                    'Al-Kharj', 'Arar', 'Sakaka', 'Jizan', 'Al-Qunfudhah', 'Bisha', 'Al-Bahah', 'Unaizah', 
                    'Rafha', 'Dawadmi', 'Ar Rass', "Al Majma'ah", 'Tarut', 'Baljurashi', 'Shaqra', 
                    'Al-Zilfi', 'Ar Rayn', 'Wadi ad-Dawasir', 'Badr', 'Al Ula', 'Tharmada', 'Turabah', 'Tayma'
                ];
                
                return cities.map(city => `<option value="${city}">${city}</option>`).join('');
            }
        }
    });
</script>
@endpush