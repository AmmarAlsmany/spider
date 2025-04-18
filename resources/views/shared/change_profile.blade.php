@extends('shared.dashboard')
@section('content')
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
    <!--breadcrumb-->
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">User Profile</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center d-flex flex-column align-items-center">
                                <img src="{{ !empty($profile_data->avatar) ? url('upload/profile_images/' . $profile_data->avatar) : url('upload/no_image.jpg') }}"
                                    alt="Admin" class="p-1 rounded-circle bg-primary" width="110">
                                <div class="mt-3">
                                    <h4>{{ $profile_data->name }}</h4>
                                    <p class="mb-1 text-secondary">{{ $profile_data->username }}</p>
                                    <p class="text-muted font-size-sm">{{ $profile_data->address }}</p>
                                </div>
                            </div>
                            <hr class="my-4" />
                            <ul class="list-group list-group-flush">
                                <li class="flex-wrap list-group-item d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-globe me-2 icon-inline">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="2" y1="12" x2="22" y2="12"></line>
                                            <path
                                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                                            </path>
                                        </svg>Company Mail</h6>
                                    <span class="text-secondary">{{ $profile_data->email }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <form id="profileForm" action="{{ route('update.user.profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Full Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                            value="{{ $profile_data->name }}" required minlength="3" />
                                        <div class="invalid-feedback">
                                            Please enter a valid name (minimum 3 characters)
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                            value="{{ $profile_data->email }}" required />
                                        <div class="invalid-feedback">
                                            Please enter a valid email address
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Phone</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ $profile_data->phone }}" required pattern="[0-9]{10}" />
                                        <div class="invalid-feedback">
                                            Please enter a valid 10-digit phone number
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Address</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                            value="{{ $profile_data->address }}" required minlength="5" />
                                        <div class="invalid-feedback">
                                            Please enter a valid address (minimum 5 characters)
                                        </div>
                                    </div>
                                </div>
                                @if (!Auth::guard('client')->check())
                                <div class="mb-3 row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Profile Image</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" name="avatar" class="form-control" id="image" accept="image/*" />
                                        <div class="invalid-feedback">
                                            Please select a valid image file
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0"></h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <img id="showimage"
                                            src="{{ !empty($profile_data->avatar) ? url('upload/profile_images/' . $profile_data->avatar) : url('upload/no_image.jpg') }}"
                                            alt="Admin" class="p-1 rounded-circle bg-primary" width="80">
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <button type="submit" class="px-4 btn btn-primary" id="submitBtn">Save Changes</button>
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
    $(document).ready(function() {
        // Image preview
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showimage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });

        // Form validation
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input[required]');
        
        // Real-time validation
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                validateInput(this);
            });
            
            input.addEventListener('blur', function() {
                validateInput(this);
            });
        });

        function validateInput(input) {
            if (input.type === 'tel') {
                // Phone validation
                const phoneRegex = /^[0-9]{10}$/;
                if (!phoneRegex.test(input.value)) {
                    input.classList.add('is-invalid');
                    return false;
                }
            } else if (input.type === 'email') {
                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value)) {
                    input.classList.add('is-invalid');
                    return false;
                }
            } else if (input.name === 'name') {
                // Name validation
                if (input.value.length < 3) {
                    input.classList.add('is-invalid');
                    return false;
                }
            } else if (input.name === 'address') {
                // Address validation
                if (input.value.length < 5) {
                    input.classList.add('is-invalid');
                    return false;
                }
            }

            input.classList.remove('is-invalid');
            return true;
        }

        // Form submission
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>
@endpush