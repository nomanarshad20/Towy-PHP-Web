@extends('layouts.admin.index')

@section('title')
    Vehicle Type Create
@endsection




@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Type Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Vehicle Type Information</h6>
                    <form id="createForm">
                        @csrf
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control"
                                           placeholder="Enter Name"
                                           onkeypress="return isCharacterKey(event)">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Min Fare</label>
                                    <input type="text" name="min_fare" class="form-control"
                                           placeholder="Enter Min Fare"
                                           onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Per Km Rate</label>
                                    <input type="text" name="per_km_rate" class="form-control"
                                           onkeypress="return isNumberKey(event)"
                                           placeholder="Enter Per Km Rate" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Per Min Rate</label>
                                    <input type="text" name="per_min_rate" class="form-control"
                                           placeholder="Enter Per Min Rate" onkeypress="return isNumberKey(event)">


                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Initial Per Km Rate (Before PickUp)</label>
                                    <input type="text" name="initial_distance_rate" class="form-control"
                                           onkeypress="return isNumberKey(event)"
                                           placeholder="Enter Initial Per Km Rate" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Initial Per Min Rate (Before PickUp)</label>
                                    <input type="text" name="initial_time_rate" class="form-control"
                                           placeholder="Enter Initial Per Min Rate" onkeypress="return isNumberKey(event)">


                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Waiting Price Per Min</label>
                                    <input type="text" name="waiting_price_per_min" class="form-control"
                                           placeholder="Enter Waiting Price Per Min"
                                           onkeypress="return isNumberKey(event)">


                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Tax (%)</label>
                                    <input type="text" name="tax_rate" class="form-control"
                                           placeholder="Enter Tax (%)" onkeypress="return isNumberKey(event)">


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
                    url: '{{route("vehicleTypeSave")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('vehicleTypeListing')}}';
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
