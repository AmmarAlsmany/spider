<!-- Search Form -->
<div class="mb-4">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="client_name" class="form-control" placeholder="Search by Client Name" value="{{ request('client_name') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="client_email" class="form-control" placeholder="Search by Client Email" value="{{ request('client_email') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="client_phone" class="form-control" placeholder="Search by Client Phone" value="{{ request('client_phone') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="contract_number" class="form-control" placeholder="Search by Contract Number" value="{{ request('contract_number') }}">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-search"></i> Search
            </button>
            <a href="{{ request()->url() }}" class="btn btn-secondary">
                <i class="bx bx-reset"></i> Reset
            </a>
        </div>
    </form>
</div>
