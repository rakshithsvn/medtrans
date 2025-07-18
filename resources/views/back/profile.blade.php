@extends('layouts.back.index')

@section('content')

@if(@$data)

<section class="section my-2">
    <div class="card shadow-sm p-4">
        <h6 class="border-bottom mb-3 profile_pge">BASIC INFORMATION</h6>
        <div class="row">
            <div class="col-sm-12 col-md-12"><span class="card-title">Name: </span><span class="name border-bottom">
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
            <div class="col-sm-12 col-md-12"><span class="card-title">Admission No: </span><span class="border-bottom">
                {{ @$data->AdmissionNumber }}</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="card shadow-sm p-4">
        <h6 class="border-bottom mb-3 profile_pge">CONTACT INFORMATION</h6>
        <form id="update-form" action="{{ route('admin/update-student') }}" method="POST" class="">
            @csrf
            <input type="hidden" name="CampusID" value="{{ @$data->CampusID }}">
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2 mt-2">
                    <h4 class="card-title">Email: </h4>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group mb-4 d-flex">
                        <input type="mail" name="Email" class="form-control form-control-md" placeholder="Email" value="{{ @$data->Email }}" autocomplete="off">&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2 mt-2">
                    <h4 class="card-title">Mobile: </h4>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                    <div class="form-group mb-4 d-flex">
                        <input type="phone" name="MobileNumber" class="form-control form-control-md" placeholder="Mobile" value="{{ @$data->MobileNumber }}" autocomplete="off">&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2 mt-2">
                    <h4 class="card-title">Address: </h4>
                </div>
                <div class="col-sm-12 col-md-4  col-lg-4 col-xl-4">
                    <div class="form-group mb-2 d-flex">
                        <textarea name="Address" class="form-control form-control-md" placeholder="Address" rows="6">{!! @$data->Address !!}</textarea>&nbsp;&nbsp;<i class="bi bi-pencil-fill mt-2 edit"></i>&nbsp;&nbsp;{{-- <i class="bi bi-trash-fill mt-2 delete"></i> --}}
                    </div> 
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>

@endif

@endsection
