@extends('layouts.admin.index')

@section('title')
    Voucher Code Send
@endsection

@section('style')

    <link rel="stylesheet" href="{{asset('admin/css/select2(4.0.3).min.css')}}">

@endsection

@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Voucher Code Send</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Voucher Code Information</h6>
                    <form id="createForm">
                        <input type="hidden" name="id" value="{{$data->id}}">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Voucher Code</label>
                                    <input type="text" name="voucher_code" class="form-control"
                                           readonly value="{{$data->voucher_code}}"
                                           placeholder="Enter Voucher Code">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" name="expiry_date" class="form-control datepicker"
                                           placeholder="Enter Expiry Date" disabled
                                           value="{{\Carbon\Carbon::parse($data->expiry_date)->format('m/d/Y')}}">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                                    <select name="discount_type" class="form-control">
                                        <option
                                            value="percentage" {{$data->discount_type == 'percentage' ? 'selected':''}}>
                                            Percentage (%)
                                        </option>
                                    </select>
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Discounted Value</label>
                                    <input type="text" name="discount_value" class="form-control" readonly
                                           value="{{$data->discount_value}}"
                                           placeholder="Enter Discounted Value" onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Passengers </label>
                                    <select class="form-control  js-example-basic-multiple" multiple="multiple"
                                            name="passengers[]">
                                        @foreach($passengers as $passenger)
                                                <option value="{{$passenger->id}}">{{$passenger->name .' ( '. $passenger->referral_code .' ) '}}</option>

                                        @endforeach
                                    </select>

                                </div>
                            </div><!-- Col -->

                        </div><!-- Row -->


                        <button type="button" class="btn btn-primary createBtn">
                            Send
                        </button>
                    </form>



                    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin pt-5">
                        <div>
                            <h4 class="mb-3 mb-md-0">Already Send To Passengers</h4>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Passenger Name</th>
                                <th>Voucher Code</th>
                                <th>Expiry Date</th>
                                <th>Discount Type</th>
                                <th>Percentage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data->voucherCodePassenger as $voucherCode)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$voucherCode->passenger->name .' ( '.$voucherCode->passenger->referral_code .' ) ' }}</td>
                                    <td>{{$voucherCode->voucher_code}}</td>
                                    <td>{{$voucherCode->expiry_date}}</td>
                                    <td>{{$voucherCode->discount_type}}</td>
                                    <td>{{$voucherCode->discount_amount}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script src="{{asset('admin/js/select2(4.0.3).full.js')}}"></script>

    <script>
        $(document).ready(function () {

            $('.js-example-basic-multiple').select2();


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
                    url: '{{route("voucherCodeSendPassenger")}}',
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
