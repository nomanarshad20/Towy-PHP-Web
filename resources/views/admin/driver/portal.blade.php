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

{{--    //ridesSummary ,userInfo   --}}
    <div class="main-content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            @if(session()->has('success'))
                <div class="row">
                    <p class="alert alert-success">{{session('success')}}</p>
                </div>
            @endif
            @if(session()->has('error'))
                <div class="row">
                    <p class="alert alert-danger">{{session('error')}}</p>
                </div>
            @endif
            <div class="row">
                <div class="col-xl-5 col-sm-12">
                    <div class="card shadow">
                        <!-- Card Body -->
                        <div class="card-body" style="padding-bottom:0px !important;">
                            <div class="row">
                                <div class="col-xl-3 col-md-5  col-lg-5 mb-12" style="padding-right:0px !important;">
                                    @if(isset($userInfo->image) && $userInfo->image != null)
                                        <img src="{{asset($userInfo->image)}}" height="180" width="200"
                                             style="margin: 0 auto;  display: block; border: 1px solid; border-radius: 80px;" />
                                    @else
                                        <img src="{{asset('admin/img/admin.png')}}" height="180" width="200"
                                             style="margin: 0 auto;display: block;border: 0px solid;border-radius: 100px;" />
                                    @endif
                                    <br/>
                                    {{--<div style="width:100%;position:relative;font-weight:bold;display:block;"> Referrals :
                                        </div>
                                    <div style="width:100%;position:relative;font-weight:bold;display:block;"> Verified :
                                        </div>
                                    <div style="width:100%;position:relative;font-weight:bold;display:block;"> Unverified :
                                       </div>--}}
                                </div>
                                <div class="col-xl-4 col-md-7 col-lg-7 mb-12">
                                    <table>
                                        <tbody
                                            style="position: relative;font-weight:bold;display: block;margin:0px 20px 0px;left:25%;top:0%;">
                                        <tr>
                                            <td style="padding:5px 25px;">Name</td>
                                            <td>{{$userInfo->first_name.' '.$userInfo->last_name ?? ''}}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 25px;">Contact#</td>
                                            <td>{{$userInfo->mobile_no ?? ''}}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 25px;">User ID</td>
                                            <td>{{$userInfo->referral_code ?? ''}}</td>
                                        </tr>
{{--                                        <tr>--}}
{{--                                            <td style="padding:5px 25px;">Franchise</td>--}}
{{--                                            <td>{{$userInfo->driver->franchise->name ?? ''}}</td>--}}
{{--                                        </tr>--}}
                                        {{--<tr>
                                            <td style="padding:5px 25px;">Vehicle </td>
                                            <td>{{$userInfo->driver->vehicle->name ?? ''}}
                                                {{$userInfo->driver->vehicle->model ?? ''}}</td>
                                        </tr>--}}
                                        <tr>
                                            <td style="padding:5px 25px;">Vehicle#</td>
                                            <td>{{$userInfo->driver->vehicle->registration_number ?? ''}}-{{$userInfo->driver->vehicle->model_year ?? ''}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="padding-top:0px !important;">
                            {{--<div class="row" style="margin-bottom:10px;">
                                <div class="col-xl-6 col-md-6 mb-12">
                                    <a href="{{url('partnerRides/'.$userInfo->id.'/history')}}"
                                       class="btn btn-primary btn-user" style="width:100%;margin-top:8px;">Ride History</a>
                                </div>
                                <div class="col-xl-6 col-md-6 mb-12">
                                    <a href="{{url('partnerTransactions/'.$userInfo->id.'/history')}}"
                                       class="btn btn-primary btn-user" style="width:100%;margin-top:8px;">Transactions</a>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom:10px;">
                                <div class="col-xl-6 col-md-6 mb-12">
                                    <a href="{{url('partnerBonuses/'.$userInfo->id.'/history')}}"
                                       class="btn btn-primary btn-user" style="width:100%;margin-top:8px;">Bonus
                                        History</a>
                                </div>
                                <div class="col-xl-6 col-md-6 mb-12">
                                    <a href="#" class="btn btn-primary btn-user"
                                       style="width:100%;margin-top:8px;">Complaints</a>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom:10px;">
                                <div class="col-xl-6 col-md-6 mb-12">
                                    <a href="{{ url('partnerFineList/'.$userInfo->id.'/addFine') }}"
                                       class="btn btn-primary btn-user" style="width:100%;margin-top:8px;">Add Driver
                                        Fine</a>
                                </div>
                            </div>--}}
                            <div class="row" style="">
                                <form action="{{route('payReceiveFromDriver')}}" method="post" class="rechargeWalletForm"
                                      style="margin-bottom: 30px;width: 100%;">

                                    <input name="_token" type="hidden" value="{{ csrf_token() }}" />
                                    <input type="hidden" class="form-control" name="id" value="{{$userInfo->id}}">

                                    <div class="col-xl-12 col-md-12 mb-12">
                                        <select name="payReceiveFlag"
                                                style="width:100%;font-size:13px;height: 35px;border-radius: 4px;border:1px solid #f5b900 !important;">
                                            <option value="paid">Pay to Driver</option>
{{--                                            <option value="received">Received from Driver</option>--}}
{{--                                            <option value="bonus">Give Bonus to Driver</option>--}}
                                        </select>
                                    </div>
                                    <br>
                                    <div class="col-xl-12 col-md-12 mb-12">
                                        <input type="text" id="amount" name="amount" required
                                               onkeypress="return isNumberKey(event)"
                                               class="form-control"
                                               style="width:100%;font-size:13px;height: 35px;border-radius: 4px;border:1px solid #f5b900 !important;">
                                    </div>
                                    <br>
                                    <div class="col-xl-12 col-md-12 mb-12" style="float: right;">
                                        <div class="col-xl-6 col-md-6 mb-12" style="float: right;">
                                            <input type="button" class="btn btn-primary btn-user " value="SUBMIT"
                                                   id="recharge_button"
                                                   style="background:#f5b900 !important;float: right;border:1px solid #f5b900 !important;">
                                        </div>
                                    </div>
                                </form>
                                <!--<div class="col-xl-6 col-md-6 mb-12">
                                        <a href="#" class="btn btn-primary btn-user" style="width:100%;margin-top:8px;">Complaint Management</a>
                                    </div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Area Chart -->
                <div class="col-xl-7 col-lg-7 col-sm-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-body" style="padding-top:0px; !important;">
                                    <form id="form1" method="get" style="margin-bottom: 30px;">
{{--                                        @csrf--}}
                                    <input type="text" id="dateFrom" name="fromDate" class="datepicker"
                                           style="width: 200px;height: 35px;border-radius: 4px;border:1px solid #f5b900 !important;"
                                           value="{{$_GET['fromDate']??date('Y-m-d')}}"
{{--                                           value="{{$_POST['fromDate']??date('Y-m-d')}}"--}}
                                    >
                                    <input type="hidden" class="form-control" name="driverID" value="{{$userInfo->id}}">
                                    <input type="text" id="dateTill" name="tillDate" class="datepicker"
                                           max="{{date("Y-m-d", strtotime('today'))}}"
                                           style="width: 200px;height: 35px;border-radius: 4px;border:1px solid #f5b900 !important;"
{{--                                           value="{{$_POST['tillDate']??date('Y-m-d')}}"--}}
                                           value="{{$_GET['tillDate']??date('Y-m-d')}}"
                                    >
                                        <input type="submit" class="btn btn-primary btn-user" value="SEARCH" id="searchReportFrm" style="background:#f5b900 !important;border:1px solid #f5b900 !important;">
{{--                                    <button class="btn btn-primary btn-user" onclick="getPartnerRidesCalculationsHistory()"--}}
{{--                                            style="background:#f5b900 !important;border:1px solid #f5b900 !important;">SEARCH</button>--}}
                                    </form>
                                </div>
                                <!--<form action="" style="margin-bottom: 30px;">
                                        <input type="date" id="date" name="dateone" style="width: 165px;font-size:13px;height: 35px;border-radius: 4px;border:1px solid #f5b900 !important;">
                                        <input type="date" id="date" name="datetwo"  style="width: 165px;font-size:13px;height: 35px;border-radius: 4px;border:1px solid #f5b900 !important;">
                                        <input type="submit" class="btn btn-primary btn-user " style="background:#f5b900 !important;border:1px solid #f5b900 !important;">
                                    </form>-->
                            </div>
                            <div class="row" id="pcontent">
                                <div class="col-xl-6 col-lg-6 col-sm-12" style="">
                                    <table>
                                        <tr style="padding-bottom:7px;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;">
                                                {{$ridesSummary['totalCompletedRides'] ?? 0}} X Cash Trips</td>
                                            <td style="font-size:13px;">Rs.
                                                {{number_format($ridesSummary['totalDriverCashEarnings'],2) ?? 0}}</td>
{{--                                            totalDriverCreditAmount--}}
                                        </tr>


                                        <tr style="padding-bottom:7px;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;">Credit Trips</td>
                                            <td style="font-size:13px;">Rs.
                                                {{number_format($ridesSummary['totalDriverWalletAmount'],2) ?? 0}}</td>
                                        </tr>
                                        <tr style="color:green;padding-bottom:7px;position: relative;display:block;">
                                            <td style="width: 165px;font-size:13px;">Bonus</td>
                                            <td style="font-size:13px;">Rs.
                                                {{number_format($ridesSummary['driverTotalBonus'],2) ?? 0}}</td>
                                        </tr>
                                        <tr style="color:green;padding-bottom:7px;position: relative;display:block;">
                                            <td style="width: 165px;font-size:13px;color:green;">
                                                {{$ridesSummary['totalPassengerCancelRides'] ?? 0}} X Passenger Cancel</td>
                                            <td style="font-size:13px;">Rs.
                                                {{number_format($ridesSummary['totalPassengerCancelPenalty'],2) ?? 0}}</td>
                                        </tr>

                                        <tr style="padding-bottom: 7px;color:red;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;color:red;">
                                                {{$ridesSummary['totalDriverCancelRides'] ?? 0}} X Partner Cancel</td>
                                            <td style="font-size:13px;">Rs.
                                                {{number_format($ridesSummary['totalDriverCancelPenalty'],2) ?? 0}}</td>
                                        </tr>

                                        <tr style="color:red;padding-bottom:7px;position: relative;display:block;">
                                            <td style="width: 165px;font-size:13px;color:red;">Tax</td>
                                            <td style="font-size:13px;">Rs.
                                                {{number_format($ridesSummary['totalTaxAmount'],2) ?? 0}}</td>
                                        </tr>

                                        <tr
                                            style="padding-bottom: 5px;position: relative;display: block;border:1px solid;padding:4px;margin-top:15px;">
                                            <td style="width: 165px;font-size:13px;">Total</td>
                                            @if($ridesSummary['newSum'] < 0) <td style="color:red; font-size:13px;">Rs.
                                                {{number_format($ridesSummary['newSum'],2) ?? 0}}</td>
                                            @else
                                                <td style="color:green; font-size:13px;">Rs.
                                                    {{number_format($ridesSummary['newSum'],2) ?? 0}}</td>
                                            @endif
                                        </tr>

                                        <tr style="padding-bottom: 7px;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;">Cash collected</td>
                                            <td style="font-size:13px;font-size:13px;">Rs.
                                                {{number_format($ridesSummary['totalCashCollectedByDriver'],2) ?? 0}}</td>
                                        </tr>


                                        <tr style="padding-bottom:7px;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;">Current Remainings</td>
                                            @if($ridesSummary['remainings'] < 0) <td style="color:red; font-size:13px;">Rs.
                                                {{number_format($ridesSummary['remainings'],2) ?? 0}}</td>
                                            @else
                                                <td style="color:green; font-size:13px;">Rs.
                                                    {{number_format($ridesSummary['remainings'],2) ?? 0}}</td>
                                            @endif
                                        </tr>

                                        <tr style="padding-bottom:7px;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;">Previous Cycle Adjustment</td>
                                            @if($ridesSummary['previous_total_amount'] < 0) <td
                                                style="color:red; font-size:13px;">Rs.
                                                {{number_format($ridesSummary['previous_total_amount'],2) ?? 0}}</td>
                                            @else
                                                <td style="color:green; font-size:13px;">Rs.
                                                    {{number_format($ridesSummary['previous_total_amount'],2) ?? 0}}</td>
                                            @endif
                                        </tr>

                                        <tr
                                            style="padding-bottom: 5px;position: relative;display: block;border:1px solid;padding:4px;margin-top:15px;">
                                            <td style="width: 165px;font-size:13px;">Payable/Receivable</td>
                                            @if($ridesSummary['final_total_amount'] < 0) <td
                                                style="color:red; font-size:13px;"><b>Rs.
                                                    {{number_format($ridesSummary['final_total_amount'],2) ?? 0}}</b></td>
                                            @else
                                                <td style="color:green; font-size:13px;"><b>Rs.
                                                        {{number_format($ridesSummary['final_total_amount'],2) ?? 0}}</b>
                                                </td>
                                            @endif
                                        </tr>
                                        <tr
                                            style="padding-bottom:7px;position: relative;display:block;border-top: 1px solid;padding-top: 0px;margin-top: 10px;">
                                            <td style="width: 165px;font-size:13px;">Cash Received From Towy</td>
                                            <td style="color:green;font-size:13px;">Rs.
                                                {{number_format($ridesSummary['amountPaidToDriver'],2) ?? 0}}</td>
                                        </tr>
                                        <tr style="padding-bottom: 7px;position: relative;display: block;">
                                            <td style="width: 165px;font-size:13px;">Cash Paid To Towy</td>
                                            <td style="color:red;font-size:13px;font-size:13px;">Rs.
                                                {{number_format($ridesSummary['amountReceivedFromDriver'],2) ?? 0}}</td>
                                        </tr>

                                    </table>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-sm-12" style="">
                                    <div class="card-body" style="padding-top:0px; !important;">
                                        <div class="row" style="margin-bottom:5px;">
                                            <!-- Earnings (Monthly) Card Example -->
                                            <div class="col-xl-12 col-md-12 mb-12">
                                                <div class="card border-left-primary shadow h-100 py-2">
                                                    <div class="card-body" style="padding:0.1rem 0.1rem !important;">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div
                                                                    class="text-center text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                                    Driver Ratings : {{number_format($ridesSummary['ratingsAvg'],1) ?? 0}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--<div class="row" style="margin-bottom:5px;">
                                            <!-- Earnings (Monthly) Card Example -->
                                            <div class="col-xl-12 col-md-12 mb-12">
                                                <div class="card border-left-success shadow h-100 py-2">
                                                    <div class="card-body" style="padding:0.1rem 0.1rem !important;">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div
                                                                    class="text-center text-xs font-weight-bold text-success text-uppercase mb-1">
                                                                    Available Hours : {{ $ridesSummary['totalLoginHours']}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>--}}

                                        <div class="row" style="margin-bottom:5px;">
                                            <!-- Earnings (Monthly) Card Example -->
                                            <div class="col-xl-12 col-md-12 mb-12">
                                                <div class="card border-left-info shadow h-100 py-2">
                                                    <div class="card-body" style="padding:0.1rem 0.1rem !important;">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div
                                                                    class="text-center text-xs font-weight-bold text-info text-uppercase mb-1">
                                                                    Acceptance Rate :
                                                                    {{number_format($ridesSummary['acceptRidesPercent'],2) ?? 0}}%
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Pending Requests Card Example -->
                                            <div class="col-xl-12 col-md-12 mb-12">
                                                <div class="card border-left-warning shadow h-100 py-2">
                                                    <div class="card-body" style="padding:0.1rem 0.1rem; !important;">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div
                                                                    class="text-center text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                                    Ride Completion :
                                                                    {{number_format($ridesSummary['completeRidesPercent'],2) ?? 0}}%</div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Pending Requests Card Example -->
                                            <div class="col-xl-12 col-md-12 mb-12">
                                                <div class="card border-left-warning shadow h-100 py-2">
                                                    <div class="card-body" style="padding:0.1rem 0.1rem; !important;">
                                                        <div class=" no-gutters align-items-center">
                                                            <div class="col mr-2" style="padding-left: 3px;">
                                                                <div class="text-left text-xs font-weight-bold text-info text-uppercase mb-1">
                                                                    Rides Received :
                                                                    {{$ridesSummary['totalReceivedRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-success text-uppercase mb-1">
                                                                    Rides Accepted :
                                                                    {{$ridesSummary['totalAcceptRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-success text-uppercase mb-1">
                                                                    Rides Completed :
                                                                    {{$ridesSummary['totalCompletedRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-success text-uppercase mb-1">
                                                                    Partner Cancelled :
                                                                    {{$ridesSummary['totalDriverCancelRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                                    Passenger Cancelled :
                                                                    {{$ridesSummary['totalPassengerCancelRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                                    Rides Ignored :
                                                                    {{$ridesSummary['totalIgnoreRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                                    Rides Rejected :
                                                                    {{$ridesSummary['totalRejectRides'] ?? 0}}</div>
                                                                <div class="text-left text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                                    Admin Cancelled :
                                                                    {{$ridesSummary['totalSystemCancelRides'] ?? 0}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!--
                                    <div class="row">

                                        <div class="col-xl-12 col-md-12 mb-12">
                                            <div class="card border-left-warning shadow h-100 py-2">
                                                <div class="card-body" style="padding:0.1rem 0.1rem; !important;">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div
                                                                class="text-center text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                                <a href="{{url('driver_rides/'.$userInfo->id)}}"> View
                                                                    Rejected Rides</a></div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									-->
                                    </div>
                                </div>
                                <!-- Close Div-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

{{--    <script>--}}
{{--        $.ajaxSetup({--}}
{{--            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }--}}
{{--        });--}}

{{--        function getPartnerRidesCalculationsHistory()--}}
{{--        {--}}
{{--            //preventDefault();--}}
{{--            var rprtFrom    = $('input[name=reportFrom]').val();--}}
{{--            var rprtTill    = $('input[name=reportTill]').val();--}}
{{--            var rprtId      = $('input[name=driverID]').val();--}}
{{--            //alert(rprtFrom);--}}
{{--            if (rprtFrom == '' || rprtTill == '') {--}}
{{--                alert('Please select both dates before searching');--}}
{{--                return false;--}}
{{--            }--}}
{{--            if (new Date(rprtFrom) > new Date(rprtTill)) {--}}
{{--                alert('Please select an appropriate date range');--}}
{{--                return false;--}}
{{--            }--}}
{{--            jQuery.ajax({--}}
{{--                type: "post",--}}
{{--                url: '{{{URL::to("")}}}/ridesCalculationsHistoryAjax',--}}
{{--                data: {id: rprtId,--}}
{{--                    fromDate: rprtFrom,--}}
{{--                    tillDate: rprtTill,--}}
{{--                    "_token": "{{ csrf_token() }}"--}}
{{--                },--}}
{{--                //	cache: false,--}}
{{--                success: function (response) {--}}
{{--                    console.log(response);--}}
{{--                    $("#pcontent").html("");--}}
{{--                    $("#pcontent").html(response);--}}
{{--                    //	$( "#ajaxval p" ).append(response);--}}
{{--                }, error: function (response) {--}}
{{--                    console.log(response);--}}
{{--                    alert("error!!!! " + response);--}}
{{--                }--}}
{{--            });--}}
{{--        }--}}


{{--    </script>--}}





    {{--<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            <a href="#" class="noble-ui-logo d-block mt-3">Driver Portal</a>
                            <p class="mt-1 mb-1"><b>Reference ID: {{$userInfo->referral_code}} </b></p>
                            <p><strong>Name: {{$userInfo->name}}</strong> </p>
                            <p><strong>Email: {{$userInfo->email}}</strong> </p>
                            <p><strong>Phone: {{$userInfo->mobile_no}}</strong> </p>

                        </div>
                        <div class="col-lg-3 pe-0">
                            <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">receipt</h4>
                            <h6 class="text-end mb-5 pb-4"># </h6>
                            <p class="text-end mb-1">Ride Actual Fare</p>
                            <h4 class="text-end fw-normal"></h4>


                            <h6 class="mb-0 mt-3 text-end fw-normal mb-2">
                                <span class="text-muted">Booking Type :</span>
                            </h6>

                            <h6 class="mb-0 mt-3 text-end fw-normal mb-2"><span
                                    class="text-muted">Total Distance :</span>

                            </h6>

                        </div>
                    </div>



                    <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th><h4>Total Ride Amount:</h4></th>
                                    <th><h4>{{$ridesSummary['totalRideActualAmount'] ?? 0 }}</h4></th>
                                    <th><h4>Driver Total Cash Collected:</h4></th>
                                    <th><h4>{{$ridesSummary['totalCashCollectedByDriver']}}</h4></th>
                                </tr>

                                <tr>
                                    <th>Driver Earnings</th>
                                    <th>Amount</th>
                                    <th>Driver Paid</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr class="text-end">
                                    <td class="text-start">Total Driver Cash Amount : </td>
                                    <td class="text-start" style="color:green;"> {{abs($ridesSummary['totalRideActualAmount']) + $ridesSummary['driverCurrentBalance']}}</td>
                                    <td class="text-start">Total Franchise Amount : </td>
                                    <td class="text-start" style="color:red;">{{$ridesSummary['driverCurrentBalance'] - $ridesSummary['totalTaxAmount']}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start">Wallet Credit Amount:</td>
                                    <td class="text-start" style="color:green;">{{$ridesSummary['totalPassengerWalletPaid'] ?? 0}}</td>
                                    <td class="text-start">Total Tax Amount:</td>
                                    <td class="text-start" style="color:red;">{{$ridesSummary['totalTaxAmount'] ?? 0}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start">Total Bonus Amount:</td>
                                    <td class="text-start" style="color:green;"> 0 </td>

                                    <td class="text-start">Passenger Paid Extra Amount:</td>
                                    <td class="text-start" style="color:red;">{{$ridesSummary['totalPassengerPaidExtraAmount'] ?? 0}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start">Passenger Penalty:</td>
                                    <td class="text-start" style="color:green;">0</td>

                                    <td class="text-start">Driver Penalty:</td>
                                    <td class="text-start" style="color:red;">{{$ridesSummary['totalDriverCancelPenalty'] ?? 0}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start"><h5 class="mt-5 mb-2 text-muted">Total Amount Earning:</h5></td>
                                    <td class="text-start"><h5 class="mt-5 mb-2 text-muted">{{$ridesSummary['totalDriverCreditAmount'] + $ridesSummary['totalPassengerWalletPaid'] }}</h5></td>
                                    <td class="text-start"><h5 class="mt-5 mb-2 text-muted">Total Amount Paid:</h5></td>
                                    <td class="text-start"><h5 class="mt-5 mb-2 text-muted">{{$ridesSummary['totalPassengerPaidExtraAmount'] + $ridesSummary['totalDriverCancelPenalty'] + $ridesSummary['totalTaxAmount']}}</h5></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="container-fluid mt-5 w-100">
                        <div class="row">
                            <div class="col-md-6 ms-auto">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Sub Total</td>
                                                <td class="text-end">
                                                    {{$ridesSummary['driverCurrentBalance'] ?? 0}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Previous Amount</td>
                                                <td class="text-end">{{$ridesSummary['previous_total_amount'] ?? 0}}</td>

                                            </tr>
                                            <tr>
                                                <td>Franchise Paid To Driver </td>
                                                <td class="text-end"> {{$ridesSummary['amountPaidToDriver'] ?? 0}} </td>

                                            </tr>
                                            <tr>
                                                <td>Franchise Received from Driver</td>
                                                <td class="text-end">{{$ridesSummary['amountReceivedFromDriver'] ?? 0 }}</td>
                                            </tr>

                                            <tr>
                                                <td class="text-bold-800">Total</td>
                                                <td class="text-bold-800 text-end"></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}

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


