@extends('layouts.back.index')

@section('content')

<section class="section my-2">
    <div class="card shadow-sm p-4">
        <h6 class="border-bottom mb-3 profile_pge">{{ __('CHANGE PASSWORD') }}</h6>

        <form action="{{route('admin/post-change-password')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mt-2">

                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-md" name="current_password" maxlength="255" required placeholder="Current Password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" minlength="6" maxlength="255" class="form-control form-control-md" name="new_password" placeholder="New Password" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" class="form-control form-control-md" placeholder="Confirm Password" name="new_password_confirm" minlength="6" maxlength="255" required>
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-2 mb-4">
                        {{ __('Change Password') }}
                    </button>

                </div>
            </div>
        </form>
    </div>
</section>

@endsection
