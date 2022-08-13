@extends('layouts.admin.index')

@section('title')
    Passenger Edit
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/dataTables.bootstrap4.css')}}">


@endsection


@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Passenger Edit</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Passenger Information</h6>
                    <form id="updateForm">
                        @csrf
                        <input type="hidden" name="id" value="{{$data->id}}">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name"
                                           value="{{$data->first_name}}"
                                           class="form-control" placeholder="Enter Name">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name"
                                           value="{{$data->last_name}}"
                                           class="form-control" placeholder="Enter Name">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email"
                                           value="{{$data->email}}"
                                           class="form-control" autocomplete="chrome-off"
                                           placeholder="Enter Email">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile No</label>
                                    <input type="text" name="mobile_no" class="form-control"
                                           value="{{$data->mobile_no}}"
                                           placeholder="Enter Mobile No" onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="Enter Password" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->


                        </div><!-- Row -->



                        <button type="button" class="btn btn-primary updateBtn">
                            Update
                        </button>

                        <a href="{{route('passengerListing')}}">
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


    <script>
        $(document).ready(function () {

            $('.updateBtn').click(function () {

                var data = new FormData($('#updateForm')[0]);

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
                    url: '{{route("passengerUpdate")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('passengerListing')}}';
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
