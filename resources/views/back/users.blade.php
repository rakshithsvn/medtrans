@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Section Title -->
            <h6 class="border-bottom mb-4 profile_pge" style="color: white;">User Management</h6>

            <!-- Filter Form -->
            <div class="text-right">
                <form id="update-form" action="{{ route('users') }}" method="POST" class="">
                    @csrf
                    <div class="row g-3 align-items-center">
                        <!-- Filter Fields (Grouped on the Left) -->
                        <div class="col-md-8 col-lg-9 d-flex gap-3 align-items-center">
                            <!-- User Type -->
                            <div class="flex-grow-1">
                                <select class="form-control border-primary" name="register_by">
                                    <option value="" selected>Select</option>
                                    <option value="EMPLOYEE" @if(@$request->register_by == 'EMPLOYEE') selected @endif>Employee</option>
                                </select>
                            </div>

                            <!-- Login Type -->
                            <div class="flex-grow-1">
                                <select class="form-control border-primary" name="verified">
                                    <option value="" selected>Select</option>
                                    <option value="1" @if(@$request->verified == '1') selected @endif>Verified</option>
                                    <option value="0" @if(@$request->verified == '0') selected @endif>Unverified</option>
                                </select>
                            </div>

                            <!-- Employee ID -->
                            <div class="flex-grow-1">
                                <input type="number" name="username" class="form-control border-primary" value="{{ @$request->username }}" placeholder="Employee ID" />
                            </div>
                        </div>

                        <!-- Filter Button (On the Right) -->
                        <div class="col-md-4 col-lg-3 text-end">
                            <button type="submit" class="btn btn-primary w-100">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="card shadow-sm border-0 global-font">
    <div class="card-body p-4">
        <!-- User Table -->
        <div class="table-responsive">
            <table class="table table-borderless data-table">
                <thead>
                    <tr style="background: #164966; color: white;">
                        <th class="ps-3 pe-3">User Name</th>
                        <th class="ps-3 pe-3">Email ID</th>
                        <th class="ps-3 pe-3">User Type</th>
                        <th class="ps-3 pe-3">Register Type</th>
                        <th class="ps-3 pe-3">Verified</th>
                        <th class="ps-3 pe-3 text-center">Actions</th> <!-- Centered header -->
                    </tr>
                </thead>
                <tbody>
                    @if(@$users)
                    @foreach(@$users as $key=>$user)
                    <tr class="border-bottom align-middle">
                        <td class="ps-3 pe-3">{{ @$user->username }}</td>
                        <td class="ps-3 pe-3">{{ @$user->email }}</td>
                        <td class="ps-3 pe-3">{{ @$user->register_by }}</td>
                        <td class="ps-3 pe-3">{{ @$user->register_type }}</td>
                        <td class="ps-3 pe-3 text-center">
                            <div class="form-check form-check-lg d-flex align-items-center justify-content-center">
                                <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" @if(@$user->verified == 1) checked @endif disabled>
                            </div>
                        </td>
                        <td class="ps-3 pe-3 text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <!-- Align buttons horizontally -->
                                @if(@$user->verified == 1)
                                @if(@$user->register_by == 'ADMIN')
                                <span class="badge bg-success">Verified</span>
                                @endif
                                @else
                                <a class="btn btn-sm btn-primary" href="{{ route('user-verify', @$user->id) }}">
                                    <i class="fas fa-check me-2"></i>Verify
                                </a>
                                @endif
                                @if(@$user->register_by !== 'ADMIN')
                                <button class="btn btn-sm btn-primary d-flex align-items-center ms-auto me-2 justify-content-center" data-bs-toggle="modal" data-bs-target="#assign{{$user->id}}">
                                    Assign Role
                                </button>
                                @endif
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#reset{{$user->id}}">
                                    </i>Reset Password
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Assign Role Modal -->
                    <div class="modal fade" id="assign{{$user->id}}" tabindex="-1" aria-labelledby="assignLable" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header" style="background: #164966; color: white;">
                                    <h5 class="modal-title" id="reportLabel" style="color:white">Assign Role</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin/update-user') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ @$user->id }}">
                                        <input type="hidden" name="username" value="{{ @$user->username }}">
                                        <input type="hidden" name="user_type" value="{{ @$user->register_by }}">

                                        <div class="row">
                                            @foreach(@$roles as $role)
                                            <div class="col-sm-12 col-md-4 col-lg-4 mt-2">
                                                <h6 class="p-2">{{ @$role->name }}</h6>
                                                <div class="form-check form-check-lg d-flex align-items-center">
                                                    <input type="checkbox" name="role[]" class="form-check-input" value="{{ @$role->id }}" id="flexCheckDefault" @if(@$user_roles->where('user_id', @$user->id)->where('role_id', @$role->id )->count()>0) checked @endif>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Reset Password Modal -->
                    <div class="modal fade" id="reset{{$user->id}}" tabindex="-1" aria-labelledby="resetLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header" style="background: #164966; color: white;">
                                    <h5 class="modal-title" id="reportLabel" style="color:white">Reset Password</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <!-- Modal Body -->
                                <div class="modal-body">
                                    <form action="{{ route('admin-reset-password') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ @$user->id }}">
                                        <input type="hidden" name="username" value="{{ @$user->username }}">
                                        <input type="hidden" name="user_type" value="{{ @$user->register_by }}">

                                        <!-- New Password Field -->
                                        <div class="form-group position-relative has-icon-left pass_show mb-4">
                                            <input type="password" class="form-control border-primary" name="password" minlength="6" maxlength="255" required placeholder="New Password">
                                            <span class="ptxt1">Show</span>
                                            <div class="form-control-icon" style="top:25%">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            @error('password')
                                            <span class="invalid-tooltip" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password Field -->
                                        <div class="form-group position-relative has-icon-left pass_show mb-4">
                                            <input type="password" class="form-control border-primary" name="password_confirm" minlength="6" maxlength="255" required placeholder="Confirm Password">
                                            <span class="ptxt1">Show</span>
                                            <div class="form-control-icon" style="top:25%">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            @error('password')
                                            <span class="invalid-tooltip" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <!-- Hint and Submit Button -->
                                        <div class="text-danger small mb-3">* Password must be at least 6 characters.</div>
                                        <div class="form-group text-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Update Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="create{{$user->id}}" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header" style="background: #164966; color: white;">
                                    <h5 class="modal-title" id="reportLabel">Edit User</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <!-- Modal Body -->
                                <div class="modal-body">
                                    <form id="update-form" action="{{ route('admin/update-user') }}" method="POST" class="">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ @$user->id }}">

                                        <!-- User Name -->
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-4 col-lg-3">
                                                <label class="form-label text-secondary">User Name</label>
                                            </div>
                                            <div class="col-sm-12 col-md-8 col-lg-9">
                                                <div class="form-group d-flex align-items-center">
                                                    <input type="text" name="username" class="form-control border-primary" placeholder="User Name" value="{{ @$user->username }}" required>
                                                    <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Email ID -->
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-4 col-lg-3">
                                                <label class="form-label text-secondary">Email ID</label>
                                            </div>
                                            <div class="col-sm-12 col-md-8 col-lg-9">
                                                <div class="form-group d-flex align-items-center">
                                                    <input type="text" name="email" class="form-control border-primary" placeholder="Email ID" value="{{ @$user->email }}" required>
                                                    <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mobile Number -->
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-4 col-lg-3">
                                                <label class="form-label text-secondary">Mobile Number</label>
                                            </div>
                                            <div class="col-sm-12 col-md-8 col-lg-9">
                                                <div class="form-group d-flex align-items-center">
                                                    <input type="number" name="mobilenumber" class="form-control border-primary" placeholder="Mobile Number" value="{{ @$user->mobilenumber }}" required>
                                                    <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Register By -->
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-4 col-lg-3">
                                                <label class="form-label text-secondary">Register By</label>
                                            </div>
                                            <div class="col-sm-12 col-md-8 col-lg-9">
                                                <div class="form-group d-flex align-items-center">
                                                    <select class="form-control border-primary" name="register_by" required>
                                                        <option value="">-- Select User Type --</option>
                                                        <option value="STUDENT" @if(@$user->register_by == 'STUDENT') selected @endif>STUDENT</option>
                                                        <option value="PARENT" @if(@$user->register_by == 'PARENT') selected @endif>PARENT</option>
                                                    </select>
                                                    <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Register Type -->
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-4 col-lg-3">
                                                <label class="form-label text-secondary">Register Type</label>
                                            </div>
                                            <div class="col-sm-12 col-md-8 col-lg-9">
                                                <div class="form-group d-flex align-items-center">
                                                    <select class="form-control border-primary" name="register_type" required>
                                                        <option value="">-- Select Register Type --</option>
                                                        <option value="EMAIL" @if(@$user->register_type == 'EMAIL') selected @endif>EMAIL</option>
                                                        <option value="MOBILE" @if(@$user->register_type == 'MOBILE') selected @endif>MOBILE</option>
                                                    </select>
                                                    <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Verified -->
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-4 col-lg-3">
                                                <label class="form-label text-secondary">Verified</label>
                                            </div>
                                            <div class="col-sm-12 col-md-8 col-lg-9">
                                                <div class="form-check form-check-lg d-flex align-items-center">
                                                    <input type="checkbox" name="verified" class="form-check-input" value="1" id="flexCheckDefault" @if(@$user->verified == 1) checked @endif>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center">Data not available</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <div class="float-end"> {{ $users->links() }}</div>

            <br>
        </div>
    </div>
