<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedTrans</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">

    @yield('style')
</head>

<body>
    <div id="app">
                      
        <div id="sidebar">
            @include('front.partials.sidebar')
        </div>

        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none"> <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            @if (@$title)
                            <h3>{{ @$title }}</h3>
                            @endif
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="float-start float-md-end float-lg-end">
                                <a href="{{ url('dashboard') }}">
                                    <h5 class="card-title"><i class="bi bi-person-fill right-icon"></i>&nbsp;{!!
                                        @$data->FirstName !!}
                                        {!! @$data->LastName !!}</h5>
                                </a>
                            </div>
                        </div>
                        <div class="col-12">

                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                            @endif

                            @if (Session::has('success'))
                            <div class="alert alert-dismissable alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {!! Session::get('success') !!}
                            </div>
                            @endif
                            @if (Session::has('error'))
                            <div class="alert alert-dismissable alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {!! Session::get('error') !!}
                            </div>
                            @endif
                        </div>
                        {{-- <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </nav>

                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                            @endif

                        </div> --}}
                    </div>
                </div>

                <!-- Preloader -->
                <div class="preloader">
                    <div class="loader">
                        <h5>Processing... Please Wait</h5>
                        <div class="spinner">
                            <div class="double-bounce1"></div>
                            <div class="double-bounce2"></div>
                        </div>
                    </div>
                </div>
                <!-- End Preloader -->

                @yield('content')

            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="text-center">
                        <p>{{ now()->year }} &copy; AJHRC</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.preloader').hide();
    });
</script>

@yield('script')

</html>
