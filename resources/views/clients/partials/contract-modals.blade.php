{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('clients.contract_modals.reject_contract') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('client.contract.reject', $contract->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">{{ __('clients.contract_modals.reason_for_rejection') }}</label>
                        <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('clients.contract_modals.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('clients.contract_modals.reject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Update Request Modal --}}
<div class="modal fade" id="updateRequestModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('clients.contract_modals.request_update') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('client.contract.update-request', $contract->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="request_details" class="form-label">{{ __('clients.contract_modals.update_request_details') }}</label>
                        <textarea class="form-control" id="request_details" name="request_details" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('clients.contract_modals.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('clients.contract_modals.submit_request') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- History Modal --}}
<div class="modal fade" id="historyModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('contracts.contract_history') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($contract->history && $contract->history->count() > 0)
                    <div class="timeline">
                        @foreach($contract->history->sortByDesc('created_at') as $history)
                            <div class="timeline-item">
                                <div class="timeline-item-content">
                                    <div class="mb-2 d-flex align-items-center">
                                        <span class="timeline-icon bg-{{ 
                                            str_contains(strtolower($history->action), 'approved') ? 'success' :
                                            (str_contains(strtolower($history->action), 'rejected') ? 'danger' :
                                            (str_contains(strtolower($history->action), 'updated') ? 'primary' :
                                            (str_contains(strtolower($history->action), 'created') ? 'info' : 'secondary')))
                                        }}">
                                            <i class="bx bx-{{ 
                                                str_contains(strtolower($history->action), 'approved') ? 'check' :
                                                (str_contains(strtolower($history->action), 'rejected') ? 'x' :
                                                (str_contains(strtolower($history->action), 'updated') ? 'edit' :
                                                (str_contains(strtolower($history->action), 'created') ? 'plus' : 'history')))
                                            }}"></i>
                                        </span>
                                        <div class="ms-3">
                                            <h6 class="mb-1 fw-bold">{{ ucfirst($history->action) }}</h6>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($history->created_at)->format('M d, Y h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                    @if($history->notes)
                                        <div class="ms-5 ps-2">
                                            <p class="mb-0 text-muted">{{ $history->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-5 text-center">
                        <div class="mb-3">
                            <i class="bx bx-history text-muted" style="font-size: 64px;"></i>
                        </div>
                        <h6 class="text-muted">{{ __('contracts.no_history_available') }}</h6>
                        <p class="mb-0 text-muted">{{ __('contracts.no_history_records') }}</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('contracts.close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-item {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px;
        top: 40px;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
        flex-shrink: 0;
        z-index: 1;
        position: relative;
    }

    .timeline-icon i {
        font-size: 16px;
    }

    .timeline-item-content {
        margin-left: 0;
        position: relative;
    }

    /* Status Colors */
    .bg-success { background-color: #28a745 !important; }
    .bg-danger { background-color: #dc3545 !important; }
    .bg-primary { background-color: #007bff !important; }
    .bg-info { background-color: #17a2b8 !important; }
    .bg-secondary { background-color: #6c757d !important; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Removed the script for reject form submission
});
</script>
@endpush
