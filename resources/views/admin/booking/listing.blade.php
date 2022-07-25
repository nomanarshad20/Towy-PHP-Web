@extends('layouts.admin.index')

@section('title')
    Booking Listing
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/dataTables.bootstrap4.css')}}">

    <style>

        .blockUI {
            z-index: 1060 !important;
        }

    </style>

@endsection


@section('body')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Booking Listing</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking Unique ID</th>
                                <th>Passenger</th>
                                <th>Driver</th>
                                {{--                                <th>Vehicle Type</th>--}}
                                <th>Franchise ID</th>
                                <th>Booking Type</th>
                                <th>Pick Up Area</th>
                                <th>Drop Off Area</th>
                                <th>Ride Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $booking)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        @if($booking->ride_status != 0  && $booking->ride_status != 1)
                                            <a href="{{route('bookingDetail',['id'=>$booking->id])}}">
                                                {{$booking->booking_unique_id}}
                                            </a>
                                        @else
                                            <a href="javascript:void(0)">
                                                {{$booking->booking_unique_id}}
                                            </a>
                                        @endif

                                    </td>
                                    <td>{{isset($booking->passenger) ? $booking->passenger->name:'N/A'}}</td>
                                    <td>{{isset($booking->driver) ? $booking->driver->name:'N/A'}}</td>
                                    {{--                                    <td>{{$booking->vehicleType->name}}</td>--}}
                                    <td>{{isset($booking->franchise) ? $booking->franchise->name:'N/A'}}</td>
                                    <td>{{$booking->booking_type}}</td>
                                    <td>{{$booking->pick_up_area}}</td>
                                    <td>{{$booking->drop_off_area}}</td>

                                    <td>
                                        @if($booking->ride_status == 0)
                                            Request For Ride
                                        @elseif($booking->ride_status == 1)
                                            Ride Accepted
                                        @elseif($booking->ride_status == 2)
                                            Passenger Cancel
                                        @elseif($booking->ride_status == 3)
                                            Admin Cancel
                                        @elseif($booking->ride_status == 4)
                                            Completed
                                        @elseif($booking->ride_status == 5)
                                            Driver Cancel
                                        @endif
                                    </td>


                                    <td>
                                        <a title="Edit" href="{{route('bookingEdit',['id'=>$booking->id])}}">
                                            <i data-feather="edit">Edit</i>
                                        </a>
                                        <a data-id="{{$booking->id}}" title="Delete" class="deleteRecord"
                                           href="javascript:void(0)">
                                            <i data-feather="trash">Delete</i>
                                        </a>
                                        {{--                                        <a href="javascript:void(0)" data-id="{{$vehicle->id}}" title="Change Status"--}}
                                        {{--                                           class="changeStatus">--}}
                                        {{--                                            <i data-feather="refresh-ccw">Change Status</i>--}}
                                        {{--                                        </a>--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.booking.modal.delete_modal')
@endsection


@section('script')
    <!-- Plugin js for this page -->
    <script src="{{asset('admin/js/jquery.dataTables.js')}}"></script>
    <script src="{{asset('admin/js/dataTables.bootstrap4.js')}}"></script>
    <!-- End plugin js for this page -->

    <!-- Custom js for this page -->
    <script src="{{asset('admin/js/data-table.js')}}"></script>
    <!-- End custom js for this page -->

    <script>
        $(document).ready(function () {

            $(document).on('click', '.deleteRecord', function () {

                var data = $(this).data('id');

                var html = $('#deleteRecordId').val(data);

                $('#delete_form').append(html);

                $('#delete_modal').modal('show');

            });

            $('#deleteRecordBtn').click(function () {

                var data = $('#delete_form').serialize();

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
                    url: '{{route("bookingDelete")}}',
                    data: data,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.reload();
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

            {{--$('.changeStatus').click(function () {--}}

            {{--    var data = $(this).data('id');--}}

            {{--    $.blockUI({--}}
            {{--        css: {--}}
            {{--            border: 'none',--}}
            {{--            padding: '15px',--}}
            {{--            backgroundColor: '#000',--}}
            {{--            '-webkit-border-radius': '10px',--}}
            {{--            '-moz-border-radius': '10px',--}}
            {{--            opacity: .5,--}}
            {{--            color: '#fff'--}}
            {{--        }--}}
            {{--    });--}}
            {{--    $.ajax({--}}

            {{--        type: 'GET',--}}
            {{--        url: '{{route("vehicleChangeStatus")}}',--}}
            {{--        data: {'id': data},--}}

            {{--        success: function (response, status) {--}}

            {{--            if (response.result == 'success') {--}}
            {{--                $.unblockUI();--}}
            {{--                successMsg(response.message);--}}

            {{--                setTimeout(function () {--}}
            {{--                    window.location.reload();--}}
            {{--                }, 1000);--}}

            {{--            } else if (response.result == 'error') {--}}
            {{--                $.unblockUI();--}}
            {{--                errorMsg(response.message);--}}
            {{--            }--}}
            {{--        },--}}
            {{--        error: function (data) {--}}
            {{--            $.each(data.responseJSON.errors, function (key, value) {--}}
            {{--                $.unblockUI();--}}
            {{--                errorMsg(value);--}}
            {{--            });--}}
            {{--        }--}}


            {{--    });--}}
            {{--});--}}

        });
    </script>
@endsection
