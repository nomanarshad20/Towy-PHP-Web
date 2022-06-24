<!-- core:js -->
<script src="{{asset('admin/js/core.js')}}"></script>
<!-- endinject -->


<!-- inject:js -->
<script src="{{asset('admin/js/feather.min.js')}}"></script>
<script src="{{asset('admin/js/template.js')}}"></script>
<!-- endinject -->


<script src="{{asset('admin/js/toastr.min.js')}}"></script>
<script src="{{asset('admin/js/jquery.blockUI.js')}}"></script>
<script src="{{asset('admin/js/keypress_functions.js')}}"></script>

<script>




    function successMsg(_msg) {
        window.toastr.success(_msg);
    }

    function errorMsg(_msg) {
        window.toastr.error(_msg);
    }

    function warningMsg(_msg) {
        window.toastr.warning(_msg);
    }


    @if(Session::has('success'))
    successMsg('{{Session::get("success")}}');
    @endif

    @if(Session::has('error'))
    errorMsg("{{Session::get('error')}}");
    @endif


</script>
