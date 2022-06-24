<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="google" value="notranslate">

    @yield('meta')

    <title>@yield('title')</title>

    @include('layouts.admin.fonts')
    @include('layouts.admin.css')

    @yield('style')
</head>

<body class="sidebar-dark">

<div class="main-wrapper">

    @auth
        {{--partial:partials/_sidebar.html--}}
        @include('layouts.admin.sidebar')

        <div class="page-wrapper">

            <!-- partial:partials/_navbar.html -->
            @include('layouts.admin.header')

            <div class="page-content">
                @yield('body')
            </div>

            @include('layouts.admin.footer')


        </div>
    @else
        @yield('body')
    @endauth

</div>

@include('layouts.admin.js')

@yield('script')
</body>

</html>


