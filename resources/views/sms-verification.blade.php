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
                <div class="card-header">{{ __('Enter OTP') }}</div>
                    <div class="card-body">
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
    </div>
</div>
@endsection

