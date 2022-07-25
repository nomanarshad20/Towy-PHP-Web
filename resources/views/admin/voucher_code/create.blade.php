@extends('layouts.admin.index')

@section('title')
    Voucher Code Create
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery-ui.css')}}" >
@endsection

@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Voucher Code Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Voucher Code Information</h6>
                    <form id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Voucher Code</label>
                                    <input type="text" name="voucher_code" class="form-control"
                                           placeholder="Enter Voucher Code">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" name="expiry_date" class="form-control datepicker"
                                           placeholder="Enter Expiry Date">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                                    <select name="discount_type" class="form-control">
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Discounted Value</label>
                                    <input type="text" name="discount_value" class="form-control"
                                           placeholder="Enter Discounted Value" onkeypress="return isNumberKey(event)">
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

    <script src="{{asset('admin/js/jquery-ui.js')}}"></script>


    <script>
        $(document).ready(function () {

            $( ".datepicker" ).datepicker({
                minDate: 0,
            });

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
                    url: '{{route("voucherCodeSave")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('voucherCodeListing')}}';
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
