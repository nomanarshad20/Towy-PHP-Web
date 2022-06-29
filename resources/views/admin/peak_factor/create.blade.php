@extends('layouts.admin.index')

@section('title')
    Peak Factor Create
@endsection




@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Peak Factor Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Peak Factor Information</h6>
                    <form id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Point</label>
                                    <input type="text" name="start_point" class="form-control"
                                           placeholder="Enter Start Point" onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">End Point</label>
                                    <input type="text" name="end_point" class="form-control"
                                           placeholder="Enter End Point" onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Factor Rate</label>
                                    <input type="text" name="factor_rate" class="form-control"
                                           onkeypress="return isNumberKey(event)"
                                           placeholder="Enter Factor Rate" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->

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
                    url: '{{route("peakFactorSave")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('peakFactorListing')}}';
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
