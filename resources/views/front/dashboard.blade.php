@extends('layouts.front.index')

@section('content')

<section class="section">
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm p-4 text-center">
                <div class="row mt-3 mb-5 text-start">
                           
                    <div class="col-md-5">
                        <label>From</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <label>To</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-2 mt-4 text-start">
                        <button class="btn btn-success">Go</button>
                    </div>
                </div>

                    <a href="total_refered_patients.html">
                    <h2>Total Patients - HMIS</h2>
                    <h1>2500</h1>
                </a>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-primary shadow-sm p-4 text-center text-white">
                    
                            <h2 class="text-white">Total In Patients</h2>
                            <h1 class="text-white">1500</h1>
                        </div>
                            
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-primary shadow-sm p-4 text-center text-white">
                    
                            <h2 class="text-white">Total Out Patients</h2>
                            <h1 class="text-white">500</h1>
                        </div>    
                    </div>
                </div>
            </div>
            <div class="col-md-12">
            <div class="card shadow-sm p-4 text-center">
                    <a href="totalexternalpatients.html">
                <h2>Total Referred Patients - Marketing Department</h2>
                <h1>1300</h1>
            </a>
            <div class="row">
                <div class="col-md-6">
            <div class="card bg-primary shadow-sm p-4 text-center text-white">
                
                <h2 class="text-white">Total In Patients</h2>
                <h1 class="text-white">700</h1>
            </div>
                
            </div>
            <div class="col-md-6">
            <div class="card bg-primary shadow-sm p-4 text-center text-white">
                
                <h2 class="text-white">Total Out Patients</h2>
                <h1 class="text-white">600</h1>
            </div>
                
            </div>
            </div>
            </div>
        </div>
        
    </div>
</section>

<!-- <section class="section my-2">
    <div class="card shadow-sm p-4">
        <h6 class="border-bottom mb-3 profile_pge">BASIC INFORMATION</h6>
        <div class="row">
            <div class="col-sm-12 col-md-12"><span class="card-title">@if($user->register_by == 'PARENT') Student @endif Name: </span><span class="name border-bottom">
                {!! @$data->FirstName !!} {!! @$data->LastName !!}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">DOB: </span>
                <span class="border-bottom">@if(@$data->DOB) {{ Carbon\Carbon::parse(@$data->DOB)->format('d M Y') }} @endif</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">Father Name: </span><span class="border-bottom">
                {{ @$data->FatherName }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">Mother Name: </span><span class="border-bottom">
                {{ @$data->MotherName }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">Degree: </span><span class="border-bottom">{{ @$CourseDetail->DegreeName }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">Batch: </span><span class="border-bottom">{{ @$CourseDetail->BatchName }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">Campus ID: </span><span class="border-bottom">{{ @$data->CampusID }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">Register No: </span><span class="border-bottom">{{ @$data->RollNumber }}</span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-12"><span class="card-title">ABC ID: </span><span class="border-bottom">
                {{ @$data->ABCID }}</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="card shadow-sm p-4">
        <h6 class="border-bottom mb-3 profile_pge">CONTACT INFORMATION</h6>
        <form id="update-form" action="{{ route('update-student') }}" method="POST" class="">
            @csrf
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2 mt-2">
                    <h4 class="card-title">Email: </h4>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group mb-4 d-flex">
                        @if($user->register_by == 'PARENT')
                        <input type="mail" name="FatherEmail" class="form-control form-control-md" placeholder="Email" value="{{ @$data->FatherEmail }}" autocomplete="off">&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                        @else
                        <input type="mail" name="Email" class="form-control form-control-md" placeholder="Email" value="{{ @$data->Email }}" autocomplete="off">&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2 mt-2">
                    <h4 class="card-title">Mobile: </h4>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group mb-4 d-flex">
                       @if($user->register_by == 'PARENT')
                       <input type="phone" name="FatherNumber" class="form-control form-control-md" placeholder="Mobile" value="{{ @$data->FatherNumber }}" autocomplete="off">&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                       @else
                       <input type="phone" name="MobileNumber" class="form-control form-control-md" placeholder="Mobile" value="{{ @$data->MobileNumber }}" autocomplete="off">&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                       @endif
                   </div>
               </div>
           </div>
           <div class="row">
            <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2 mt-2">
                @if($user->register_by == 'STUDENT')
                <h4 class="card-title">Address: </h4>
                @endif
            </div>
            <div class="col-sm-12 col-md-4  col-lg-4 col-xl-4">
                @if($user->register_by == 'STUDENT')
                <div class="form-group mb-2 d-flex">
                   <textarea name="Address" class="form-control form-control-md" placeholder="Address" rows="6">{!! @$data->Address !!}</textarea>&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
               </div>
               @endif
               <button type="submit" class="btn btn-primary mt-3">Submit</button>
           </div>
       </div>
   </form>
</div>
</section>

<div class="modal fade" id="welcome" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- <div class="modal-header">
                <h5 class="modal-title" id="reportLabel">Welcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> --}}
            <div class="modal-body">
                <div class="mx-4">
                    @if($user->register_by == 'STUDENT')
                    <h6>Hi, {!! @$data->FirstName !!} {!! @$data->LastName !!}
                    @else
                        <h6>Hi <br/>{!! @$parent !!}
                    @endif<br/><br/><br/>
                    <a href="{{ route('hostel/profile') }}"><strong>Click here to update Hostel Profile</strong></a>

                    <div class="form-group float-right">
                       
                    </div>
                </form>
                </div>
        </div>
    </div>
</div>
</div> -->

@endsection

@section('script')
<script type="text/javascript">
    $(window).on('load', function() {
       {{-- @if($user->register_by == 'STUDENT' && @$data->StayLocationID == null && @$data->RoomID == null) --}}
        @if($user->register_by == 'STUDENT' && @$data->StayLocationID == null && @$stay_room[0] == null)
            if (!sessionStorage.getItem('firstVisit') == 1)
            {
                $('#welcome').modal('show');
                sessionStorage.setItem('firstVisit', '1');
            }
        @endif
    });
</script>
@endsection
