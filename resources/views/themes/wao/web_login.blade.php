@extends('themes.wao.layouts.main')
@section('style')
    <style>
        .rounded-pill-end{
            border-top-right-radius: 2rem !important;
            border-bottom-right-radius: 2rem !important;
        }
    </style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
        <div class="row">
            <div class="col-md-10 col-12 mx-auto" style="margin-top: 100px;">
                <a href="{{ url('/') }}" class="d-flex justify-content-center text-decoration-none">
                    <h3 class="white_text">{{Domain::admin('name')}}</h3>
                </a>
                <form action="{{route('web_login_process')}}" method="POST">
                    @csrf
                    @if(session('failed'))
                        <div class="alert alert-danger text-center">
                            {{ session('failed') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Email:</label>
                        <input type="email" placeholder="Email" name="email" class="form-control" value="{{old('email')}}" required>
                        @error('email')
                            <p class="text-white">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Password:</label>
                        <div class="input-group">
                            <input type="password" placeholder="password" name="password" id="password" class="form-control" required>
                            <div class="input-group-addon passwordShow d-flex justify-content-center align-items-center  px-3 rounded-pill-end" style="background: white;">
                                <i class="fa fa-eye eyeicon" style=""></i>
                                <i class="fa fa-eye-slash eyeslash" style="display: none;"></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-white">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn button1 btn-block white_text">Login</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
    // passwordShow
    $(document).ready(function () {
        // $('.passwordShow')
        $(document).on('click','.passwordShow',function (e) {
            e.preventDefault();
            var password=$('#password');
            if (password.attr('type')=='password') {
                $('.eyeicon').hide();
                $('.eyeslash').show();
                password.attr("type", "text");
            }else if (password.attr('type')=='text') {
                $('.eyeicon').show();
                $('.eyeslash').hide();
                password.attr("type", "password");
            }
        });
    });   
</script>
@endsection