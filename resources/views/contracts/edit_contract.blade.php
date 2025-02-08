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

    .btn-success {
        background: linear-gradient(45deg, #28a745, #34ce57);
        border: none;
    }

    .btn-danger {
        background: linear-gradient(45deg, #dc3545, #ff4d5b);
        border: none;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    h4 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #edf2f7;
    }
</style>
<div class="page-content">
    <div class="mb-4 page-breadcrumb d-flex align-items-center">
        <div class="breadcrumb-title pe-3 fw-bold">Sales Profile</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales.dashboard') }}" class="text-decoration-none"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Contract</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container mt-4">
        <h2>Edit Contract</h2>
        <form action="{{ route('contract.update', $contract->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <fieldset class="mb-4">
                <legend>Contract Information</legend>
                <div class="form-group">
                    <label for="contractnumber">Contract Number</label>
                    <input type="text" class="form-control" id="contractnumber" name="contract_number" 
                        value="{{ $contract->contract_number }}" required readonly>
                    <div class="invalid-feedback">Please provide a valid contract number.</div>
                </div>
                <div class="form-group">
                    <label for="Property_type">Property Type <span data-toggle="tooltip"
                            title="Select the type of property">(?)</span></label>
                    <select type="text" class="form-select" name="Property_type" id="Property_type">
                        <option disabled>Select One</option>
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                    </select>
                    <div class="invalid-feedback">Please select a property type.</div>
                </div>
                {{-- warranty --}}
                <div class="form-group">
                    <label for="warrantyperiod">Warranty Period (Months) <span data-toggle="tooltip"
                            title="Enter the warranty period in months">(?)</span></label>
                    <input type="number" class="form-control" id="warrantyperiod" name="warrantyperiod" min="0"
                        value="{{ $contract->warranty }}" required>
                    <div class="invalid-feedback">Please enter a valid warranty period.</div>
                </div>
                {{-- number of visits --}}
                <div class="form-group">
                    <label for="number_of_visits">Number of Visits <span data-toggle="tooltip"
                            title="Enter the number of visits">(?)</span></label>
                    <input type="number" class="form-control" id="number_of_visits" name="number_of_visits" min="0"
                        value="{{ $contract->number_of_visits }}" required>
                    <div class="invalid-feedback">Please enter a valid number of visits.</div>
                </div>
                <div class="form-group">
                    <label for="contracttype">Contract Type <span data-toggle="tooltip"
                            title="Select the type of contract">(?)</span></label>
                    <select class="form-control" id="contracttype" name="contract_type" required>
                        @foreach ($contract_type as $type)
                        <option value="{{ $type->id }}" {{ $contract->contract_type == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} {{ $type->type }}
                        </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Please select a contract type.</div>
                </div>
                <div class="form-group">
                    <label for="contractDescription">Contract Description <span data-toggle="tooltip"
                            title="Enter a brief description of the contract">(?)</span></label>
                    <textarea class="form-control" id="contractDescription" name="contract_description" rows="3"
                        required>{{ $contract->contract_description }}</textarea>
                    <div class="invalid-feedback">Please provide a valid contract description.</div>
                </div>
                <div class="form-group">
                    <label for="contractstartdate">Start Date <span data-toggle="tooltip"
                            title="Select the start date of the contract">(?)</span></label>
                    <input type="date" class="form-control" id="contractstartdate" name="contract_start_date"
                        value="{{ $contract->contract_start_date }}" required>
                    <div class="invalid-feedback">Please select a start date.</div>
                </div>
                <div class="form-group">
                    <label for="totalAmount">Contract Amount <span data-toggle="tooltip"
                            title="Enter the total amount of the contract">(?)</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="totalAmount" name="contract_price"
                            value="{{ $contract->contract_price }}" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="includeTax" name="include_tax">
                                    <label class="form-check-label" for="includeTax">
                                        Include Tax (15%)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">
                        Amount before tax: <span id="amountBeforeTax">{{ $contract->contract_price }}</span> SAR<br>
                        Tax amount (15%): <span id="taxAmount">0</span> SAR<br>
                        Total amount: <span id="finalAmount">{{ $contract->contract_price }}</span> SAR
                    </small>
                </div>
                <div class="form-group">
                    <label for="paymenttype">Payment Type</label>
                    <select class="form-control" id="paymenttype" name="Payment_type" required>
                        <option value="prepaid" {{ $contract->payment_type == 'prepaid' ? 'selected' : '' }}>
                            Prepaid</option>
                        <option value="postpaid" {{ $contract->payment_type == 'postpaid' ? 'selected' : '' }}>
                            Postpaid</option>
                    </select>
                    <div class="invalid-feedback">Please select a payment type.</div>
                </div>

                <div class="form-group" id="paymentScheduleSection" style="{{ $contract->payment_type == 'postpaid' ? '' : 'display: none;' }}">
                    <label for="payment_schedule">Payment Schedule</label>
                    <select class="form-control" id="payment_schedule" name="payment_schedule">
                        <option value="monthly" {{ $contract->payment_schedule == 'monthly' ? 'selected' : '' }}>Monthly (Automatic)</option>
                        <option value="custom" {{ $contract->payment_schedule == 'custom' ? 'selected' : '' }}>Custom Dates</option>
                    </select>
                    <div class="invalid-feedback">Please select a payment schedule.</div>
                </div>
                <div class="form-group">
                    <label for="contractenddate">End Date <span data-toggle="tooltip"
                            title="Select the end date of the contract">(?)</span></label>
                    <input type="date" class="form-control" id="contractenddate" name="contract_end_date"
                        value="{{ $contract->contract_end_date }}" required>
                    <div class="invalid-feedback">Please select an end date.</div>
                </div>
            </fieldset>
            <fieldset class="mb-4">
                <legend>Client Information</legend>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clientName">Client Name</label>
                            <input type="text" class="form-control" id="clientName" name="clientName"
                                value="{{ $contract->customer->name }}" required>
                            <div class="invalid-feedback">Please provide a valid client name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clientEmail">Client Email</label>
                            <input type="email" class="form-control" id="clientEmail" name="clientEmail"
                                value="{{ $contract->customer->email }}" required>
                            <div class="invalid-feedback">Please provide a valid client email.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clientPhone">Phone</label>
                            <input type="text" class="form-control" id="clientPhone" name="clientPhone"
                                value="{{ $contract->customer->phone }}" required>
                            <div class="invalid-feedback">Please provide a valid phone number.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clientMobile">Mobile</label>
                            <input type="text" class="form-control" id="clientMobile" name="clientMobile"
                                value="{{ $contract->customer->mobile }}" required>
                            <div class="invalid-feedback">Please provide a valid mobile number.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_tax_number">Tax Number</label>
                            <input type="number" class="form-control" id="client_tax_number" name="client_tax_number"
                                value="{{ $contract->customer->tax_number }}" min="0">
                            <div class="invalid-feedback">Please provide a valid tax number.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_zipcode">Zip Code</label>
                            <input type="number" class="form-control" id="client_zipcode" name="client_zipcode"
                                value="{{ $contract->customer->zip_code }}" min="0">
                            <div class="invalid-feedback">Please provide a valid zip code.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_city">City</label>
                            <select class="form-select" id="client_city" name="client_city" required>
                                <option value="">Select City</option>
                                @foreach($saudiCities as $city)
                                <option value="{{ $city }}" {{ $contract->customer->city == $city ? 'selected' : '' }}>
                                    {{ $city }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a city.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clientAddress">Address</label>
                            <input type="text" class="form-control" id="clientAddress" name="clientAddress"
                                value="{{ $contract->customer->address }}" required>
                            <div class="invalid-feedback">Please provide a valid address.</div>
                        </div>
                    </div>
                </div>
            </fieldset>
            @if ($contract->is_multi_branch == 'yes')
            <fieldset class="mb-4">
                <legend>Branch Information</legend>
                @foreach ($contract->branchs as $index => $branch)
                <div class="mb-3 card">
                    <div class="card-body">
                        <h5>Branch {{ $index + 1 }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="branch_id[]" value="{{ $branch->id }}" hidden readonly>
                                <div class="form-group">
                                    <label for="branchName{{ $index }}">Branch Name <span data-toggle="tooltip"
                                            title="Enter the name of the branch">(?)</span></label>
                                    <input type="text" class="form-control" id="branchName{{ $index }}"
                                        name="branch_name[]" value="{{ $branch->branch_name }}" required>
                                    <div class="invalid-feedback">Please provide a valid branch name.</div>
                                </div>
                                <div class="form-group">
                                    <label for="branchmanager{{ $index }}">Branch Manager <span data-toggle="tooltip"
                                            title="Enter the name of the branch manager">(?)</span></label>
                                    <input type="text" class="form-control" id="branchmanager{{ $index }}"
                                        name="branch_manager_name[]" value="{{ $branch->branch_manager_name }}"
                                        required>
                                    <div class="invalid-feedback">Please provide a valid branch manager.</div>
                                </div>
                                <div class="form-group">
                                    <label for="branchphone{{ $index }}">Branch Phone <span data-toggle="tooltip"
                                            title="Enter the phone number of the branch">(?)</span></label>
                                    <input type="text" class="form-control" id="branchphone{{ $index }}"
                                        name="branch_manager_phone[]" value="{{ $branch->branch_manager_phone }}"
                                        required>
                                    <div class="invalid-feedback">Please provide a valid branch phone.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branchadress{{ $index }}">Branch Address <span data-toggle="tooltip"
                                            title="Enter the address of the branch">(?)</span></label>
                                    <input type="text" class="form-control" id="branchadress{{ $index }}"
                                        name="branch_address[]" value="{{ $branch->branch_address }}" required>
                                    <div class="invalid-feedback">Please provide a valid branch address.</div>
                                </div>
                                <div class="form-group">
                                    <label for="branchcity{{ $index }}">Branch City <span data-toggle="tooltip"
                                            title="Enter the city of the branch">(?)</span></label>
                                    <input type="text" class="form-control" id="branchcity{{ $index }}"
                                        name="branch_city[]" value="{{ $branch->branch_city }}" required>
                                    <div class="invalid-feedback">Please provide a valid branch city.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <button type="button" class="mt-2 btn btn-success" id="add-branch">Add New Branch</button>
            </fieldset>
            @endif
            <fieldset class="mb-4">
                <legend>Payment Information</legend>
                <div class="card">
                    <div class="card-body" id="payments-container">
                        @foreach ($contract->payments as $index => $payment)
                        <div class="mb-3 row payment-row" data-payment-id="{{ $index + 1 }}">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_amount{{ $index }}">Payment Amount <span data-toggle="tooltip"
                                            title="Enter the amount of the payment">(?)</span></label>
                                    <input type="number" class="form-control" id="payment_amount{{ $index }}"
                                        name="payment_amount[]" value="{{ $payment->payment_amount }}" required>
                                    <div class="invalid-feedback">Please provide a valid payment amount.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_date{{ $index }}">Payment Date <span data-toggle="tooltip"
                                            title="Select the date of the payment">(?)</span></label>
                                    <input type="date" class="form-control" id="payment_date{{ $index }}"
                                        name="payment_date[]" value="{{ $payment->due_date }}" required>
                                    <div class="invalid-feedback">Please select a payment date.</div>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                @if ($index > 0)
                                <button type="button" class="mb-3 btn btn-danger remove-payment">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="mt-2 btn btn-success" id="add-payment">Add New Payment</button>
            </fieldset>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Contract</button>
                <a href="{{ route('contract.show.details', $contract->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const paymentsContainer = document.getElementById('payments-container');
            const addPaymentBtn = document.getElementById('add-payment');
            const paymentTypeSelect = document.getElementById('paymenttype');

            // Function to update the visibility of the add payment button
            function updateAddPaymentButtonVisibility() {
                const isPostpaid = paymentTypeSelect.value === 'postpaid';
                addPaymentBtn.style.display = isPostpaid ? 'block' : 'none';
            }

            // Initial visibility check
            updateAddPaymentButtonVisibility();

            // Listen for payment type changes
            paymentTypeSelect.addEventListener('change', updateAddPaymentButtonVisibility);

            // Add new payment row
            addPaymentBtn.addEventListener('click', function() {
                if (paymentTypeSelect.value !== 'postpaid') {
                    alert('New payments can only be added for postpaid contracts');
                    return;
                }

                const paymentRows = document.querySelectorAll('.payment-row');
                const newIndex = paymentRows.length;

                const newPaymentRow = `
            <div class="mb-3 row payment-row" data-payment-id="${newIndex + 1}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="payment_amount${newIndex}">Payment Amount <span data-toggle="tooltip" title="Enter the amount of the payment">(?)</span></label>
                        <input type="number" min="0" class="form-control" id="payment_amount${newIndex}" name="payment_amount[]" required>
                        <div class="invalid-feedback">Please provide a valid payment amount.</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_date${newIndex}">Payment Date <span data-toggle="tooltip" title="Select the date of the payment">(?)</span></label>
                        <input type="date" class="form-control" id="payment_date${newIndex}" name="payment_date[]" required>
                        <div class="invalid-feedback">Please select a payment date.</div>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="mb-3 btn btn-danger remove-payment">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
                paymentsContainer.insertAdjacentHTML('beforeend', newPaymentRow);
            });

            // Remove payment row
            paymentsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-payment')) {
                    const paymentRows = document.querySelectorAll('.payment-row');
                    if (paymentRows.length > 1) {
                        e.target.closest('.payment-row').remove();
                    } else {
                        alert('At least one payment record must exist');
                    }
                }
            });
        });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
            const addBranchBtn = document.getElementById('add-branch');

            // Add new branch
            addBranchBtn.addEventListener('click', function() {
                const branchSections = document.querySelectorAll('.card h5').length;
                const newIndex = branchSections;

                const newBranchHtml = `
        <div class="mb-3 card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Branch ${newIndex + 1}</h5>
                    <button type="button" class="btn btn-danger remove-branch">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="branch_id[]" value="" hidden readonly>
                        <div class="form-group">
                            <label for="branchName${newIndex}">Branch Name <span data-toggle="tooltip" title="Enter the name of the branch">(?)</span></label>
                            <input type="text" class="form-control" id="branchName${newIndex}" name="branch_name[]" required>
                            <div class="invalid-feedback">Please provide a valid branch name.</div>
                        </div>
                        <div class="form-group">
                            <label for="branchmanager${newIndex}">Branch Manager <span data-toggle="tooltip" title="Enter the name of the branch manager">(?)</span></label>
                            <input type="text" class="form-control" id="branchmanager${newIndex}" name="branch_manager_name[]" required>
                            <div class="invalid-feedback">Please provide a valid branch manager.</div>
                        </div>
                        <div class="form-group">
                            <label for="branchphone${newIndex}">Branch Phone <span data-toggle="tooltip" title="Enter the phone number of the branch">(?)</span></label>
                            <input type="text" class="form-control" id="branchphone${newIndex}" name="branch_manager_phone[]" required>
                            <div class="invalid-feedback">Please provide a valid branch phone.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="branchadress${newIndex}">Branch Address <span data-toggle="tooltip" title="Enter the address of the branch">(?)</span></label>
                            <input type="text" class="form-control" id="branchadress${newIndex}" name="branch_address[]" required>
                            <div class="invalid-feedback">Please provide a valid branch address.</div>
                        </div>
                        <div class="form-group">
                            <label for="branchcity${newIndex}">Branch City <span data-toggle="tooltip" title="Enter the city of the branch">(?)</span></label>
                            <input type="text" class="form-control" id="branchcity${newIndex}" name="branch_city[]" required>
                            <div class="invalid-feedback">Please provide a valid branch city.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

                // Insert the new branch before the "Add New Branch" button
                addBranchBtn.insertAdjacentHTML('beforebegin', newBranchHtml);
            });

            // Remove branch
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-branch')) {
                    const branchCards = document.querySelectorAll('.card h5');
                    if (branchCards.length > 1) {
                        const branchCard = e.target.closest('.card');
                        branchCard.remove();

                        // Update branch numbers
                        document.querySelectorAll('.card h5').forEach((header, index) => {
                            if (header.textContent.includes('Branch')) {
                                header.textContent = `Branch ${index + 1}`;
                            }
                        });
                    } else {
                        alert('At least one branch must exist');
                    }
                }
            });
        });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalAmountInput = document.getElementById('totalAmount');
        const includeTaxCheckbox = document.getElementById('includeTax');
        const amountBeforeTaxSpan = document.getElementById('amountBeforeTax');
        const taxAmountSpan = document.getElementById('taxAmount');
        const finalAmountSpan = document.getElementById('finalAmount');
        const paymentAmountSpan = document.getElementById('paymentAmount');

        function calculateAmounts() {
            let amount = parseFloat(totalAmountInput.value) || 0;
            
            if (includeTaxCheckbox.checked) {
                // If amount includes tax, calculate amount before tax
                let amountBeforeTax = amount / 1.15;
                let taxAmount = amount - amountBeforeTax;
                
                amountBeforeTaxSpan.textContent = amountBeforeTax.toFixed(2);
                taxAmountSpan.textContent = taxAmount.toFixed(2);
                finalAmountSpan.textContent = amount.toFixed(2);
            } else {
                // If amount doesn't include tax, calculate tax amount
                let taxAmount = amount * 0.15;
                let finalAmount = amount + taxAmount;
                
                amountBeforeTaxSpan.textContent = amount.toFixed(2);
                taxAmountSpan.textContent = taxAmount.toFixed(2);
                finalAmountSpan.textContent = finalAmount.toFixed(2);
            }

            // Calculate payment amount
            let finalAmount = parseFloat(finalAmountSpan.textContent);
            paymentAmountSpan.textContent = paymentAmount.toFixed(2);
        }

        // Add event listeners
        totalAmountInput.addEventListener('input', calculateAmounts);
        includeTaxCheckbox.addEventListener('change', calculateAmounts);

        // Initial calculation
        calculateAmounts();
    });
</script>

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle payment type change
        $('#paymenttype').change(function() {
            if ($(this).val() === 'postpaid') {
                $('#paymentScheduleSection').show();
            } else {
                $('#paymentScheduleSection').hide();
            }
        });

        // Handle payment schedule change
        $('#payment_schedule').change(function() {
            if ($(this).val() === 'custom') {
                $('.custom-payment-dates').show();
            } else {
                $('.custom-payment-dates').hide();
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    });
</script>
@endsection