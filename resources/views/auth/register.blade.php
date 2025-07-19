@extends('layouts.app')
@section('style')
<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}
.d-f {
    display:flex;
}
.jc-c {
    justify-content: center;
}
</style>
@endsection

@section('content')
<div class="row h-100">
    <div class="col-12 text-center">
        <div id="login-form">
            <div class="wrapper-logo">
                <a href="#"><img src="assets/images/logo/medtrans.png" alt="Logo" class="img-fluid mb-3"></a>
                <h5>MedTrans</h5>
            </div>
            <h3>{{ __('REGISTER') }}</h3>

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
            
            <form method="POST" action="{{ route('post-register') }}" id="registration-form">
                @csrf
                <div class="form-group position-relative has-icon-left mt-4 mb-4">
                    <input type="radio" name="register_by" value="STUDENT" class="logintype" v-model="registerBy" required> <span class="labelname">Student</span>
                    <input type="radio" name="register_by" value="PARENT" class="logintype prnt" v-model="registerBy" required> <span class="labelname">Parent</span>
                </div>

                <div class="form-group position-relative has-icon-left mb-4" v-if="registerBy === 'PARENT'">
                   {{-- <input type="radio" id="customRadio18" name="register_type" class="logintype" value="EMAIL" required v-model="registerType">
                   <span class="labelname">Email</span>
                   <input type="radio" id="customRadio17" name="register_type" class="logintype prnt" value="MOBILE" required v-model="registerType">
                   <span class="labelname">Mobile</span> --}}
                   <input type="hidden" id="" name="register_type" class="form-control form-control-md @error('username') is-invalid @enderror" value="EMAIL" required>
               </div>

               <div class="form-group position-relative has-icon-left mb-4" v-else>
                 <input type="hidden" id="" name="register_type" class="form-control form-control-md @error('username') is-invalid @enderror" value="EMAIL" required>
             </div>

             <div class="form-group position-relative has-icon-left mb-4" v-if="registerBy === 'STUDENT'">
                <input id="username" type="text" class="form-control form-control-md @error('username') is-invalid @enderror" name="username" value="{{ old('name') }}" required autocomplete="off" placeholder='Campus ID' autofocus>
                <div class="form-control-icon">
                    <i class="bi bi-file-person"></i>
                </div>
                @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group position-relative has-icon-left mb-4" v-else>
                <input id="username" type="text" class="form-control form-control-md @error('username') is-invalid @enderror" name="username" value="{{ old('name') }}" required autocomplete="off" placeholder='Campus ID' autofocus>
                <div class="form-control-icon">
                    <i class="bi bi-file-person"></i>
                </div>
                @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            {{-- <div class="form-group position-relative has-icon-left mb-4" v-if="registerType === 'EMAIL'">
                <input id="email" type="email" class="form-control form-control-md @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="{{ __('E-Mail Address') }}" autocomplete="email">
                <div class="form-control-icon">
                    <i class="bi bi-justify"></i>
                </div>
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group position-relative has-icon-left mb-4" v-else>
                <input id="mobile" type="text" class="form-control form-control-md @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile" minlength="10" maxlength="12" placeholder="{{ __('Mobile Number') }}" @keydown="numberOnlyCheck"/>
                <div class="form-control-icon">
                    <i class="bi bi-phone"></i>
                </div>
                @error('mobile')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div> --}}

            <div class="form-group position-relative has-icon-left mb-4">

              <h5>Create Password</h5>
              <div class="form-group position-relative has-icon-left mb-4">
                  <input id="password" type="password" class="form-control form-control-md @error('password') is-invalid @enderror" name="password" required placeholder="{{ __('Password') }}" autocomplete="new-password">
                  <div class="form-control-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group position-relative has-icon-left mb-4">
             <input id="password-confirm" type="password" class="form-control form-control-md" name="password_confirmation" required placeholder="{{ __('Confirm Password') }}" autocomplete="new-password">
             <div class="form-control-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group position-relative has-icon-left mb-4 mb-0">               
            <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-2 mb-4">
                {{ __('Register') }}
            </button>

            @if (Route::has('login'))
            <a class="btn btn-primary btn-block btn-lg shadow-lg mt-2 mb-4" href="{{ route('login') }}">{{ __('Login') }}</a>
            @endif
        </div>
    </form>
</div>
</div>
</div>

