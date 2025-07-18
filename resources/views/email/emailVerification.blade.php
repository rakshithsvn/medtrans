{{-- <h3>Please verify your email</h3> --}}
@if(@$register_by == 'PARENT')
Dear Parent,<br/><br/>
@else
Dear Applicant,<br/><br/>
@endif
Thank you for registering at MedTrans.<br/>
To continue, please verify your email account by clicking the below link.<br/><br/>
<a href="{{ route('user.verify', $token) }}">Click here to Verify Email</a><br/><br/>
Thanks,<br/>
Department of IT