</div>

<div class="modal fade" id="create" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" style="background: #164966; color: white;">
                <h5 class="modal-title" id="reportLabel">Create User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form id="update-form" action="{{ route('admin/update-user') }}" method="POST" class="">
                    @csrf
                    <!-- User Name -->
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <label class="form-label text-secondary">User Name</label>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-9">
                            <div class="form-group d-flex align-items-center">
                                <input type="text" name="username" class="form-control border-primary" placeholder="User Name" required>
                                <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Email ID -->
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <label class="form-label text-secondary">Email ID</label>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-9">
                            <div class="form-group d-flex align-items-center">
                                <input type="email" name="email" class="form-control border-primary" placeholder="Email ID" required>
                                <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Number -->
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <label class="form-label text-secondary">Mobile Number</label>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-9">
                            <div class="form-group d-flex align-items-center">
                                <input type="number" name="mobilenumber" class="form-control border-primary" placeholder="Mobile Number" required>
                                <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Register By -->
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <label class="form-label text-secondary">Register By</label>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-9">
                            <div class="form-group d-flex align-items-center">
                                <select class="form-control border-primary" name="register_by" required>
                                    <option value="" selected>-- Select User Type --</option>
                                    <option value="STUDENT">STUDENT</option>
                                    <option value="PARENT">PARENT</option>
                                </select>
                                <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Register Type -->
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <label class="form-label text-secondary">Register Type</label>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-9">
                            <div class="form-group d-flex align-items-center">
                                <select class="form-control border-primary" name="register_type" required>
                                    <option value="" selected>-- Select Register Type --</option>
                                    <option value="EMAIL">EMAIL</option>
                                    <option value="MOBILE">MOBILE</option>
                                </select>
                                <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Verified -->
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-4 col-lg-3">
                            <label class="form-label text-secondary">Verified</label>
                        </div>
                        <div class="col-sm-12 col-md-8 col-lg-9">
                            <div class="form-check form-check-lg d-flex align-items-center">
                                <input type="checkbox" name="verified" class="form-check-input" value="1" id="flexCheckDefault">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).on('click', '.pass_show .ptxt1', function() {
        $(this).text($(this).text() == "Show" ? "Hide" : "Show");
        $(this).prev().attr('type', function(index, attr) {
            return attr == 'password' ? 'text' : 'password';
        });
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
