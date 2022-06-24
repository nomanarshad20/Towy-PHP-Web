@extends('layouts.admin.index')

@section('title')
    Vehicle Create
@endsection




@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Vehicle Information</h6>
                    <form id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Model</label>
                                    <input type="text" name="model" class="form-control"
                                           placeholder="Enter Model">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Model Year</label>
                                    <input type="text" name="model_year" class="form-control"
                                           placeholder="Enter Model Year" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control"
                                           placeholder="Enter Registration Number">


                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Driver</label>
                                    <select class="form-control" name="driver_id">
                                        <option value="" selected disabled>Select</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{$driver->user->id}}">{{$driver->user->name}}</option>
                                        @endforeach
                                    </select>


                                </div>
                            </div><!-- Col -->


{{--                            <div class="col-sm-6">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Vehicle Type</label>--}}
{{--                                    <select class="form-control" name="vehicle_type_id">--}}
{{--                                        <option value="" selected disabled>Select</option>--}}
{{--                                        @foreach($vehicleTypes as $vehicleType)--}}
{{--                                            <option value="{{$vehicleType->id}}">{{$vehicleType->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}


{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}


                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Vehicle Registration Book Image</h6>
                                        <input type="file"
                                               accept="image/jpg, image/png, image/jpeg" id="registration_book"
                                               name="registration_book" class="myDropify"/>

                                    </div>
                                </div>
                            </div>


                        </div><!-- Row -->


                        <button type="button" class="btn btn-primary createBtn">
                            Create
                        </button>
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

            $('.createBtn').click(function () {

                var data = new FormData($('#createForm')[0]);

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
                    url: '{{route("vehicleSave")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('vehicleListing')}}';
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


        });
    </script>
@endsection
