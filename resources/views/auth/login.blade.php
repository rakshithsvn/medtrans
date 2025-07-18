@extends('layouts.app')

@section('content')
<div>
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

    <form id="logins-form" method="POST" action="{{ route('admin-login') }}">
        @csrf
        <input type="hidden" name="register_by" value="ADMIN" class="form-control form-control-md">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <div class="form-group position-relative has-icon-left mb-4">
            <input type="text" class="form-control form-control-md" name="username" value="{{ old('username') }}" placeholder="Username / Employee ID">
            <div class="form-control-icon">
                <i class="bi bi-person"></i>
            </div>
            @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group position-relative has-icon-left mb-3">
            <input type="password" class="form-control form-control-md" name="password" placeholder="Password">
            <div class="form-control-icon">
                <i class="bi bi-lock"></i>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-2 mb-4">
            {{ __('Login') }}
        </button>

        <!-- Google Login Button -->
        {{-- <a href="{{ route('auth/google') }}" class="btn btn-block border shadow-sm mt-1 mb-3"><img src="assets/images/google.png" class="img-fluid text-start">&nbsp;&nbsp;&nbsp;Sign in with Google</a> --}}

        {{-- <a class="btn btn-link" href="{{ route('forgot-password') }}">
        <b>{{ __('Forgot Your Password ?') }}</b>
        </a> --}}
    </form>

    <div class="mx-2">
        {{-- @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary btn-block btn-lg shadow-lg mt-1">Register</a>
        @endif --}}
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
                var isSubmitting = false;
                $('#login-form').submit(function(e) {
                    e.preventDefault();
                    if (!isSubmitting) {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                // Capture the latitude and longitude
                                var latitude = position.coords.latitude;
                                var longitude = position.coords.longitude;

                                // Set the values in hidden input fields
                                $('#latitude').val(latitude);
                                $('#longitude').val(longitude);

                                isSubmitting = true;
                                $('#login-form')[0].submit();
                            }, function(error) {
                                alert('Error getting location: ' + error.message);
                            });
                        } else {
                            alert("Geolocation is not supported by this browser.");
                        }
                    }
                });

</script>
@endsection
