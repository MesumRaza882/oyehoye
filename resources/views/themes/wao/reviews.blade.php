@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
        <div class="row">
            <div class="cart">
                <a href="{{route('customer.reviews.form')}}" style="background-color: white; border-radius: 50%; padding-top: 2px; padding-bottom: 5px; padding-right: 10px; padding-left: 10px;" class="image-cart">
                    <img src="{{asset('themes/wao/images/add.png')}}" class="cart-img mt-0 pt-0" style="margin-top: -10px;" width="20px" alt="">
                </a>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection