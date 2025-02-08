<!doctype html>
<html lang="ar">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Spider-Web Dashboard">
    <!--favicon-->
    <link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('backend/assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('backend/assets/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/header-colors.css') }}" />
    <title>Spider-Web</title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        @if (Auth::check())
        @if (Auth::user()->role == 'admin')
        @include('admin.sidebar')
        @elseif (Auth::user()->role == 'sales')
        @include('managers.sales.sidebar')
        @elseif (Auth::user()->role == 'technical')
        @include('managers.technical.sidebar')
        @elseif (Auth::user()->role == 'team_leader')
        @include('managers.team-leader.sidebar')
        @elseif (Auth::user()->role == 'sales_manager')
        @include('managers.sales Manager.sidebar')
        @elseif (Auth::user()->role == 'finance')
        @include('managers.finance.sidebar')
        @else
        @include('clients.sidebar')
        @endif
        @else
        @include('clients.sidebar')
        @endif
        <!--end sidebar wrapper -->
        <!--start header -->
        @include('shared.header')
        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            @yield('content')
        </div>
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <a href="#" class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"><i
                class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        @include('shared.footer')
    </div>
    <!--end wrapper-->

    <!-- Core JS -->
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--plugins-->
    <script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('backend/assets/plugins/select2/js/select2-custom.js') }}"></script>

    <!-- Initialize jVectorMap after jQuery is loaded -->
    <script src="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#world-map').length) {
                $('#world-map').vectorMap({
                    map: 'world_mill_en',
                    backgroundColor: 'transparent',
                    zoomOnScroll: false,
                    regionStyle: {
                        initial: {
                            fill: '#7c8a96'
                        }
                    }
                });
            }
        });
    </script>

    <script src="{{ asset('backend/assets/plugins/chartjs/js/chart.js') }}"></script>
    <script src="{{ asset('backend/assets/js/index.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!--app JS-->
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize flatpickr
            $(".datepicker").flatpickr();

            // Toast notifications
            @if (Session::has('message'))
                var type = "{{ Session::get('alert-type', 'info') }}";
                var message = "{{ Session::get('message') }}";
                
                if(typeof toastr !== 'undefined') {
                    switch(type) {
                        case 'info':
                            toastr.info(message);
                            break;
                        case 'success':
                            toastr.success(message);
                            break;
                        case 'warning':
                            toastr.warning(message);
                            break;
                        case 'error':
                            toastr.error(message);
                            break;
                    }
                }
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>