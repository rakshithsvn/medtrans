@extends('layouts.back.index')

@section('content')

<section class="section">
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <!-- Section Title -->
            <h6 class="border-bottom mb-3 profile_pge" style="color: white;">Module Management</h6>

            <!-- Action Buttons -->
            <div class="text-right mb-4">
                <a href="{{ route('roles') }}" class="btn btn-primary">
                    View Role
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                    Create
                </button>
            </div>

            <!-- Module Table -->
            <div class="table-responsive">
                <table class="table table-borderless data-table">
                    <thead>
                        <tr style="background: #164966; color: white;">
                            <th class="ps-3 pe-3">Module Name</th>
                            <th class="ps-3 pe-3">URL</th>
                            <th class="ps-3 pe-3">Icon</th>
                            <th class="ps-3 pe-3">Hierarchy</th>
                            <th class="ps-3 pe-3">Active</th>
                            <th class="ps-3 pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(@$modules)
                        @foreach(@$modules as $key=>$module)
                        <tr class="border-bottom align-middle">
                            <td class="ps-3 pe-3">{{ @$module->name }}</td>
                            <td class="ps-3 pe-3">{{ @$module->url }}</td>
                            <td class="ps-3 pe-3">{{ @$module->icon }}</td>
                            <td class="ps-3 pe-3">{{ @$module->hierarchy }}</td>
                            <td class="ps-3 pe-3 text-center">
                                <div class="form-check form-check-lg d-flex align-items-center justify-content-center">
                                    <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" @if(@$module->view == 1) checked @endif disabled>
                                </div>
                            </td>
                            <td class="ps-3 pe-3 text-center">
                                <button class="btn btn-sm btn-primary d-flex align-items-center ms-auto me-2" data-bs-toggle="modal" data-bs-target="#create{{$module->id}}">
                                    Edit
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Module Modal -->
                        <div class="modal fade" id="create{{$module->id}}" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: #164966; color: white;">
                                        <h5 class="modal-title" id="reportLabel" style="color:white">Edit Module</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="update-form" action="{{ route('admin/update-module') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ @$module->id }}">
                                            <div class="row g-3">
                                                <!-- Module Name -->
                                                <div class="col-md-4">
                                                    <label class="form-label text-secondary">Name</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="name" class="form-control border-primary" placeholder="Name" value="{{ @$module->name }}" required>
                                                </div>

                                                <!-- URL -->
                                                <div class="col-md-4">
                                                    <label class="form-label text-secondary">URL</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="url" class="form-control border-primary" placeholder="URL" value="{{ @$module->url }}" required>
                                                </div>

                                                <!-- Icon -->
                                                <div class="col-md-4">
                                                    <label class="form-label text-secondary">Icon</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="icon" class="form-control border-primary" placeholder="Icon" value="{{ @$module->icon }}" required>
                                                </div>

                                                <!-- Hierarchy -->
                                                <div class="col-md-4">
                                                    <label class="form-label text-secondary">Hierarchy</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" name="hierarchy" class="form-control border-primary" placeholder="Hierarchy" value="{{ @$module->hierarchy }}" required>
                                                </div>

                                                <!-- Active -->
                                                <div class="col-md-4">
                                                    <label class="form-label text-secondary">Active</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-check form-check-lg d-flex align-items-center">
                                                        <input type="checkbox" name="view" class="form-check-input" value="1" id="flexCheckDefault" @if(@$module->view == 1) checked @endif>
                                                    </div>
                                                </div>

                                                <!-- roles -->
                                                @if(@$module->view == 1)
                                                <div class="col-md-12">
                                                    <h6 class="p-2">Roles</h6>
                                                    <div class="row">
                                                        @foreach(@$roles as $role)
                                                        <div class="col-md-3 mb-3">
                                                            <div class="form-check form-check-lg d-flex align-items-center">
                                                                <input type="checkbox" name="role[]" class="form-check-input" value="{{ @$role->id }}" id="flexCheckDefault" @if(@$role_modules->where('module_id', @$module->id)->where('role_id', @$role->id )->count()>0) checked @endif>
                                                                <label class="form-check-label ms-2" for="flexCheckDefault">{{ @$role->name }}</label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="text-center mt-2">
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
                            <td colspan="6" class="text-center text-muted py-3">No modules found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $modules->links() }}
            </div>
        </div>
    </div>

    <!-- Create Module Modal -->
    <div class="modal fade" id="create" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #164966; color: white;">
                    <h5 class="modal-title" id="reportLabel" style="color:white;">Create Module</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="update-form" action="{{ route('admin/update-module') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <!-- Module Name -->
                            <div class="col-md-4">
                                <label class="form-label text-secondary">Name</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="name" class="form-control border-primary" placeholder="Name" required>
                            </div>

                            <!-- URL -->
                            <div class="col-md-4">
                                <label class="form-label text-secondary">URL</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="url" class="form-control border-primary" placeholder="URL" required>
                            </div>

                            <!-- Icon -->
                            <div class="col-md-4">
                                <label class="form-label text-secondary">Icon</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="icon" class="form-control border-primary" placeholder="Icon" required>
                            </div>

                            <!-- Hierarchy -->
                            <div class="col-md-4">
                                <label class="form-label text-secondary">Hierarchy</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" name="hierarchy" class="form-control border-primary" placeholder="Hierarchy" required>
                            </div>

                            <!-- Active -->
                            <div class="col-md-4">
                                <label class="form-label text-secondary">Active</label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-check form-check-lg d-flex align-items-center">
                                    <input type="checkbox" name="view" class="form-check-input" value="1" id="flexCheckDefault">
                                </div>
                            </div>

                            <!-- roles -->
                            <div class="col-md-12">
                                <h6 class="p-2">Roles</h6>
                                <div class="row">
                                    @foreach(@$roles as $role)
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check form-check-lg d-flex align-items-center">
                                            <input type="checkbox" name="role[]" class="form-check-input" value="{{ @$role->id }}" id="flexCheckDefault">
                                            <label class="form-check-label ms-2" for="flexCheckDefault">{{ @$role->name }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
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