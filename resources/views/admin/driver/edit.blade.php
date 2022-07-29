@extends('layouts.admin.index')

@section('title')
    Driver Edit
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/dataTables.bootstrap4.css')}}">


@endsection


@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Driver Edit</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Driver Information</h6>
                    <form id="updateDriverForm">
                        @csrf
                        <input type="hidden" name="id" value="{{$data->id}}">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name"
                                           value="{{$data->name}}">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" autocomplete="chrome-off"
                                           placeholder="Enter Email" value="{{$data->email}}">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile No</label>
                                    <input type="text" name="mobile_no" class="form-control"
                                           placeholder="Enter Mobile No" value="{{$data->mobile_no}}">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="Enter Password" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Franchise</label>
                                    <select name="franchise_id" class="form-control">
                                        <option value="" selected disabled>Select</option>
                                        @foreach($franchises as $franchise)
                                            <option
                                                value="{{$franchise->id}}" {{isset($data->driver) ? $data->driver->franchise_id == $franchise->id ? 'selected':'':''}}>{{$franchise->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" placeholder="Enter City"
                                           value="{{isset($data->driver) ? $data->driver->city:''}}">

                                </div>
                            </div><!-- Col -->


                        </div><!-- Row -->

                        <h6 class="card-title">Vehicle Information</h6>

                        <div class="row">
{{--                            <div class="col-sm-6">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Vehicle Type</label>--}}
{{--                                    <select class="form-control" name="vehicle_type_id">--}}
{{--                                        <option value="" selected disabled>Select</option>--}}
{{--                                        @foreach($vehicleTypes as $vehicleType)--}}
{{--                                            <option value="{{$vehicleType->id}}" {{$data->driver->vehicle_type_id == $vehicleType->id ? 'selected':''}} >{{$vehicleType->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}


{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Name</label>
                                    <input type="text" name="vehicle_name" class="form-control"
                                           placeholder="Enter Vehicle Name" value="{{isset($data->driver->vehicle) ? $data->driver->vehicle->name:''}}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Model</label>
                                    <input type="text" name="model" class="form-control"
                                           placeholder="Enter Vehicle Model" value="{{isset($data->driver->vehicle) ? $data->driver->vehicle->model:''}}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Model Year</label>
                                    <input type="text" name="model_year" class="form-control"
                                           placeholder="Enter Vehicle Model Year"
                                           value="{{isset($data->driver->vehicle) ? $data->driver->vehicle->model_year:''}}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control"
                                           placeholder="Enter Vehicle Registration Number"
                                           value="{{isset($data->driver->vehicle) ? $data->driver->vehicle->registration_number:''}}">
                                </div>
                            </div>
                        </div>

                        <h6 class="card-title">
                            Driver Documentation
                        </h6>

                        <div class="row">
                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Drivers License</h6>
                                        <input type="file" accept="image/jpg, image/png, image/jpeg"
                                               @if(isset($data->driver))
                                                {{ $data->driver->drivers_license ?  'data-default-file='.asset($data->driver->drivers_license):'' }}
                                               @endif
                                               id="cnic_front_side"
                                               name="drivers_license" accept="" class="myDropify"/>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Vehicle Insurance</h6>
                                        <input type="file" accept="image/jpg, image/png, image/jpeg"
                                               @if(isset($data->driver))
                                               {{ $data->driver->vehicle_insurance ?  'data-default-file='.asset($data->driver->vehicle_insurance):'' }}
                                               @endif
                                               id="cnic_back_side"
                                               name="vehicle_insurance" class="myDropify"/>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Vehicle Inspection</h6>
                                        <input type="file" accept="image/jpg, image/png, image/jpeg"
                                               @if(isset($data->driver))
                                               {{ $data->driver->vehicle_inspection ?  'data-default-file='.asset($data->driver->vehicle_inspection):'' }}
                                               @endif
                                               id="license_front_side"
                                               name="vehicle_inspection" class="myDropify"/>

                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Profile Image</h6>
                                        <input type="file"
                                               @if(isset($data->driver))
                                               {{ $data->image ?  'data-default-file='.asset($data->image):'' }}
                                               @endif
                                               name="profile_image"
                                               class="myDropify" id="profile_image"
                                               accept="image/jpg, image/png, image/jpeg"/>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Vehicle Registration Book Image</h6>
                                        <input type="file"
                                               @if(isset($data->driver->vehicle))
                                               {{ $data->driver->vehicle->registration_book ?  'data-default-file='.asset($data->driver->vehicle->registration_book):'' }}
                                               @endif
                                               accept="image/jpg, image/png, image/jpeg" id="registration_book"
                                               name="registration_book" class="myDropify"/>

                                    </div>
                                </div>
                            </div>


                        </div>


                        <button type="button" class="btn btn-primary updateDriverBtn">
                            Update
                        </button>

                        <a href="{{route('driverListing')}}">
                            <button type="button" class="btn btn-danger">
                                Cancel
                            </button>
                        </a>
                    </form>


                </div>


            </div>


        </div>

    </div>




@endsection

@section('script')


    <script src="{{asset('admin/js/dropify.min.js')}}"></script>

    <script src="{{asset('admin/js/dropify.js')}}"></script>

    <script>
        $(document).ready(function () {

            $('.updateDriverBtn').click(function () {

                var data = new FormData($('#updateDriverForm')[0]);

                $.blockUI({
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }
                });
                $.ajax({

                    type: 'POST',
                    url: '{{route("driverUpdate")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('driverListing')}}';
                            }, 1000);

                        } else if (response.result == 'error') {
                            $.unblockUI();
                            errorMsg(response.message);
                        }
                    },
                    error: function (data) {
                        $.each(data.responseJSON.errors, function (key, value) {
                            $.unblockUI();
                            errorMsg(value);
                        });
                    }


                });
            });

            $(document).on('click', '.dropify-clear', function () {
                var data = $(this).parents('div.dropify-wrapper').find('input').attr('id');
                var id = '{{$data->id}}';

                $.blockUI({
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .5,
                        color: '#fff'
                    }
                });
                $.ajax({

                    type: 'GET',
                    url: '{{route("driverDeleteImage")}}',
                    data: {'id': id, 'image': data},

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);


                        } else if (response.result == 'error') {
                            $.unblockUI();
                            errorMsg(response.message);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }
                    },
                    error: function (data) {
                        $.each(data.responseJSON.errors, function (key, value) {
                            $.unblockUI();
                            errorMsg(value);
                        });
                    }


                });

            });


        });
    </script>
@endsection
