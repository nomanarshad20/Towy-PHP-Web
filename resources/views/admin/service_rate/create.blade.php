@extends('layouts.admin.index')

@section('title')
    Service Rate
@endsection




@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Service Rate</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Service Information</h6>
                    <form id="createForm">
                        @csrf
                        <input type="hidden" name="id" value="{{isset($data->id) ? $data->id:''}}">
                        <div class="row">


                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Initial Distance Rate</label>
                                    <input type="text" name="initial_distance_rate" class="form-control"
                                           value="{{isset($data->initial_distance_rate) ? $data->initial_distance_rate:''}}"
                                           onkeypress="return isNumberKey(event)" placeholder="Enter Initial Distance Rate">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Initial Time Rate</label>
                                    <input type="text" name="initial_time_rate" class="form-control"
                                           value="{{isset($data->initial_time_rate) ? $data->initial_time_rate:''}}"
                                           onkeypress="return isNumberKey(event)" placeholder="Enter Initial Time Rate">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Service Time Rate</label>
                                    <input type="text" name="service_time_rate"
                                           value="{{isset($data->service_time_rate) ? $data->service_time_rate:''}}"
                                           class="form-control" onkeypress="return isNumberKey(event)" placeholder="Enter Service Time Rate">
                                </div>
                            </div><!-- Col -->


                        </div><!-- Row -->


                        <button type="button" class="btn btn-primary createBtn">
                            Save
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
                    url: '{{route("saveServiceRate")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('serviceRate')}}';
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