<div class="modal fade" id="OTPSentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width:500px;">
        <div class="modal-content text-center">
            <div class="modal-body">
                <div class="col-md-12 mt-3">
                    <form method="POST" action="{{ route('verify-mobile-number') }}" id="verifyOTPForm">
                        @csrf
                        <input type="hidden" name="username" value="">
                        <div>
                            <div class="form-group">
                                <label for="otp" style="color: black">Enter Verification Code Received On Your Mobile</label>
                                <input type="number" class="form-control" placeholder="Enter Verification Code" name="otp" required>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-rounded submit-btn" id="hideSubmit">Submit</button>
                        <button class="btn btn-primary btn-rounded" type="button" id="register-otp-resend-button" disabled>Resend Verification Code</button>
                    </form>
                    <div id="register-otp-resend-timer-container">Wait <span id="register-otp-resend-timer">60</span> seconds to resend verification code</div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
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

    var registrationForm = $('#registration-form')

    registrationForm.on('submit', e => {
        if($('[name="register_type"]:checked').val() === 'MOBILE') {
            e.preventDefault()
            $.ajax({
                type: registrationForm.attr('method'),
                url: registrationForm.attr('action'),
                data: registrationForm.serialize(),
                success(response) {
                    if(response.status === 'success') {
                        console.log('hi')
                        if(response.message === 'OTP Sent') {
                            console.log('hii')

                            $('#OTPSentModal').modal({
                                // these parameters prevent it from being closed
                                backdrop: 'static',
                                keyboard: false
                            })
                            $('#verifyOTPForm [name="username"]').val(registrationForm[0].querySelector('[name=mobile]').value)
                            setTimeout(function() { $('#verifyOTPForm [name="otp"]')[0].focus() }, 1000)
                            registerOTPResendButtonClock()
                        }
                    }
                    if(response.status === 'error') {
                        createAlert('main', 'error', response.message)
                        registrationForm.trigger('reset')
                        $(window).scrollTop(0)
                    }
                }
            })
        }
    })

    function createAlert(containerSelector, type, message, smallFontSize=false) {
        if(type === 'success') {
            document.querySelector(containerSelector).insertAdjacentHTML('afterbegin', `
                <div class="alert alert-success text-center animated fadeIn" ${smallFontSize ? 'style="font-size: 1em"' : ''}>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                ${message}
                </div>
                `)
            $("#hideShow").hide();
            $("#hideUser").show();
            $("#myShowHidebtn1").hide();
        }
        if(type === 'error') {
            document.querySelector(containerSelector).insertAdjacentHTML('afterbegin', `
                <div class="alert alert-danger text-center animated fadeIn" ${smallFontSize ? 'style="font-size: 1em"' : ''}>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                ${message}
                </div>
                `)
        }
    }

    var verifyOTPForm = $('#verifyOTPForm')

    verifyOTPForm.submit(e => {
        e.preventDefault()
        $('#OTPSentModal .alert').remove()
        $.ajax({
            type: verifyOTPForm.attr('method'),
            url: verifyOTPForm.attr('action'),
            data: verifyOTPForm.serialize(),
            success(response) {
                if(response.status === 'success') {
                    $('#OTPSentModal').modal('hide')
                    createAlert('main', 'success', `<h2 style=" font-weight: bold; color: green; ">Registration Completed!</h2>` + response.message)
                    registrationForm.trigger('reset')
                    $(window).scrollTop(0)
                    document.querySelector('header input[type="text"]').focus()
                }
                if(response.status === 'error') {
                    createAlert('#OTPSentModal > .modal-dialog > .modal-content', 'error', response.message, true)
                }
            }
        })
    })

    $(document).ready(function() {
        $("#register-otp-resend-button").hide();
        $("#hideSubmit").show();
    });

    $("#myShowHidebtn").click(function () {
        $("#myShowHidebtn1").show();
        $("#hideUser").hide();
        $("#hideShow").stop(true).toggle("slow");
        $("#myShowHidebtn").text(function (i, t) {
            return t == 'Hide' ? 'Register Now' : 'Register Now';
        });
    });

    $("#myShowHidebtn1").click(function () {
        $("#myShowHidebtn1").hide();
        $("#hideUser").show();
        $("#hideShow").stop(true).toggle("slow");
        $("#myShowHidebtn").text(function (i, t) {
            return t == 'Hide' ? 'Register Now' : 'Register Now';
        });
    });

    function registerOTPResendButtonClock() {
        var registerOTPTimer = setInterval(myClock, 1000)
        var counter = 60

        function myClock() {
            --counter
            $('#register-otp-resend-timer').html(counter)
            if (counter == 0) {
                clearInterval(registerOTPTimer)
                $('#register-otp-resend-button').prop('disabled', false)
                $('#register-otp-resend-timer-container').hide()
                $("#register-otp-resend-button").show();

            }
        }
    }

    $('#register-otp-resend-button').click(() => {
        $("#hideSubmit").show();
        $("#register-otp-resend-button").hide();
        $('#register-otp-resend-button').prop('disabled', true)
        $('#register-otp-resend-timer').html(60)
        $('#register-otp-resend-timer-container').show()
        registerOTPResendButtonClock()
        $.ajax({
            type: 'POST',
            url: `{{ route('registration-resend-verification-code') }}`,
            data: {
                _token: '{{ csrf_token() }}',
                username: registrationForm[0].querySelector('input[name="mobile"]').value,
                user_type: registrationForm[0].querySelector('input[name="register_by"]').value,
            },
            success(response) {
                if(response.status === 'success') {
                    createAlert('#OTPSentModal > .modal-dialog > .modal-content', 'success', response.message, true)
                    setTimeout(function() { $('#verifyOTPForm [name="otp"]')[0].focus() }, 1000)
                }
            }
        })
    })

</script>
@endsection
