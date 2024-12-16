@extends('admin.layouts.app')
@section('title') Admin Login @endsection

@section('content')

@php

if(request()->cookie('cookieEmail') && request()->cookie('cookiePassword'))
{
$login_email = request()->cookie('cookieEmail');
$login_password = request()->cookie('cookiePassword');
$remember = "checked='checked'";
}
else {

$login_email = '';
$login_password = '';
$remember = '';
}

@endphp

<section style="background-color:#4F5ECE">
    <div class="container">
        <div class="row vh-100 align-items-center d-flex justify-content-center">
            <div class="col-md-8 mh-100">
                <div class="card shadow p-md-3">
                    <div class="card-body">
                        <h4 class="text-center">Login Your Account</h4>
                        <form action="{{route('admin.check')}}" method="POST" autocomplete="off">
                            @csrf
                            <!-- E-mail -->
                            <div class="input-group mt-4">
                                <input type="email" class="form-control" value="{{$login_email}}" name="email" required placeholder="E-mail">
                            </div>
                            <p class="mb-4 mt-1 text-danger">@error('email'){{$message}}@enderror</sppan>

                                <!-- Password -->
                            <div class="input-group mt-4">
                                <input type="password" class="form-control" name="password" id="password_adm" required value="{{$login_password}}" placeholder="Password">
                                <a class="btn btn-primary text-white toggle-password">
                                    <span>
                                        <i class="toggle-icon fa fa-eye"></i>
                                    </span>
                                </a>
                            </div>
                            <p class="mb-4 mt-1 text-danger">@error('password'){{$message}}@enderror</p>

                            <!-- Remember me -->
                            <div class="my-2">
                                <input type="checkbox" name="remember" id="remember" {{$remember}}>
                                <label for="remember" class="fw-bold">Remember Me</label>
                            </div>

                            <!-- Form submit Button -->
                            <button type="submit" class="btn btn-primary m-auto d-block border">
                                Admin Login
                            </button>
                            <!-- End Loading Button -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')

<script>

</script>

@endsection