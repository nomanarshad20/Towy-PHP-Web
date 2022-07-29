@extends('layouts.admin.index')

@section('title')
    Driver Create
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/dataTables.bootstrap4.css')}}">
@endsection


@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Driver Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Driver Information</h6>
                    <form id="createDriverForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="Enter First Name">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="Enter Last Name">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" autocomplete="chrome-off"
                                           placeholder="Enter Email">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile No</label>
                                    <input type="text" name="mobile_no" class="form-control"
                                           placeholder="Enter Mobile No">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="Enter Password" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->

{{--                            <div class="col-sm-6">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Franchise</label>--}}
{{--                                    <select name="franchise_id" class="form-control">--}}
{{--                                        <option value="" selected disabled>Select</option>--}}
{{--                                        @foreach($franchises as $franchise)--}}
{{--                                            <option value="{{$franchise->id}}">{{$franchise->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" placeholder="Enter City">

                                </div>
                            </div><!-- Col -->


                        </div><!-- Row -->

                        <h6 class="card-title">Vehicle Information</h6>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Type</label>
                                    <select class="form-control" name="vehicle_type_id">
                                        <option value="" selected disabled>Select</option>
                                        @foreach($vehicleTypes as $vehicleType)
                                            <option value="{{$vehicleType->id}}" >{{$vehicleType->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Name</label>
                                    <input type="text" name="vehicle_name" class="form-control"
                                           placeholder="Enter Vehicle Name">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Vehicle Model</label>
                                    <input type="text" name="model" class="form-control"
                                           placeholder="Enter Vehicle Model">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Model Year</label>
                                    <input type="text" name="model_year" class="form-control"
                                           placeholder="Enter Vehicle Model Year">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control"
                                           placeholder="Enter Vehicle Registration Number">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary createDriverBtn">
                            Create
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function () {

            $('.createDriverBtn').click(function () {

                var data = $('#createDriverForm').serialize();

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
                    url: '{{route("driverSave")}}',
                    data: data,

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


        });
    </script>
@endsection
