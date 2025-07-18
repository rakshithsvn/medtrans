@extends('layouts.app')

@section('content')

<div class="row h-100">
    <div class="col-12 text-center">
        <div id="login-form">
            <div class="wrapper-logo">
                <a href="#"><img src="assets/images/logo/medtrans.png" alt="Logo" class="img-fluid mb-3"></a>
                <h5>MedTrans</h5>
                <h6></h6>
                <h4>HMIS</h4>
            </div>
            <h4>{{ __('Forgot Password') }}</h4>

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

            <div class="card-body">
                <div class="col-md-12 mt-3">
                    {{-- <form method='POST' action="{{ route('post-forgot-password') }}"> --}}
                        <form method="POST" id="registration-form" :action="registerBy == 'EMPLOYEE' ? '{{ route('admin/post-forgot-password') }}' : '{{ route('post-forgot-password') }}'">
                        @csrf
                            <div class="row">
                                {{-- <h5>Recovery username type</h5> --}}
                                <div class="form-group position-relative has-icon-left mt-4">
                                    <input type="radio" id="customRadio20" name="user_type" v-model="registerBy" class="logintype" value="EMPLOYEE">
                                    <span class="labelname">Employee</span>
                                    <!-- <input type="radio" id="customRadio20" name="user_type" v-model="registerBy" class="logintype " value="STUDENT" checked style="margin-left: 4%">
                                    <span class="labelname">Student</span>
                                    <input type="radio" id="customRadio21" name="user_type" v-model="registerBy" class="logintype " value="PARENT" style="margin-left: 4%">
                                    <span class="labelname">Parent</span> -->
                                </div>

                                {{-- <div class="form-group position-relative has-icon-left mb-4" v-if="registerBy === 'PARENT'">
                                    <input type="radio" id="customRadio22" name="login_type" onclick="email()" class="logintype" v-model="registerType" value="EMAIL" checked>
                                    <span class="labelname">Email</span>
                                    <input type="radio" id="customRadio23" name="login_type" onclick="otp()" class="logintype prnt" v-model="registerType" value="MOBILE">
                                    <span class="labelname">Mobile</span>
                                    <input type="hidden" id="customRadio22" name="login_type" class="form-control form-control-md @error('username') is-invalid @enderror" value="EMAIL" required>
                                </div> --}}

                                <div class="form-group position-relative has-icon-left mb-4">
                                   <input type="hidden" id="customRadio22" name="login_type" class="form-control form-control-md @error('username') is-invalid @enderror" value="EMAIL" required>
                               </div>

                           </div>

                       <div class="form-group position-relative has-icon-left mb-4" id="email" v-if="registerBy === 'EMPLOYEE'">
                         <input id="username" type="number" class="form-control form-control-md @error('username') is-invalid @enderror" name="username" value="{{ old('name') }}" required autocomplete="username" placeholder='Employee ID' autofocus>
                         <div class="form-control-icon" style="top:15%">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <button class="btn btn-primary btn-rounded btn-forgot submit-btn my-4" type="submit">Send Email</button>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4" id="email" v-else>
                        <input id="username" type="number" class="form-control form-control-md @error('username') is-invalid @enderror" name="username" value="{{ old('name') }}" required autocomplete="username" placeholder='Campus ID' autofocus>
                        <div class="form-control-icon" style="top:15%">
                           <i class="bi bi-file-person"></i>
                       </div>
                       @error('username')
                       <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                       </span>
                       @enderror
                       <button class="btn btn-primary btn-rounded btn-forgot submit-btn my-4" type="submit">Send Email</button>
                   </div>

                   {{--  <div class="form-group position-relative has-icon-left mb-4" id="email">
                        <input id="eml" type="email" class="form-control form-control-md @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="{{ __('E-Mail Address') }}" autocomplete="email">
                        <div class="form-control-icon" style="top:15%">
                            <i class="bi bi-justify"></i>
                        </div>
                        <button class="btn btn-primary btn-rounded btn-forgot submit-btn my-4">Send Email</button>
                    </div> --}}

                    <div class="form-group position-relative has-icon-left mb-4" id="otp">
                        <input id="mbl" type="text" class="form-control form-control-md @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile" minlength="10" maxlength="12" placeholder="{{ __('Mobile Number') }}" @keydown="numberOnlyCheck"/>
                        <div class="form-control-icon" style="top:15%">
                            <i class="bi bi-phone"></i>
                        </div>
                        <button class="btn btn-primary btn-rounded btn-forgot  submit-btn my-4" id="sotp">Send Verification Code</button>
                    </div>
                    @if (Route::has('login'))
                    <a class="btn btn-primary btn-block btn-lg shadow-lg mt-2 mb-2" href="{{ route('login') }}">{{ __('Login') }}</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
@section('script')
<!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> -->

<script>
    new Vue({
        el: "#app",
        data: {
            registerType: 'EMAIL',
            registerBy: 'STUDENT'
        },
        methods: {
            numberOnlyCheck(e) {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault()
            }
        }
    }
})
</script>

<script>
    $("#email").show()
    $("#otp").hide()

    $("#mbl").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            return
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault()
    }
})

    var email1 = document.getElementById("customRadio22")
    var otp1 = document.getElementById("customRadio23")

    email()

    function email() {
        if(email1.checked) {
            $("#email").show()
            $("#email input").prop('required', true)
            // $("#email input").prop('name', 'email')
            $("#otp").hide()
            $("#otp input").prop('required', false)
            $("#otp input").val('')
            $("#otp input").removeAttr('name')
        }
        else {
           $("#email").show()
           $("#email input").prop('required', true)
            // $("#email input").prop('name', 'email')
            $("#otp").hide()
            $("#otp input").prop('required', false)
            $("#otp input").val('')
            $("#otp input").removeAttr('name')
        }
    }

    function otp() {
        if(otp1.checked) {
            $("#otp").show()
            $("#otp input").prop('required', true)
            $("#otp input").prop('name', 'mobile')
            $("#email").hide()
            $("#email input").prop('required', false)
            $("#email input").val('')
            $("#email input").removeAttr('name')
        }
    }


</script>
@endsection
