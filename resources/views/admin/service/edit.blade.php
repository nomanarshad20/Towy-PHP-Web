@extends('layouts.admin.index')

@section('title')
    Service Edit
@endsection



@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Service Edit</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Service Information</h6>
                    <form id="updateForm">
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
                                    <label class="form-label">Base Rate</label>
                                    <input type="text" name="base_rate" class="form-control" value="{{$data->base_rate}}"
                                           placeholder="Enter Base Rate" onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Is Quantity Allowed</label>
                                    <select class="form-control" name="is_quantity_allowed">
                                        <option value="0" {{$data->is_quantity_allowed == 0 ? 'selected':''}}>No</option>
                                        <option value="1" {{$data->is_quantity_allowed == 1 ? 'selected':''}}>Yes</option>
                                    </select>
                                </div>
                            </div><!-- Col -->

{{--                            <div class="col-sm-4">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Initial Distance Rate</label>--}}
{{--                                    <input type="text" name="initial_distance_rate" class="form-control" value="{{$data->initial_distance_rate}}"--}}
{{--                                           onkeypress="return isNumberKey(event)" placeholder="Enter Initial Distance Rate">--}}
{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}

{{--                            <div class="col-sm-4">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Initial Time Rate</label>--}}
{{--                                    <input type="text" name="initial_time_rate" class="form-control" onkeypress="return isNumberKey(event)"--}}
{{--                                           placeholder="Enter Initial Time Rate" value="{{$data->initial_time_rate}}">--}}
{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}

{{--                            <div class="col-sm-4">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Service Time Rate</label>--}}
{{--                                    <input type="text" name="service_time_rate" class="form-control" value="{{$data->service_time_rate}}"--}}
{{--                                           onkeypress="return isNumberKey(event)" placeholder="Enter Service Time Rate">--}}
{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}


                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea cols="4" rows="2" class="form-control" name="description">{{$data->description}}</textarea>
                                </div>
                            </div><!-- Col -->

                            <div class="col-md-6 stretch-card grid-margin grid-margin-md-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Service Image</h6>
                                        <input type="file"
                                               {{ $data->image ?  'data-default-file='.asset($data->image):'' }}
                                               accept="image/jpg, image/png, image/jpeg" id="service_image"
                                               name="image" class="myDropify"/>

                                    </div>
                                </div>
                            </div>


                        </div><!-- Row -->


                        <button type="button" class="btn btn-primary updateBtn">
                            Update
                        </button>

                        <a href="{{route('serviceListing')}}">
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
                    url: '{{route("serviceUpdate")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('serviceListing')}}';
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
                // var data = $(this).parents('div.dropify-wrapper').find('input').attr('id');
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
                    url: '{{route("serviceDeleteImage")}}',
                    data: {'id': id},

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
