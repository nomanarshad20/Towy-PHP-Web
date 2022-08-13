@extends('layouts.admin.index')

@section('title')
    Franchise Create
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin/css/dataTables.bootstrap4.css')}}">
@endsection


@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Franchise Create</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Franchise Information</h6>
                    <form id="createFranchiseForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control"
                                           placeholder="Enter First Name">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control"
                                           placeholder="Enter Last Name">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" autocomplete="chrome-off"
                                           placeholder="Enter Email">
                                </div>
                            </div><!-- Col -->

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Mobile No</label>
                                    <input type="text" name="mobile_no" class="form-control"
                                           placeholder="Enter Mobile No">
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="Enter Password" autocomplete="chrome-off">
                                </div>
                            </div><!-- Col -->



                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="address" class="form-control pickup_location" id="locationSearch"
                                           placeholder="Enter Location">
                                    <input type="hidden" name="lat" id="lat" value="">
                                    <input type="hidden" name="lng" id="lng" value="">

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


    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP')}}&js?sensor=false&libraries=places&extn=.js"></script>

    @include('admin.franchise.script.autocomplete_script')

    <script>
        $(document).ready(function () {

            $('.createBtn').click(function () {

                var data = $('#createFranchiseForm').serialize();

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
                    url: '{{route("franchiseSave")}}',
                    data: data,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = '{{route('franchiseListing')}}';
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
