@extends('layouts.admin.index')

@section('title')
    Booking Detail
@endsection

@section('style')


@endsection


@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Booking Detail</h4>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            <h4 class="fw-bolder text-uppercase  mt-4 mb-2">Ride Status:</h4>
                            <h6 class="mb-5 pb-4">
                                @if($data->ride_status == 0)
                                    Ride Request Send
                                @elseif($data->ride_status == 1)
                                    Ride Request Accepted
                                @elseif($data->ride_status == 2)
                                    Passenger Cancel Ride Request
                                @elseif($data->ride_status == 3)
                                    Admin Cancel Ride Request
                                @elseif($data->ride_status == 4)
                                    Ride Completed
                                @elseif($data->ride_status == 5)
                                    Driver Cancel Ride Request
                                @endif
                            </h6>
                        </div>
                        @if($data->ride_status == 2 || $data->ride_status == 5)
                            <div class="col-lg-3 pe-0">
                                <h4 class="fw-bolder text-uppercase text-end  mt-4 mb-2">Cancel Reason:</h4>
                                <h6 class="mb-5 pb-4 text-end ">
                                    @if($data->other_cancel_reason)
                                        {{$data->other_cancel_reason}}
                                    @else
                                        {{$data->cancel->reason}}
                                    @endif
                                </h6>
                            </div>
                        @endif
                    </div>

                    <div class="container-fluid d-flex justify-content-between">
                        <div class="col-lg-3 ps-0">
                            <a href="#" class="noble-ui-logo d-block mt-3">Booking</a>
                            <p class="mt-1 mb-1"><b>Location:</b></p>
                            <p><strong>From:</strong> {{$data->pick_up_area}}</p>
                            <p><strong>To:</strong> {{$data->drop_off_area}}</p>

                            <h5 class="mt-5 mb-2 text-muted">Passenger :</h5>
                            <p>{{$data->passenger->first_name.' '.$data->passenger->last_name}}</p>

                            <h5 class="mt-5 mb-2 text-muted">Driver :</h5>
                            <p>{{$data->driver->first_name.' '.$data->driver->last_name}}</p>

                        </div>
                        <div class="col-lg-3 pe-0">
                            <h4 class="fw-bolder text-uppercase text-end mt-4 mb-2">receipt</h4>
                            <h6 class="text-end mb-5 pb-4"># {{$data->booking_unique_id}}</h6>
                            <p class="text-end mb-1">Ride Actual Fare</p>
                            <h4 class="text-end fw-normal">{{$data->actual_fare}}</h4>


                            <h6 class="mb-0 mt-3 text-end fw-normal mb-2">
                                <span class="text-muted">Booking Type :</span>
                                {{str_replace('_',' ',ucfirst($data->booking_type))}}
                            </h6>

                            @if($data->booking_type == 'book_now')
                                <h6 class="mb-0 mt-3 text-end fw-normal mb-2"><span
                                        class="text-muted">Ride Date :</span>
                                    {{\Carbon\Carbon::parse($data->created_at)->format('d M Y')}}</h6>
                            @else
                                <h6 class="mb-0 mt-3 text-end fw-normal mb-2"><span
                                        class="text-muted">Ride Date :</span>
                                    {{\Carbon\Carbon::parse($data->pick_up_date)->format('d M Y')}}
                                    {{\Carbon\Carbon::parse($data->pick_up_time)->format('H:i:s')}}
                                </h6>
                            @endif

                            <h6 class="mb-0 mt-3 text-end fw-normal mb-2"><span
                                    class="text-muted">Total Distance In Km:</span>
                                {{$data->total_calculated_distance ? $data->total_calculated_distance:0}}
                            </h6>

                        </div>
                    </div>
                    <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Cost</th>
                                    <th>Total Distance/Time</th>
                                    <th class="text-end">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="text-end">
                                    <td class="text-start">1</td>
                                    <td class="text-start">Per Km Rate</td>
                                    <td class="text-start">{{$data->bookingDetail->vehicle_per_km_rate}}</td>
                                    <td class="text-start">{{$data->total_calculated_distance ? $data->bookingDetail->mobile_final_distance :0}}</td>
                                    <td>{{ $data->bookingDetail->vehicle_per_km_rate * $data->bookingDetail->mobile_final_distance}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start">2</td>
                                    <td class="text-start">Per Min Rate</td>
                                    <td class="text-start">{{$data->bookingDetail->vehicle_per_min_rate}}</td>
                                    <td class="text-start">{{$data->bookingDetail->total_ride_minutes ? $data->bookingDetail->total_ride_minutes:0}}</td>
                                    <td>{{$data->bookingDetail->vehicle_per_min_rate * $data->bookingDetail->total_ride_minutes}}</td>
                                </tr>


                                <tr class="text-end">
                                    <td class="text-start">3</td>
                                    <td class="text-start">Pick Up KM Rate</td>
                                    <td class="text-start">{{$data->bookingDetail->initial_distance_rate ? $data->bookingDetail->initial_distance_rate:0}}</td>
                                    <td class="text-start">{{$data->bookingDetail->mobile_initial_distance ? $data->bookingDetail->mobile_initial_distance:0}}</td>
                                    <td>{{$data->bookingDetail->initial_distance_rate * $data->bookingDetail->mobile_initial_distance}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start">4</td>
                                    <td class="text-start">Pick Up Min Rate</td>
                                    <td class="text-start">{{$data->bookingDetail->initial_time_rate ? $data->bookingDetail->initial_time_rate:0}}</td>
                                    <td class="text-start">{{$data->bookingDetail->initial_time_rate ? $data->bookingDetail->total_minutes_to_reach_pick_up_point:0}}</td>
                                    <td>{{$data->bookingDetail->initial_time_rate * $data->bookingDetail->total_minutes_to_reach_pick_up_point}}</td>
                                </tr>

                                <tr class="text-end">
                                    <td class="text-start">5</td>
                                    <td class="text-start">Waiting Time</td>
                                    <td class="text-start">{{$data->bookingDetail->waiting_price_per_min ? $data->bookingDetail->waiting_price_per_min:0}}</td>
                                    <td class="text-start">{{$data->bookingDetail->driver_waiting_time ? $data->bookingDetail->driver_waiting_time:0}}</td>
                                    <td>{{$data->bookingDetail->waiting_price_per_min * $data->bookingDetail->driver_waiting_time}}</td>
                                </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>

                    @php
                        if($data->ride_status !=2 && $data->ride_status !=3)
                        {
                            $sum =  ($data->bookingDetail->vehicle_per_km_rate * $data->bookingDetail->mobile_final_distance) +
                                    ($data->bookingDetail->vehicle_per_min_rate * $data->bookingDetail->total_ride_minutes) +
                                    ($data->bookingDetail->initial_distance_rate * $data->bookingDetail->mobile_initial_distance) +
                                    ($data->bookingDetail->initial_time_rate * $data->bookingDetail->total_minutes_to_reach_pick_up_point) +
                                    ($data->bookingDetail->waiting_price_per_min * $data->bookingDetail->driver_waiting_time);
                        }
                        else{
                            $sum = $data->fine_amount;
                        }
                    $sum =  $sum + $data->bookingDetail->min_vehicle_fare;

                    $totalTaxAmount = ($data->bookingDetail->vehicle_tax * $sum )/100;

                    $discountedAmount = 0;

                    if($data->bookingDetail->is_voucher == 1)
                    {
                        $voucherDetail = json_decode($data->bookingDetail->voucher_detail);


                         if(\Carbon\Carbon::parse($voucherDetail->expiry_date) > \Carbon\Carbon::now())
                        {
                            $discountedAmount = (($sum + $totalTaxAmount)*$voucherDetail->discount_value)/100;
                        }


                    }



                    @endphp
                    <div class="container-fluid mt-5 w-100">
                        <div class="row">
                            <div class="col-md-6 ms-auto">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td>Sub Total</td>
                                            <td class="text-end">
                                                {{
                                                   $sum
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fine</td>
                                            <td class="text-end">{{$data->fine_amount}}</td>

                                        </tr>

                                        <tr>
                                            <td>TAX ( {{$data->bookingDetail->vehicle_tax}} % )</td>
                                            <td class="text-end">{{ $totalTaxAmount }}</td>
                                        </tr>

                                        @if($data->bookingDetail->is_voucher == 1)
                                            <tr>
                                                <td>Voucher ( {{$voucherDetail->discount_value}} {{$voucherDetail->discount_type == 'percentage' ? '%': $data->bookingDetail->voucher_detail['discount_type']}} )</td>
                                                <td class="text-end">{{ $discountedAmount }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>Voucher ( 0 % )</td>
                                                <td class="text-end"> 0 </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-bold-800">Total</td>
                                            <td class="text-bold-800 text-end"> {{$sum + $totalTaxAmount + $discountedAmount}}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Mode</td>
                                            <td class="text-danger text-end">{{$data->payment_type}}</td>
                                        </tr>
                                        @if($data->payment_type == 'cash')
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Balance Paid</td>
                                                <td class="text-bold-800 text-end">{{$data->bookingDetail->passenger_total_cash_paid}}</td>
                                            </tr>

                                            <tr class="bg-light">
                                                <td class="text-bold-800">Extra Amount Paid For Wallet</td>
                                                <td class="text-bold-800 text-end">{{$data->bookingDetail->passenger_extra_cash_paid}}</td>
                                            </tr>
                                        @elseif($data->payment_type == 'wallet')
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Balance Paid From Wallet</td>
                                                <td class="text-bold-800 text-end">{{$data->bookingDetail->passenger_wallet_paid}}</td>
                                            </tr>
                                        @elseif($data->payment_type == 'cash_wallet')
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Balance Paid From Wallet</td>
                                                <td class="text-bold-800 text-end">{{$data->bookingDetail->passenger_wallet_paid}}</td>
                                            </tr>

                                            <tr class="bg-light">
                                                <td class="text-bold-800">Balance Paid</td>
                                                <td class="text-bold-800 text-end">{{$data->bookingDetail->passenger_total_cash_paid}}</td>
                                            </tr>

                                            <tr class="bg-light">
                                                <td class="text-bold-800">Extra Amount Paid For Wallet</td>
                                                <td class="text-bold-800 text-end">{{$data->bookingDetail->passenger_extra_cash_paid}}</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--                    <div class="container-fluid w-100">--}}
                    {{--                        <a href="javascript:;" class="btn btn-outline-primary float-end mt-4">--}}
                    {{--                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer me-2 icon-md"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>--}}
                    {{--                            Print--}}
                    {{--                        </a>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>

@endsection

