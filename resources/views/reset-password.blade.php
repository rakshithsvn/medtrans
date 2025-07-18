@extends('layouts.app')

@section('content')

<div class="row h-100">
    <div class="col-12 text-center">
        <div id="login-form">
            <div class="wrapper-logo">
                <a href="#"><img src="assets/images/logo/medtrans.png" alt="Logo" class="img-fluid mb-3"></a>
                <h5>MedTrans</h5>
                <h6></h6>
            </div>
            <h4>{{ __('Reset Password') }}</h4>

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
                <div class="col-md-12 frgt-txtbx input-fields" id="email">
                    <form action="{{ route('post-reset-password') }}" method="POST">
                        @csrf
                        <input type="hidden" name="username" value="{{ Request::get('username') }}">
                        <input type="hidden" name="user_type" value="{{ Request::get('user_type') }}">
                        <input type="hidden" name="verification_code" value="{{ Request::get('verification_code') }}">

                        <div class="form-group position-relative has-icon-left pass_show mb-4">
                            <input type="password" class="form-control form-control-md" name="password"  minlength="6" maxlength="255" required placeholder="New Password"> <span class="ptxt1">Show</span>
                            <div class="form-control-icon" style="top:25%">
                                <i class="bi bi-shield-lock"></i>
                            </div>      
                            @error('password')
                            <span class="invalid-tooltip" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror                      
                        </div>

                        <div class="form-group position-relative has-icon-left pass_show mb-4">
                            <input type="password" class="form-control form-control-md" name="password_confirm"  minlength="6" maxlength="255" required placeholder="Confirm Password"> <span class="ptxt1">Show</span>
                            <div class="form-control-icon" style="top:25%">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                            <span class="invalid-tooltip" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div style="color: red;" class="mb-2"> Hint * : Password minimum 6 characters.</div>
                        <div class="form-group">
                            <button class="btn btn-forgot-reset btn-primary btn-rounded submit-btn mt-2" type="submit">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>            
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
    $(document).on('click','.pass_show .ptxt1', function(){
        $(this).text($(this).text() == "Show" ? "Hide" : "Show");
        $(this).prev().attr('type', function(index, attr){return attr == 'password' ? 'text' : 'password'; });
    });

    $(document).click(function() {
       $(this).find('span.invalid-tooltip').hide();
   });
    $(".invalid-tooltip").click(function(e) {
        e.stopPropagation(); 
        return false;
    });

</script>
@endsection