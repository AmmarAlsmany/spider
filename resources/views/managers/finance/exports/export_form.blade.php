@extends('shared.dashboard')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3">{{ __('messages.finance') }}</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="p-0 mb-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('messages.export_payments') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div><i class="bx bx-export me-1 font-22 text-primary"></i></div>
                    <h5 class="mb-0 text-primary">{{ __('messages.export_payments') }}</h5>
                </div>
                <hr>
                
                @if(session('success'))
                <div class="border-0 alert alert-success bg-success alert-dismissible fade show">
                    <div class="text-white">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="border-0 alert alert-danger bg-danger alert-dismissible fade show">
                    <div class="text-white">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <form action="{{ route('finance.exports.payments.download') }}" method="GET">
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">{{ __('messages.start_date') }}</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">{{ __('messages.end_date') }}</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-t')) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="status" class="form-label">{{ __('messages.payment_status') }}</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">{{ __('messages.all_statuses') }}</option>
                                <option value="paid">{{ __('messages.paid') }}</option>
                                <option value="pending">{{ __('messages.pending') }}</option>
                                <option value="overdue">{{ __('messages.overdue') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="contract_id" class="form-label">{{ __('messages.contract') }}</label>
                            <select class="form-select" id="contract_id" name="contract_id">
                                <option value="">{{ __('messages.all_contracts') }}</option>
                                @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}">{{ $contract->contract_number }} - {{ $contract->customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="format" class="form-label">{{ __('messages.export_format') }}</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf" checked>
                                    <label class="form-check-label" for="format_pdf">
                                        {{ __('messages.pdf') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="format_csv" value="csv">
                                    <label class="form-check-label" for="format_csv">
                                        {{ __('messages.csv') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="px-5 btn btn-primary">
                                <i class="bx bx-export me-1"></i> {{ __('messages.export') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mt-4 card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div><i class="bx bx-info-circle me-1 font-22 text-primary"></i></div>
                    <h5 class="mb-0 text-primary">{{ __('messages.export_instructions') }}</h5>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>{{ __('messages.pdf_export') }}</h6>
                        <ul>
                            <li>{{ __('messages.pdf_export_desc_1') }}</li>
                            <li>{{ __('messages.pdf_export_desc_2') }}</li>
                            <li>{{ __('messages.pdf_export_desc_3') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>{{ __('messages.csv_export') }}</h6>
                        <ul>
                            <li>{{ __('messages.csv_export_desc_1') }}</li>
                            <li>{{ __('messages.csv_export_desc_2') }}</li>
                            <li>{{ __('messages.csv_export_desc_3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2 for better dropdown experience
        $('#contract_id').select2({
            placeholder: "{{ __('messages.select_contract') }}",
            allowClear: true
        });
    });
</script>
@endpush
