@extends('layouts.admin.index')

@section('title')
    Booking Create
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery-ui.css')}}" >
    <link href="{{asset('admin/css/mdtimepicker.css')}}" rel="stylesheet">

@endsection




@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Booking Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Booking Information</h6>
                    <form id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Passenger</label>
                                    <select name="passenger_id" class="form-control">
                                        <option value="" disabled selected>Select</option>
                                        @foreach($passengers as $passenger)
                                            <option value="{{$passenger->id}}">{{$passenger->name .' ( '.$passenger->referral_code .' )'}}</option>
                                        @endforeach

                                    </select>

                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Driver</label>
                                    <select name="driver_id" class="form-control">
                                        <option value="" disabled selected>Select</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{$driver->id}}">{{$driver->name .' ( '.$driver->referral_code .' )'}}</option>
                                        @endforeach

                                    </select>

                                </div>
                            </div><!-- Col -->



{{--                            <div class="col-sm-6">--}}
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label">Vehicle Type</label>--}}
{{--                                    <select class="form-control" name="vehicle_type_id">--}}
{{--                                        <option value="" selected disabled>Select</option>--}}
{{--                                        @foreach($vehicleTypes as $vehicleType)--}}
{{--                                            <option value="{{$vehicleType->id}}">{{$vehicleType->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}


{{--                                </div>--}}
{{--                            </div><!-- Col -->--}}

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pick Up Area</label>
                                    <input type="text" class="form-control locationSearch"
                                           id="locationSearch"
                                           name="pick_up_area">
                                    <input type="hidden" id="lat" name="pick_up_lat" value="">
                                    <input type="hidden" id="lng" name="pick_up_lng" value="">

                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Drop Off Area</label>
                                    <input type="text" class="form-control drop_off_area"
                                           id="dropOffLocationSearch"
                                           name="drop_off_area">
                                    <input type="hidden" id="drop_off_lat" name="drop_off_lat" value="">
                                    <input type="hidden" id="drop_off_lng" name="drop_off_lng" value="">
                                </div>
                            </div><!-- Col -->



                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Booking Type</label>
                                    <select class="form-control bookingType" name="booking_type">
                                        <option value="book_now">Book Now</option>
                                        <option value="book_later">Book Later</option>
                                    </select>
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <div class="row bookingLaterFields" style="display: none">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pick Up Date</label>
                                    <input type="text" name="pick_up_date" class="form-control datepicker">
                                </div>
                            </div><!-- Col -->


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pick Up Time</label>
                                    <input type="text" name="pick_up_time" class="form-control bs-timepicker">
                                </div>
                            </div><!-- Col -->

                        </div>

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

    <script src="{{asset('admin/js/mdtimepicker.js')}}"></script>


    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP')}}&js?sensor=false&libraries=places&extn=.js"></script>



    @include('admin.franchise.script.autocomplete_script')


    <script>
        $(document).ready(function () {

            $( ".datepicker" ).datepicker({
                minDate: 0,
            });

            $('.bs-timepicker').mdtimepicker({

                timeFormat: 'hh:mm:ss.000',

                theme: 'blue',

                readOnly: true,

                clearBtn: false,


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
                    url: '{{route("bookingSave")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('bookingListing')}}';
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

            $('.bookingType').change(function(){
                var data = $('.bookingType').val();

                if(data == 'book_now')
                {
                    $('.bookingLaterFields').css('display','none');
                    $('.datepicker').val('');
                    $('.bs-timepicker').val('');
                }
                else if(data == 'book_later')
                {
                    $('.bookingLaterFields').removeAttr('style');
                }
            });


        });
    </script>
@endsection
