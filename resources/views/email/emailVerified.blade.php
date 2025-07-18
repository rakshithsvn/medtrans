{{-- <h3>Please verify your email</h3> --}}
@if(@$user->register_by == 'PARENT')
Dear Parent,<br/><br/>
@else
Dear Applicant,<br/><br/>
@endif
Your Account has been Verified.<br/>
To continue, please login by clicking the below link.<br/><br/>
<a href="">Click here to Login</a><br/><br/>
Thanks,<br/>
Department of IT
