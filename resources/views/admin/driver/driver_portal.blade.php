@extends('layouts.admin.index')

@section('title')
    Driver Portal
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery-ui.css')}}">
@endsection


@section('body')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Driver Portal </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 main-content ps-xl-4 pe-xl-5">

            <div class="example">
                <div class="d-flex align-items-start">
                    @if($userInfo->image)
                        <img src="{{asset($userInfo->image)}}" class="wd-100  me-3" alt="...">
                    @else
                        <img src="{{asset('admin/img/user (5).png')}}" class="wd-100  me-3" alt="...">
                    @endif
                    <div>
                        <h5 class="mb-2">{{$userInfo->first_name.' '.$userInfo->last_name ?? ''}}</h5>
                        <p><b>Contact :</b> {{$userInfo->mobile_no ?? ''}}</p>
                        <p><b>User Referral Code :</b> {{$userInfo->referral_code ?? ''}}</p>
                        <p><b>Vehicle Number:</b> {{$userInfo->driver->vehicle->registration_number ?? ''}}-{{$userInfo->driver->vehicle->model_year ?? ''}}</p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.driver.modal.recharge_modal')

@endsection



@section('script')
    <script src="{{asset('admin/js/jquery-ui.js')}}"></script>

    <script>

        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $("#recharge_button").click(function () {

            var selectedamount = $('#amount').val();
            var negative_amount = 0;
            var balance = {{$driverWalletBalance}};
            if (balance < 0) {
                negative_amount = abs(balance);
            }

            if (selectedamount < negative_amount || selectedamount == "" || selectedamount == null) {
                errorMsg("Please Add Amount grater than 0 or greater than negative wallet amount.");
                return false;
            } else {
                $('#recharge_modal').modal('show');
            }
            // else {
            //     return false;
            // }

        });

        $('#rechargeRecordBtn').click(function(){
            $('.rechargeWalletForm').submit();
        });

        $('#amount').keypress(function (eve) {
            if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57)) {
                eve.preventDefault();
            }
            // this part is when left part of number is deleted and leaves a . in the leftmost position. For example, 33.25, then 33 is deleted
            $('#amount').keyup(function (eve) {
                // console.log($(this).val().indexOf('.'))
                // if ($(this).val().indexOf('.') == 0) {
                //     $(this).val($(this).val().substring(1));
                // }
                if ($(this).val().indexOf('0') == 0) {
                    $(this).val($(this).val().substring(1));
                } else if ($(this).val().indexOf('.') == 0) {
                    $(this).val($(this).val().substring(1));
                }
            });
        });
    </script>
@endsection

