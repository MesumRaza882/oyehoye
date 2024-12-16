@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
        <div class="row">
            <div class="col-md-8 col-12 mx-auto my-4">
                <form action="{{route('product.search')}}" method="post">
                    @csrf
                    <div class="form-group py-2">
                        <input type="text" placeholder="Search Product" name="name" class="form-control">
                    </div>
                    <div class="d-grid">
                        <button class="btn button1 btn-block white_text">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection