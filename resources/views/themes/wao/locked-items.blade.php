@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4 scree_bg">
        <div class="row">
            <div class="col-12 mx-auto" style="margin-top: 100px;">
                <form action="{{route('locked_items.product')}}" method="post">
                    @csrf
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Enter Password:</label>
                        <input type="text" placeholder="*******" name="password" class="form-control">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn button1 btn-block white_text">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        
        toastr.options = {
            'closeButton': true,
            'debug': false,
            'newestOnTop': false,
            'progressBar': false,
            "positionClass": "toast-top-center",
            'preventDuplicates': false,
            'showDuration': '1000',
            'hideDuration': '1000',
            'timeOut': '5000',
            'extendedTimeOut': '1000',
            'showEasing': 'swing',
            'hideEasing': 'linear',
            'showMethod': 'fadeIn',
            'hideMethod': 'fadeOut',
        }
    </script>
    @if (session()->has('success'))
        <script>
            toastr["success"]('{{session()->get("success")}}');
        </script>
    @endif
    @if (session()->has('error'))
        <script>
            toastr["error"]('{{session()->get("error")}}');
        </script>
    @endif
@endsection