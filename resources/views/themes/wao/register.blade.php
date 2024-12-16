@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
        <div class="row">
            <div class="col-md-8 col-12 mx-auto" style="margin-top: 100px;">
                <a href="{{ url('/') }}" class="d-flex justify-content-center text-decoration-none">
                    <h3 class="white_text">{{Domain::admin('name')}}</h3>
                    <h4 class="yellow_text" style="padding-top:5px;padding-left:4px">Collection</h4>
                </a>
                <form action="{{route('register.submit')}}" method="POST">
                    @csrf
                    @if(session('failed'))
                        <div class="alert alert-danger text-center">
                            {{ session('failed') }}
                        </div>
                    @endif
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Name:</label>
                        <input type="text" placeholder="Name" name="name" class="form-control">
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Whatsapp:</label>
                        <input type="text" placeholder="Whatsapp" name="whatsapp" class="form-control">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn button1 btn-block white_text">Signup</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>

@endsection