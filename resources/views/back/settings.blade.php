@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Section Header -->
            <h6 class="border-bottom mb-4 p-2">Settings</h6>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2">
                @php $auth = Auth()->user(); @endphp
                @if($auth->register_by !== 'ADMIN')
                <a href="{{ url('admin/change-password') }}" class="btn btn-primary">
                   Change Password
                </a>
                @else
                <a href="{{ route('roles') }}" class="btn btn-primary">
                    View Roles
                </a>
                <a href="{{ route('modules') }}" class="btn btn-primary">
                    View Modules
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
