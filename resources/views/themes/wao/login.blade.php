@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
        <div class="row">
            <div class="col-12 mx-auto" style="margin-top: 100px;">
                <div class="d-flex justify-content-center">
                    <h3 class="white_text">WAO</h3>
                    <h4 class="white_text px-2">Imported</h4>
                    <h6 class="yellow_text px-1 pt-2">Collection</h6>
                </div>
                <form action="{{route('register.submit')}}" method="POST">
                    @csrf
                    @if(session('failed'))
                        <div class="alert alert-danger text-center">
                            {{ session('failed') }}
                        </div>
                    @endif
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Name:</label>
                        <input type="text" placeholder="Name" value="Hassan" name="name" class="form-control">
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Whatsapp:</label>
                        <input type="text" placeholder="Whatsapp" value="03001234567" name="whatsapp" class="form-control">
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