<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'MedTrans' }}</title>

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    {{-- <script src="{{ asset('assets/js/sweetalert.min.js') }}" defer></script> --}}
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('css/app.css') }}?v=1.0" rel="stylesheet">
    <link href="{{ asset('assets/css/common.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css?v=1.4" defer>
    <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    @yield('style')
    <style>
        .notify-count{        
            position: absolute;
            top: -16px;
            font-size: 12px;
            background: #dc3545!important;
            width: 20px;
            height: 20px;
            border-radius: 100px;
            text-align: center;
            left: 7px;color: #fff;
        }
        .notify{position: relative; margin-right: 20px; margin-top: 5px;}
		
		@media only screen and (max-width:540px){
			.col-sm-6{width:50%}
		}
    </style>
</head>

<body>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-center">
                        <div class="logo text-center">
                            <a href="{{ url('dashboard') }}">
                                <img src="/assets/images/logo/medtrans.png" alt="Logo" class="img-fluid mb-0">
                            </a>
                        </div>
                        <div class="toggler"> <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    @if(Auth()->user())
                    <ul class="menu">
                        @php
                        $auth = Auth()->user();
                        //if($auth->register_by !== 'ADMIN') {
                        $user_role = DB::table('user_roles')->where('user_id', @$auth->id)->get();
                        foreach (@$user_role as $role) {
                        $module_role[] = DB::table('role_modules')->where('role_id', @$role->role_id)->get();
                        }
                        if(@$module_role) {
                        foreach($module_role as $module) {
                        foreach($module as $id) {
                        $module_id[] = $id->module_id;
                        }
                        }
                        }
                        if(is_array(@$module_id)){
                        $modules = DB::table('modules')->orderBy('hierarchy')->where('view','=',1)->whereIn('id', @$module_id)->get();
                        } else {
                        $modules = null;
                        }
                        //} else {
                        // $modules = DB::table('modules')->orderBy('hierarchy')->where('view','=',1)->get();
                        //}
                        @endphp
                        @if(in_array($auth['register_by'], ['ADMIN', 'SUPERVISOR']))
                        <li class="sidebar-item {{ request()->is('dashboard') || request()->is('dashboard/*') ? 'active' : ''}}">
                            <a href="{{ url('dashboard') }}" class='sidebar-link'> <i class="bi bi-house-door"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        {{-- @else
                        <li class="sidebar-item {{ request()->is('admin/profile') || request()->is('admin/profile/*') ? 'active' : ''}}">
                            <a href="{{ url('admin/profile') }}" class='sidebar-link'> <i class="bi bi-house-door"></i>
                                <span>Profile</span>
                            </a>
                        </li> --}}
                        @endif

                        @if(@$modules)
                        @foreach(@$modules as $module)
                        <li class="sidebar-item {{ request()->is(@$module->url) || request()->is(@$module->url.'/*') ? 'active' : ''}}">
                            <a href="{{ url(@$module->url) }}" class='sidebar-link'>
                                <i class="{{ @$module->icon }}"></i><span>{{ @$module->name }}</span>
                            </a>
                        </li>
                        @endforeach
                        @endif

                        @if(in_array($auth['register_by'], ['ADMIN']))
                        <li class="sidebar-item {{ request()->is('settings') ? 'active' : ''}}">
                            <a href="{{ route('settings') }}" class='sidebar-link'>
                                <i class="bi bi-gear"></i><span>Settings</span>
                            </a>
                        </li>
                        @endif
                        <!-- <li class="sidebar-item {{ request()->is('support') ? 'active' : ''}}">
                                <a href="{{ route('support') }}" class='sidebar-link'> <i class="	bi bi-headset"></i>
                                    <span>Support</span>
                                </a>
                            </li> -->
                        @if($auth->register_by == 'ADMIN')
                        <li class="sidebar-item {{ request()->is('users') ? 'active' : ''}}">
                            <a href="{{ route('users') }}" class='sidebar-link'>
                                <i class="bi bi-people"></i><span>Users</span>
                            </a>
                        </li>
                        @endif
                        <li class="sidebar-item {{ request()->is('admin-logout') ? 'active' : ''}}">
                            <a class="sidebar-link" href="{{route('admin-logout')}}">
                                <i class="bi bi-unlock"></i><span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('admin-logout') }}" method="POST" class="d-none">
                                @csrf
                                {{-- <input type="hidden" name="latitude" id="latitude"/>
                                <input type="hidden" name="longitude" id="longitude"/> --}}
                            </form>
                        </li>
                    </ul>
                    @endif
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i>
                </button>
            </div>
        </div>

        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none"> <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 col-sm-6">
                            @if(@$title) <h3>{{ @$title }}</h3> @endif
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="d-none d-xs-none d-sm-block float-start float-md-end float-lg-end">
                                @if(@$auth && @$auth->register_by == 'EMPLOYEE')
                                @php $user = DB::table('employees')->where('employee_id', @$auth->username)->first(); @endphp                               
                                    <h5 class="card-title"><i class="bi bi-person-fill right-icon"></i>&nbsp;
                                        {!! @$user->name !!}</h5>                               
                                @else                                
                                    <h5 class="card-title"><i class="bi bi-person-fill right-icon"></i>&nbsp;
                                        {!! @$auth->name !!}</h5>                                
                                @endif
                            </div>

                            <!-- Notification Bell -->
                            <div class="dropdown">
                                <button class="btn btn-light position-relative float-end me-4" data-bs-toggle="dropdown">
                                    <h5 class="card-title mb-0"><i class="bi bi-bell-fill" style="color:#164966"></i></h5>
                                    @php
                                        $unreadCount = auth()->user()->unreadNotifications->count();
                                    @endphp
                                    @if ($unreadCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </button>

                                <!-- Dropdown Menu -->
                                <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 500px; max-height: 500px; overflow-y: auto;">
                                    @forelse(auth()->user()->unreadNotifications as $notification)
                                        <li class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('notifications.read', @$notification->id) }}"><span>{!! $notification->data['message'] !!}</span></a>
                                                <!-- @if (!empty($notification->data['url']))
                                                    <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-primary">View</a>
                                                @endif -->
                                            </div>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </li>
                                        <hr class="my-1">
                                    @empty
                                        <li class="text-center text-muted">No new notifications</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <div class="col-12">

                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                            @endif

                            @if (Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                                    {!! Session::get('success') !!}
                                    <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <script>
                                    setTimeout(function () {
                                        const alert = document.getElementById('success-alert');
                                        if (alert) {
                                            alert.classList.remove('show');
                                            alert.classList.add('fade');
                                            setTimeout(() => alert.remove(), 500);
                                        }
                                    }, 5000);
                                </script>
                            @endif

                            @if (Session::has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="danger-alert">
                                    {!! Session::get('error') !!}
                                    <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <script>
                                    setTimeout(function () {
                                        const alert = document.getElementById('danger-alert');
                                        if (alert) {
                                            alert.classList.remove('show');
                                            alert.classList.add('fade');
                                            setTimeout(() => alert.remove(), 500);
                                        }
                                    }, 5000);
                                </script>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- <script>
        @if (session()->has('success'))
        Swal.fire('{!! session()->get('success') !!}', '', 'success')
        @endif
        @if (session()->has('error'))
        swal('{!! session()->get('error') !!}', '', 'error')
        @endif
    </script> --}}

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
    </div>

    <footer>
        <div class="footer clearfix mb-0 text-muted">
            <div class="text-right pr-5">
                <p>{{ now()->year }} &copy; MedTrans</p>
            </div>
        </div>
    </footer>
</body>

<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> --}}
{{-- <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> --}}
{{-- <script src="/assets/vendors/simple-datatables/simple-datatables.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function logout(event) {
        $('#logout-form').submit();
    }

    function logoutWithLocations(event) {
        event.preventDefault();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                $('#latitude').val(latitude);
                $('#longitude').val(longitude);

                $('#logout-form').submit();
            }, function(error) {
                alert('Error getting location: ' + error.message);
                $('#logout-form').submit();
            });
        } else {
            alert("Geolocation is not supported by this browser.");
            $('#logout-form').submit();
        }
    }
</script>

<script>
   $('form[method="POST"]').on('submit', function(e) {
    var $form = $(this);
    var $submitBtn = $form.find('button[type="submit"]:focus'); 
    if ($submitBtn.val() === 'export') {
        return;
    }
    setTimeout(function() {
        $submitBtn.prop('disabled', true).text('Processing...');
    }, 10);
});

</script>

@yield('script')

<script>
    $(document).ready(function() {
        $('.preloader').hide();
    });
</script>

</html>