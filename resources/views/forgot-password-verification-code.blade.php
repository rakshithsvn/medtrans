@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if (Session::has('error'))
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>
                        {{ Session::get('error') }}
                    </strong>
                </div>
                @endif
                <div class="card-header">{{ __('Forgot Password') }}</div>
                <div class="card-body">
                    <div class="col-md-12 mt-5 frgt-txtbx input-fields" id="email">
                        <form method="POST" action="{{ route('forgot-password-verification-code') }}">
                            @csrf
                            <input type="hidden" name="username" value="{{ $username }}">
                            <input type="hidden" name="user_type" value="{{ $user_type }}">
                            <div class="form-group">
                                <input type="number" name="verification_code" placeholder="Enter Verification Code" class="form-control" required>
                            </div>
                            <button class="btn btn-primary btn-rounded btn-forgot1 submit-btn" id="hideSubmit" style="margin: 0px;">Submit</button>
                        </form>
                        <div id="timer-container" class="send-timer">Wait <span id="timer">60</span> seconds to resend verification code</div>
                        <form method="POST" action="{{ route('forgot-password-resend-verification-code') }}">
                            @csrf
                            <input type="hidden" name="username" value="{{ $username }}">
                            <input type="hidden" name="user_type" value="{{ $user_type }}">
                            <button class="btn btn-primary btn-rounded" id="hideResend" style="position: absolute;
                            top: 53px;right: 10px;">Resend Verification Code</button>
                        </form>
                    </div>            
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('script')
<script>
    $("#resend-otp").hide();
    var timeoutHandle;
    function countdown(minutes) {
        var seconds = 60;
        var mins = minutes

        function tick() {
            var counter = document.getElementById("timer");
            var current_minutes = mins-1
            seconds--;
            counter.innerHTML =
            current_minutes.toString() + ":" + (seconds < 10 ? "0" : "") + String(seconds);
            if( seconds > 0 ) {
                timeoutHandle=setTimeout(tick, 1000);
            } else {

                if(mins > 1){
                    setTimeout(function () { countdown(mins - 1); }, 1000);
                }
            }
            if(seconds==0)
            {
                $("#resend-otp").show();
                $("#timer-container").hide();
                $("#hideResend").show();
            }
            $("#resend-otp").click(function (){
                $("#resend-otp").hide();

            })
        }
        tick();
    }
    countdown(1);

    $(document).ready(function() {
        $("#hideResend").hide();
        $("#hideSubmit").show();
    })
</script>
@endsection
