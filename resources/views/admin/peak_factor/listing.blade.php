@extends('layouts.admin.index')

@section('title')
    Peak Factor Listing
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
            <h4 class="mb-3 mb-md-0">Peak Factor Listing</h4>
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
                                <th>Start Point</th>
                                <th>End Point Point</th>
                                <th>Factor Rate</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $factor)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$factor->start_point}}</td>
                                    <td>{{$factor->end_point}}</td>
                                    <td>{{$factor->factor_rate}}</td>
                                    <td>
                                        <a title="Edit" href="{{route('peakFactorEdit',['id'=>$factor->id])}}">
                                            <i data-feather="edit">Edit</i>
                                        </a>
                                        <a data-id="{{$factor->id}}" title="Delete" class="deleteRecord"
                                           href="javascript:void(0)">
                                            <i data-feather="trash">Delete</i>
                                        </a>

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

    @include('admin.vehicle_type.modal.delete_modal')
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
                    url: '{{route("peakFactorDelete")}}',
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


        });
    </script>
@endsection
