@extends('layouts.back.index')

@section('content')

<section class="section">
    <!-- Role Management Card -->
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Section Title -->
            <h6 class="border-bottom mb-4 profile_pge" style="color: white">Role Management</h6>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('modules') }}" class="btn btn-primary">
                    View Modules
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                    Create Role
                </button>
            </div>

            <!-- Role Table -->
            <div class="table-responsive">
                <table class="table table-borderless data-table">
                        <thead>
                        <tr style="background: #164966; color: white;">
                            <th class="ps-3 py-2">Role Name</th>
                            <th class="ps-3 py-2">Slug</th>
                            <th class="ps-3 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(@$roles)
                        @foreach(@$roles as $key=>$role)
                        <tr class="border-bottom align-middle">
                            <td class="ps-3 pe-3">{{ @$role->name }}</td>
                            <td class="ps-3 pe-3">{{ @$role->slug }}</td>
                            <td class="ps-3 pe-3 text-center">
                                <button class="btn btn-sm btn-primary d-flex align-items-center ms-auto me-2 justify-content-center" data-bs-toggle="modal" data-bs-target="#create{{$role->id}}">
                                    Edit
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Role Modal -->
                        <div class="modal fade" id="create{{$role->id}}" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: #164966; color: white;">
                                        <h5 class="modal-title" id="reportLabel" style="color:white">Edit Role</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="update-form" action="{{ route('admin/update-role') }}" method="POST" class="">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ @$role->id }}">
                                            <div class="row mb-3">
                                                <div class="col-sm-12 col-md-4 col-lg-3">
                                                    <label class="form-label text-secondary">Name</label>
                                                </div>
                                                <div class="col-sm-12 col-md-8 col-lg-9">
                                                    <div class="form-group d-flex align-items-center">
                                                        <input type="text" name="name" class="form-control border-primary" placeholder="Name" value="{{ @$role->name }}" required>
                                                        <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                                    </div>
                                                </div>
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
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">No roles found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="create" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: #164966; color: white;">
                    <h5 class="modal-title" id="reportLabel" style="color:white;">Create Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="update-form" action="{{ route('admin/update-role') }}" method="POST" class="">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-4 col-lg-3">
                                <label class="form-label text-secondary">Name</label>
                            </div>
                            <div class="col-sm-12 col-md-8 col-lg-9">
                                <div class="form-group d-flex align-items-center">
                                    <input type="text" name="name" class="form-control border-primary" placeholder="Name" value="{{ @$data->name }}" required>
                                    <i class="fas fa-pencil-alt ms-2 text-secondary"></i>
                                </div>
                            </div>
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
</section>

@endsection
