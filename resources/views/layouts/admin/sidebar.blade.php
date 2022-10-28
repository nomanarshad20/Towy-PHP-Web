<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{route('adminDashboard')}}" class="sidebar-brand">
            TOWY
{{--            <span>Booking</span>--}}
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body ps ps--active-y">
        <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item active">
                <a href="{{route('adminDashboard')}}" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-box link-icon">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-category">User Management</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#driver" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Driver</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="driver">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('driverListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('driverCreate')}}" class="nav-link">Create</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('driverApprovalRequest')}}" class="nav-link">Approval Request</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#passenger" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Passenger</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="passenger">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('passengerListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('passengerCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#franchise" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Franchise</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="franchise">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('franchiseListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('franchiseCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item nav-category">Vehicle Management</li>

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#vehicle_type" role="button" aria-expanded="false"
                               aria-controls="emails">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="feather feather-mail link-icon">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span class="link-title">Vehicles Type</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="feather feather-chevron-down link-arrow">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </a>
                            <div class="collapse" id="vehicle_type">
                                <ul class="nav sub-menu">
                                    <li class="nav-item">
                                        <a href="{{route('vehicleTypeListing')}}" class="nav-link">Listing</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('vehicleTypeCreate')}}" class="nav-link">Create</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

{{--            <li class="nav-item active">--}}
{{--                <a href="{{route('vehicleFareSetting')}}" class="nav-link">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"--}}
{{--                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                         class="feather feather-box link-icon">--}}
{{--                        <path--}}
{{--                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>--}}
{{--                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>--}}
{{--                        <line x1="12" y1="22.08" x2="12" y2="12"></line>--}}
{{--                    </svg>--}}
{{--                    <span class="link-title">Vehicle Fare Setting</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#vehicle" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Vehicles</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="vehicle">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('vehicleListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('vehicleCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item nav-category">Services Management</li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#services" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Services</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="services">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('serviceListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('serviceCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>




            <li class="nav-item nav-category">Booking Management</li>


            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#booking" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Bookings</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="booking">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('bookingListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('bookingCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#booking_cancel" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Booking Cancel Reason</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="booking_cancel">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('cancelReasonListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('cancelReasonCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item nav-category">Setting</li>


            <li class="nav-item">
                <a href="{{route('setting')}}" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-box link-icon">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span class="link-title">Setting</span>
                </a>

            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#peakFactor" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Banner Image</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="peakFactor">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('bannerImageListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('bannerImageCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item nav-category">Peak Factor Management</li>


            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#peakFactor" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Peak Factor</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="peakFactor">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('peakFactorListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('peakFactorCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item nav-category">Voucher Code</li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#voucherCode" role="button" aria-expanded="false"
                   aria-controls="emails">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail link-icon">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <span class="link-title">Voucher Code </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-chevron-down link-arrow">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </a>
                <div class="collapse" id="voucherCode">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{route('voucherCodeListing')}}" class="nav-link">Listing</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('voucherCodeCreate')}}" class="nav-link">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 589px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 493px;"></div>
        </div>
    </div>
</nav>
