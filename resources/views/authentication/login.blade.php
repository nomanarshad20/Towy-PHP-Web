@extends('layouts.admin.index')

@section('title')
    Login
@endsection


@section('body')

    <div class="page-wrapper full-page">
        <div class="page-content d-flex align-items-center justify-content-center">

            <div class="row w-100 mx-0 auth-page">
                <div class="col-md-9 col-xl-9 mx-auto">
                    <div class="card">
                        <div class="row">
                            <div class="col-md-5 pe-md-0">
                                <div class="auth-side-wrapper">

                                </div>
                            </div>

                            <div class="col-md-6 ps-md-0">
                                <div class="auth-form-wrapper px-4 py-5">
                                    <a href="#" class="noble-ui-logo d-block mb-2">TOWY
                                        {{--                                        <span>Booking</span>--}}
                                    </a>
                                    <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>
                                    <form class="forms-sample" id="loginForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="userEmail" class="form-label">Email address</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                   placeholder="Email">
                                        </div>
                                        <div class="mb-3">
                                            <label for="userPassword" class="form-label">Password</label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                   autocomplete="current-password" placeholder="Password">
                                        </div>
                                        <div class="form-check mb-3">
                                            <input type="checkbox" class="form-check-input" name="remember_me"
                                                   id="authCheck">
                                            <label class="form-check-label" for="authCheck">
                                                Remember me
                                            </label>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" type="button"
                                               class="loginBtn btn btn-primary me-2 mb-2 mb-md-0 text-white">
                                                Login
                                            </a>
                                            {{--                                            <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">--}}
                                            {{--                                                <i class="btn-icon-prepend" data-feather="twitter"></i>--}}
                                            {{--                                                Login with twitter--}}
                                            {{--                                            </button>--}}
                                        </div>
                                        <a href="{{route('forgetPasswordForm')}}" class="d-block mt-3 text-muted">Forgot
                                            Password?</a>
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

        $(document).ready(function () {

            $('.loginBtn').click(function () {

                var data = $('#loginForm').serialize();

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
                    url: '{{route("loginUser")}}',
                    data: data,

                    success: function (response, status) {

                        if (response.result == 'success') {
                            $.unblockUI();
                            successMsg(response.message);

                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);

                        } else if (response.result == 'error') {
                            $.unblockUI();
                            errorMsg(response.message);
                            if (response.url) {
                                setTimeout(function () {
                                    window.location.href = response.url;
                                }, 1000);

                            }
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


        $("#password").keydown(function (e) {
            if (e.keyCode == 13) {
                $('.loginBtn').click();
            }
        });

    </script>

@endsection
