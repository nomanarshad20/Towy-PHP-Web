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
                        <p><b>Vehicle Number:</b> {{$userInfo->driver->vehicle->registration_number ?? ''}}
                            -{{$userInfo->driver->vehicle->model_year ?? ''}}</p>

                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 main-content ps-xl-4 pe-xl-5">

            {{--            <div class="col-md-12 stretch-card">--}}
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Driver Earning Filter</h6>

                    <div class="row">
                        <form id="form1" method="get">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label">From</label>
                                        <input type="text" id="dateFrom" name="fromDate" class="form-control datepicker"
                                               value="{{$_GET['fromDate']??date('Y-m-d')}}"
                                        >
                                    </div>
                                </div><!-- Col -->

                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label">To</label>
                                        <input type="text" id="dateTill" name="tillDate" class="form-control datepicker"
                                               value="{{$_GET['tillDate']??date('Y-m-d')}}"
                                        >
                                    </div>
                                </div><!-- Col -->
                            </div>

                            <button type="submit" id="searchReportFrm" class="btn btn-primary btn-user">
                                Search
                            </button>


                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6 main-content  pe-xl-5">

                    <h4>Driver Earning</h4>

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{$ridesSummary['totalCompletedRides'] ?? 0}} X Trips
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['totalDriverCashEarnings'],2) ?? 0}}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Credit Trips
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['totalDriverWalletAmount'],2) ?? 0}}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Bonus
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['driverTotalBonus'],2) ?? 0}}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{$ridesSummary['totalDriverCancelRides'] ?? 0}} X Passenger Cancel
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['totalDriverCancelPenalty'],2) ?? 0}}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total
                            @if($ridesSummary['newSum'] < 0)
                                <span class="badge bg-danger rounded-pill">
                                    {{number_format($ridesSummary['newSum'],2) ?? 0}}
                                </span>
                            @else
                                <span class="badge bg-primary rounded-pill">
                                    {{number_format($ridesSummary['newSum'],2) ?? 0}}
                                </span>
                            @endif
                        </li>


                    </ul>

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cash collected
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['totalCashCollectedByDriver'],2) ?? 0}}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Current Remaining
                            @if($ridesSummary['remainings'] < 0)
                                <span class="badge bg-danger rounded-pill">
                                    {{number_format($ridesSummary['remainings'],2) ?? 0}}
                                </span>
                            @else
                                <span class="badge bg-primary rounded-pill">
                                    {{number_format($ridesSummary['remainings'],2) ?? 0}}
                                </span>
                            @endif
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Previous Cycle Adjustment
                            @if($ridesSummary['previous_total_amount'] < 0)
                                <span class="badge bg-danger rounded-pill">
                                    {{number_format($ridesSummary['previous_total_amount'],2) ?? 0}}
                                </span>
                            @else
                                <span class="badge bg-primary rounded-pill">
                                    {{number_format($ridesSummary['previous_total_amount'],2) ?? 0}}
                                </span>
                            @endif
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Payable/Receivable
                            @if($ridesSummary['final_total_amount'] < 0)
                                <span class="badge bg-primary rounded-pill">
                                    {{number_format($ridesSummary['final_total_amount'],2) ?? 0}}
                                </span>
                            @else
                                <span class="badge bg-primary rounded-pill">
                                    {{number_format($ridesSummary['final_total_amount'],2) ?? 0}}
                                </span>
                            @endif
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cash Received From Towy

                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['amountPaidToDriver'],2) ?? 0}}
                            </span>

                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cash Paid To Towy

                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['amountReceivedFromDriver'],2) ?? 0}}
                            </span>

                        </li>


                    </ul>

                </div>

                <div class="col-xl-6 main-content  pe-xl-5">
                    <h4>Driver Rating</h4>

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            DRIVER RATING
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['ratingsAvg'],1) ?? 0}}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Acceptance Rate
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['acceptRidesPercent'],2) ?? 0}}%
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ride Completion
                            <span class="badge bg-primary rounded-pill">
                                {{number_format($ridesSummary['completeRidesPercent'],2) ?? 0}}%
                            </span>
                        </li>
                    </ul>


                    <div class="row">

                        <div class="col-6 col-md-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center"> Rides Received</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalReceivedRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center"> Rides Accepted</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalAcceptRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center"> Rides Completed</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalCompletedRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center"> Rides Cancelled</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalDriverCancelRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center"> Passenger Cancelled</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalPassengerCancelRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Rides Rejected</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalRejectRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Admin Cancelled</h5>
                                    <p class="card-text mb-3 text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{$ridesSummary['totalSystemCancelRides'] ?? 0}}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>


        <div class="col-xl-12 main-content ps-xl-4 pe-xl-5">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Payment</h6>

                    <form action="{{route('payReceiveFromDriver')}}" method="post" class="rechargeWalletForm">
                        <div class="row">
                            @csrf
                            <input type="hidden" name="id" value="{{$userInfo->id}}">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">From</label>
                                    <select name="payReceiveFlag" class="form-control">
                                        <option value="paid">Pay to Driver</option>
                                        {{--                                            <option value="received">Received from Driver</option>--}}
                                        {{--                                            <option value="bonus">Give Bonus to Driver</option>--}}
                                    </select>
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="text" id="amount" name="amount" required
                                           onkeypress="return isNumberKey(event)" class="form-control"
                                           placeholder="Enter Amount">
                                </div>
                            </div><!-- Col -->
                        </div>

                        <button type="button" class="btn btn-primary btn-user" id="recharge_button">
                            Submit
                        </button>

                    </form>
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

        $('#rechargeRecordBtn').click(function () {
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

