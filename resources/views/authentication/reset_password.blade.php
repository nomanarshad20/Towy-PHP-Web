@extends('layouts.admin.index')

@section('title')
    Reset Password
@endsection


@section('body')

    <div class="page-wrapper full-page">
        <div class="page-content d-flex align-items-center  justify-content-center">

            <div class="row w-100 mx-0 auth-page">
                <div class="col-md-6 col-xl-6 mx-auto">
                    <div class="card">
                        <div class="row">

                            <div class="col-md-5 pe-md-0">
                                <div class="auth-side-wrapper">

                                </div>
                            </div>

                            <div class="col-md-6 ps-md-0">
                                <div class="auth-form-wrapper px-4 py-5">
                                    <a href="#" class="noble-ui-logo text-center d-block mb-2">
                                        TOWY
{{--                                        <span>Booking</span>--}}
                                    </a>
                                    <h5 class="text-muted text-center fw-normal mb-4">Reset Your Password</h5>
                                    <form class="forms-sample" id="updatePassForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="userEmail" class="form-label">Email address</label>
                                            <input type="email" id="email" name="email" class="form-control"  placeholder="Email">
                                        </div>

                                        <div class="mb-3">
                                            <label for="userEmail" class="form-label">Password</label>
                                            <input type="password" id="password" name="password" class="form-control"  placeholder="Email">
                                        </div>


                                        <div class="mb-3">
                                            <label for="userEmail" class="form-label">Confirm Password</label>
                                            <input type="password" id="confirm_password" name="password_confirmation" class="form-control"  placeholder="Email">
                                        </div>


                                        <div>
                                            <a href="javascript:void(0)" type="button"
                                               class="updatePassBtn btn btn-primary me-2 mb-2 mb-md-0 text-white">
                                                Password Reset
                                            </a>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection



@section('script')

    <script>

        $('.updatePassBtn').click(function () {

            var data = $('#updatePassForm').serialize();

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
                url: '{{route("changePassword")}}',
                data: data,

                success: function (response, status) {

                    if (response.result == 'success') {
                        $.unblockUI();
                        successMsg(response.message);

                        setTimeout(function(){window.location.href = '{{route('loginPage')}}';},1000);

                    }
                    else if (response.result == 'error') {
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


        $("#confirm_password").keydown(function (e) {
            if (e.keyCode == 13) {
                $('.updatePassBtn').click();
            }
        });



    </script>

@endsection
