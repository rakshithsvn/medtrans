@extends('layouts.front.index')

@section('content')

<section class="section">
    <div class="card shadow-sm p-4">
        <h6 class="border-bottom mb-3 profile_pge">Settings</h6>
        <div class="text-right">
            <a href="{{ url('settings/change-password') }}" class="btn btn-success mb-3"> Change Password</a>
        </div>
    </div>
</section>

@endsection
