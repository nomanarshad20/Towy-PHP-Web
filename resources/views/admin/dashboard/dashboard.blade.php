@extends('layouts.admin.index')

@section('title')
    Dashboard
@endsection

@section('style')
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
        }
    </style>
@endsection


@section('body')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                @foreach($users as $user)
                    @if($user['user_type'])
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        @if($user['user_type'] == 1)
                                            <h6 class="card-title mb-0">Total Passengers</h6>
                                        @elseif($user['user_type'] == 2)
                                            <h6 class="card-title mb-0">Total Driver</h6>
                                        @elseif($user['user_type'] == 3)
                                            <h6 class="card-title mb-0">Total Franchise</h6>
                                        @endif
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="icon-lg text-muted pb-3px"
                                                   data-feather="more-horizontal"></i>
                                            </button>
                                            @if($user['user_type'] == 1)
                                                {{--                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">--}}
                                                {{--                                                    <a class="dropdown-item d-flex align-items-center"--}}
                                                {{--                                                       href="{{route('driverListing')}}">--}}
                                                {{--                                                        <i data-feather="eye" class="icon-sm me-2"></i>--}}
                                                {{--                                                        <span class="">View</span>--}}
                                                {{--                                                    </a>--}}

                                                {{--                                                </div>--}}
                                            @elseif($user['user_type'] == 2)
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item d-flex align-items-center"
                                                       href="{{route('driverListing')}}">
                                                        <i data-feather="eye" class="icon-sm me-2"></i>
                                                        <span class="">View</span>
                                                    </a>

                                                </div>
                                            @elseif($user['user_type'] == 3)

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item d-flex align-items-center"
                                                       href="{{route('franchiseListing')}}">
                                                        <i data-feather="eye" class="icon-sm me-2"></i>
                                                        <span class="">View</span>
                                                    </a>

                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12">

                                            <h3 class="mb-2">{{$user['total']}}</h3>
                                            {{--                                                <div class="d-flex align-items-baseline">--}}
                                            {{--                                                    <p class="text-success">--}}
                                            {{--                                                        <span>Total Verified Driver: {{$verifiedDriverCount}} </span>--}}
                                            {{--                                                    </p>--}}
                                            {{--                                                    <p class="text-danger">--}}
                                            {{--                                                        <span>Total UnVerified Driver: {{$unverifiedDriverCount}} </span>--}}
                                            {{--                                                    </p>--}}
                                            {{--                                                </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                {{--                <div class="col-md-4 grid-margin stretch-card">--}}
                {{--                    <div class="card">--}}
                {{--                        <div class="card-body">--}}
                {{--                            <div class="d-flex justify-content-between align-items-baseline">--}}
                {{--                                <h6 class="card-title mb-0">Total Passenger</h6>--}}
                {{--                                <div class="dropdown mb-2 ">--}}
                {{--                                    <button class="btn p-0" type="button" id="dropdownMenuButton1"--}}
                {{--                                            data-bs-toggle="dropdown" aria-haspopup="true"--}}
                {{--                                            aria-expanded="false">--}}
                {{--                                        <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>--}}
                {{--                                    </button>--}}

                {{--                                </div>--}}
                {{--                            </div>--}}
                {{--                            <div class="row">--}}
                {{--                                <div class="col-12 col-md-12 col-xl-12">--}}
                {{--                                    <h3 class="mb-2">{{$passengerCount}}</h3>--}}
                {{--                                </div>--}}

                {{--                            </div>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            </div>
        </div>
        <h4 class=" mt-2 mb-2"> Booking Detail</h4>

        <div class="col-12 col-xl-12 stretch-card">

            <div class="row flex-grow-1">

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="card-title mb-0">Total Booking</h6>
                                <div class="dropdown mb-2">
                                    <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <a class="dropdown-item d-flex align-items-center"
                                           href="{{route('bookingListing')}}"><i data-feather="eye"
                                                                                 class="icon-sm me-2"></i>
                                            <span class="">View</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-md-12 col-xl-5">
                                    <h3 class="mb-2">{{$totalBooking}}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($bookings as $booking)
                    @if($booking['ride_status'] == 2 || $booking['ride_status'] == 5
                        || $booking['ride_status'] == 4)
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <h6 class="card-title mb-0">
                                            @if($booking['ride_status'] == 2 && $booking['booking_type'] == 'book_now')
                                                Passenger Cancel
                                            @elseif($booking['ride_status'] == 5 && $booking['booking_type'] == 'book_now')
                                                Driver Cancel
                                            @elseif($booking['ride_status'] == 4 && $booking['booking_type'] == 'book_now')
                                                Complete
                                            @endif


                                        </h6>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton2"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                                <a class="dropdown-item d-flex align-items-center"
                                                   href="{{route('bookingListing')}}"><i data-feather="eye"
                                                                                         class="icon-sm me-2"></i>
                                                    <span class="">View</span></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-12 col-xl-5">
                                            <h3 class="mb-2">{{$booking['total']}}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                @endforeach


            </div>
        </div>
    </div> <!-- row -->



    <div class="row">
        <div class=" ">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title mb-0">Monthly sales</h6>
                    </div>
                    <p class="text-muted">Sales are activities related to selling or the number of goods or services
                        sold in a given time period.</p>
                    <div id="chartdiv"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('admin/js/index.js')}}"></script>
    <script src="{{asset('admin/js/xy.js')}}"></script>
    <script src="{{asset('admin/js/Animated.js')}}"></script>
    <script>

        $(document).ready(function () {



            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new("chartdiv");

            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            // https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(
                am5xy.XYChart.new(root, {
                    panX: false,
                    panY: false,
                    // wheelX: "panX",
                    // wheelY: "zoomX",
                    layout: root.verticalLayout,
                })
            );


            // Add scrollbar
            // https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
            chart.set(
                "scrollbarX",
                am5.Scrollbar.new(root, {
                    orientation: "horizontal"
                })
            );


            var data = [
                @foreach($bookingArray as $bookingRecord)

                    @if($loop->last)
                        {
                            month: "{{$bookingRecord['month']}}",
                            total: {{$bookingRecord['count']}},
                        }
                    @else
                        {
                            month: "{{$bookingRecord['month']}}",
                            total: {{$bookingRecord['count']}}
                        },
                    @endif

                @endforeach



            ];

            // Create axes
            // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
            var xAxis = chart.xAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "month",
                    renderer: am5xy.AxisRendererX.new(root, {}),
                    tooltip: am5.Tooltip.new(root, {})
                })
            );

            xAxis.data.setAll(data);

            var yAxis = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    min: 0,
                    extraMax: 0.1,
                    renderer: am5xy.AxisRendererY.new(root, {})
                })
            );


            // Add series
            // https://www.amcharts.com/docs/v5/charts/xy-chart/series/

            var series1 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "Total Bookingx",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "total",
                    categoryXField: "month",
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "{name} in {categoryX}: {valueY} {info}"
                    })
                })
            );

            series1.columns.template.setAll({
                tooltipY: am5.percent(10),
                templateField: "columnSettings"
            });

            series1.data.setAll(data);


            chart.set("cursor", am5xy.XYCursor.new(root, {}));

            // Add legend
            // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
            // var legend = chart.children.push(
            //     am5.Legend.new(root, {
            //         centerX: am5.p50,
            //         x: am5.p50
            //     })
            // );
            // legend.data.setAll(chart.series.values);

            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            chart.appear(1000, 100);
            series1.appear();


        });


    </script>

@endsection
