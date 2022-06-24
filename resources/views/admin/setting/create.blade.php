@extends('layouts.admin.index')

@section('title')
    Setting
@endsection




@section('body')

    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Setting</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Setting</h6>
                    <form id="createForm">
                        @csrf
                        @if(isset($data->id))
                            <input type="hidden" name="id" value="{{$data->id}}">
                        @endif
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Search Range</label>
                                    <input type="text" name="search_range" class="form-control"
                                           placeholder="Enter Search Range"
                                           value="{{isset($data->search_range) ? $data->search_range:''}}"
                                           onkeypress="return isNumberKey(event)">
                                </div>
                            </div><!-- Col -->



                        </div><!-- Row -->


                        <button type="button" class="btn btn-primary createBtn">
                            Save
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')



    <script>
        $(document).ready(function () {

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
                    url: '{{route("saveSetting")}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,

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
