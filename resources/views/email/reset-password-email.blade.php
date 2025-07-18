<html>
<link href="{{ asset('online-admission/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('online-admission/css/style.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ asset('online-admission/js/jquery-3.3.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('online-admission/js/bootstrap.min.js') }}"></script>
@if (@$register_by == 'PARENT')
    Dear Parent,<br /><br />
@else
    Dear Applicant,<br /><br />
@endif
Please click the below link to reset your password.<br>
<a href="{{ $link }}">Click here to reset your password.</a><br><br>
Or <br />
Please search below link. <br />
{{ $link }}

<br /><br />

Thanks,<br />
Department of IT<br />

</html>
