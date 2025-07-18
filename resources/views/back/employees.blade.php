@extends('layouts.back.index')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section class="section">
    <!-- Action Buttons -->
    <div class="card shadow-sm border-0 global-font mb-4">
        <div class="card-body p-4">
            <div class="text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal">
                    New Employee
                </button>
            </div>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="card shadow-sm border-0 global-font">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-borderless data-table">
                    <thead style="color: white;">
                        <tr>
                            <th class="ps-3 py-2" >Employee/Dept ID</th>
                            <th class="ps-3 py-2" >Employee Name</th>
                            <th class="ps-3 py-2" >Designation</th>
                            <th class="ps-3 py-2 ">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $exRow)
                        <tr class="border-bottom align-middle">
                            <td class="ps-3">{{ $exRow->employee_id }}</td>
                            <td class="ps-3">{{ $exRow->name }}</td>
                            <td class="ps-3">{{ $exRow->designation }}</td>
                            <td class="ps-3 text-end">
                                <button type="button" class="btn btn-sm btn-primary d-flex align-items-center ms-auto me-2 justify-content-center view-btn"  
										data-id="{{ $exRow->id }}"
										data-type="{{ $exRow->type }}"
										data-name="{{ $exRow->name }}"
										data-employee_id="{{ $exRow->employee_id }}"
										data-address="{{ $exRow->address }}"  
										data-email="{{ $exRow->email }}"
										data-mobile="{{ $exRow->mobile }}"
										data-designation="{{ $exRow->designation }}"
										data-area_assigned="{{ $exRow->area_assigned }}"
										data-referred_patients="{{ $exRow->referred_patients }}" 
										data-bs-toggle="modal" data-bs-target="#employeeModal">
                                   View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No employees found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="my-0">
                {{ $employees->links() }}
            </nav>
        </div>
    </div>

    <!-- Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header py-3" style="background: #164966;">
                    <h5 class="modal-title text-white mb-0" id="modalTitle">
                        <i class="fas fa-user-tie me-2"></i>{{ isset($employee) ? 'Edit Employee' : 'Create Employee' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <form id="employeeForm" action="{{ isset($employee) ? route('employee-update', $employee->id) : route('employee.store') }}" method="POST">
                        @csrf
                        @if(isset($employee))
                        @method('PUT')
                        @endif

                        <input type="hidden" id="id" name="id" value="{{ isset($employee) ? $employee->id : '' }}">
                        <input type="hidden" id="type" name="type" value="{{@$type}}">

                        <div class="row g-3">
                            <!-- Employee Name and Employee ID -->
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Employee Name</label>
                                <input type="text" id="employeeName" name="name" class="form-control border-primary" value="{{ isset($employee) ? $employee->name : '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Employee/Dept ID</label>
                                <input type="text" id="employeeId" name="employee_id" class="form-control border-primary" value="{{ isset($employee) ? $employee->employee_id : '' }}" required>
                            </div>

                            <!-- Address and Email -->
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Address</label>
                                <input type="text" id="address" name="address" class="form-control border-primary" value="{{ isset($employee) ? $employee->address : '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Email</label>
                                <input type="email" id="email" name="email" class="form-control border-primary" value="{{ isset($employee) ? $employee->email : '' }}" required>
                            </div>

                            <!-- Mobile and Designation -->
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Mobile</label>
                                <input type="text" id="mobile" name="mobile" class="form-control border-primary" value="{{ isset($employee) ? $employee->mobile : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Designation</label>
                                <input type="text" id="designation" name="designation" class="form-control border-primary" value="{{ isset($employee) ? $employee->designation : '' }}">
                            </div>

                            <!-- Area Assigned -->
                            <!-- <div class="col-md-12">
                                <label class="form-label text-secondary">Area Assigned</label>
                                <textarea id="areaAssigned" name="area_assigned[]" class="form-control border-primary" rows="3"></textarea>
                            </div> -->

                            <!-- Username and Password -->
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Username</label>
                                <input type="text" id="username" name="username" class="form-control border-primary" value="{{ isset($employee) ? $employee->username : '' }}" required readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary">Password</label>
                                <input type="password" id="password" name="password" class="form-control border-primary" value="{{ isset($employee) ? $employee->password : '' }}" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ isset($employee) ? 'Update Changes' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script>
    $(document).ready(function() {

        $('#employeeId').on('keyup', function() {

            const employeeId = $(this).val();

            if (username) {
                $('#username').val(employeeId);
            } else {
                $('#username').val('');
            }
        });

        $('.view-btn').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let employeeId = $(this).data('employee_id');
            let address = $(this).data('address');
            let email = $(this).data('email');
            let mobile = $(this).data('mobile');
            let designation = $(this).data('designation');
            let areaAssigned = $(this).data('area_assigned');
            let referredPatients = $(this).data('referred_patients');

            let initialSelections = areaAssigned.split(',').map(item => item.trim());
            console.log(initialSelections);
            $('#areaAssigned').val(initialSelections).trigger('change.select2');

            $('#areaAssigned').on('select2:select select2:unselect', function(e) {
                let selectedValues = $('#areaAssigned').val() || [];
                $('#areaAssigned').val(selectedValues).trigger('change.select2');
            });

            $('#employeeId').val(id);
            $('#employeeName').val(name);
            $('#employeeId').val(employeeId);
            $('#address').val(address);
            $('#email').val(email);
            $('#mobile').val(mobile);
            $('#designation').val(designation);
            $('#areaAssigned').val(areaAssigned);
            $('#referredPatients').val(referredPatients);
            $('#type').val(type);
            $('form').attr('action', '/employee-update/' + id);
            $('form').attr('method', 'POST');
            $('#saveChanges').text('Update Changes');
            $('#modalTitle').text('Update Employee');
            $('.login').hide();
            $('#username').attr('required', false);
            $('#password').attr('required', false);
        });

        $('#employeeModal').on('hidden.bs.modal', function() {
            $('#employeeId').val('');
            $('#employeeName').val('');
            $('#employeeId').val('');
            $('#address').val('');
            $('#email').val('');
            $('#mobile').val('');
            $('#designation').val('');
            $('#areaAssigned').val('');
            $('#referredPatients').val('');

            $('form').attr('action', '/employees');
            $('form').attr('method', 'POST');
            $('#saveChanges').text('Save Changes');
            $('#modalTitle').text('Create Employee');
            $('.login').show();
            $('#username').attr('required', true);
            $('#password').attr('required', true);
        });
    });

</script>

<script>
    $(document).ready(function() {

        $('#areaAssign').select2({
            placeholder: "Select areas"
            , tags: true
        });

        // Function to toggle the custom input field
        function toggleCustomAreaInput() {
            var isOtherSelected = $('#areaAssigned').val().includes('other');

            if (isOtherSelected) {
                $('#customAreaInput').show().prop('required', true);
            } else {
                $('#customAreaInput').hide().prop('required', false);
            }
        }

        // Initial check when page loads
        toggleCustomAreaInput();

        // Check whenever the selection changes
        $('#areaAssigned').on('change', toggleCustomAreaInput);
    });

</script>
@endsection
