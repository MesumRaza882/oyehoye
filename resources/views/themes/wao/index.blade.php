@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
            <div class="main-header">
                <div class="d-flex justify-content-center py-2" >
                    <img src="{{Domain::admin('logo')}}" class="rounded" width="120px" height="auto" alt="">
                </div>
            </div>

            <div class="home_cards">
                <div class="row">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="col-12 my-2">
                        <div class="card new_arrival">
                            <a href="{{route('product.NewArrivals')}}" class="text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-2">
                                    <h5 class="card-title white_text pt-3">New Arrivals</h5>
                                    <img src="{{asset('themes/wao/images/new-arrivals.png')}}" width="100px" height="60px" alt="">
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 my-2">
                        <div class="card active_stock">
                            <a href="{{route('active_stock')}}" class="text-white text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-2">
                                    <h5 class="card-title pt-3">Active Stock</h5>
                                    <img src="{{asset('themes/wao/images/active-stock.png')}}" width="60px" height="60px" alt="">
                                </div>
                            </a>
                        </div>
                    </div>
                    {{-- <div class="col-12 my-2">
                        <div class="card reseller">
                            <div class="card-body d-flex justify-content-between py-0">
                            <div>
                                <h5 class="card-title pt-3">Reseller</h5>
                                <h6>without Investment</h6>
                            </div>
                            <img src="{{asset('themes/wao/images/reseller-img.png')}}" class="mt-3" width="60px" height="60px" alt="">
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-12 my-2">
                        <div class="card reviews">
                            <a href="{{route('customer.reviews')}}" class="text-white text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                    <img src="{{asset('themes/wao/images/customer-reviews.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                                <h5 class="card-title pt-2">Customer Reviews</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 my-2">
                        <div class="card orders">
                            <a href="{{route('orders')}}" class="text-dark text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                    <img src="{{asset('themes/wao/images/orders.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                                    <h5 class="pt-2 ca-title">My Orders</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 my-2">
                      <div class="card yellow_color_cards">
                        <a href="{{route('web_login')}}" class="text-dark text-decoration-none">
                          <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                            <img src="{{asset('themes/wao/images/user.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                            <h5 class="card-title pt-2">Admin Login</h5>
                          </div>
                        </a>
                      </div>
                    </div>
                    <div class="col-12 my-2">
                        <div class="card yellow_color_cards">
                            <a href="{{route('user.complaint')}}" class="text-dark text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                    <img src="{{asset('themes/wao/images/bug.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                                    <h5 class="card-title pt-2">Complaint With Picture</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-12 my-2">
                        <div class="card yellow_color_cards">
                            <a href="{{route('product_search')}}" class="text-dark text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                    <img src="{{asset('themes/wao/images/search.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                                    <h5 class="card-title pt-2">Search</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                    @foreach ($category as $item)
                        <div class="col-12 my-2">
                            <a href="{{route('product.category',$item->id)}}" class="text-dark text-decoration-none">
                                <div class="card yellow_color_cards">
                                    <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                        <i class="fa-solid fa-heart heart_icon py-2 text-black"></i>
                                        <h5 class="card-title pt-2 text-end">{{$item->name}}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                    
                    {{-- <div class="col-12 my-2">
                        <div class="card yellow_color_cards">
                            <div class="card-body d-flex justify-content-between py-0">
                            <i class="fa-solid fa-heart heart_icon py-2"></i>
                            <h5 class="card-title pt-2">Mix Categories</h5>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-12 my-2">
                        <div class="card yellow_color_cards">
                            <a href="{{route('profile')}}" class="text-dark text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                    <img src="{{asset('themes/wao/images/user.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                                    <h5 class="card-title pt-2">My Profile</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-12 my-2">
                        <div class="card yellow_color_cards">
                            @php
                                $text = urlencode("checkout ". Domain::admin('website') ." collection, latest ladies suits");
                            @endphp
                            <a href="https://api.whatsapp.com/send?text={{$text}}" target="_blank" class="text-dark text-decoration-none">
                            <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                <img src="{{asset('themes/wao/images/share.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                            <h5 class="card-title pt-2">Share with Friends</h5>
                            </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 mt-2 mb-5">
                        <div class="card locked_items">
                            <a href="{{route('locked_items')}}" class="text-dark text-decoration-none">
                                <div class="card-body d-flex justify-content-between py-0" style="align-items:center;height:50px;">
                                    <img src="{{asset('themes/wao/images/locked.png')}}" class="mt-2 mb-2" width="30px" height="30px" alt="">
                                    <h5 class="card-title pt-2 ca-title">Locked Items</h5>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="whatsapp-icon">
                        <a href="https://wa.me/{{Domain::admin('whatsapp_number')}}" target="_blank">
                            <img src="{{asset('themes/wao/images/whatsapp.png')}}" alt="WhatsApp" width="50" height="50">
                        </a>
                    </div>
                    
                    {{-- <div class="cart">
                        <a href="{{route('place.order')}}" class="image-cart">
                            <img src="{{asset('themes/wao/images/shopping-bag.png')}}" class="cart-img" width="30px" class="pt-0" alt="">
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
            <button 
                type="button" class="btn-close" 
                data-bs-dismiss="modal" aria-label="Close" 
                style="position: absolute;width: 0.5em;height: 0.5em;top: 5px;right: 5px;"
            ></button>
            @foreach ($messages as $item)
                <div class="my-2">
                    <div style="background: #B2FF59;" class="p-2">
                        {{ $item->message }}
                    </div>
                    <div>
                        <button type="button" class="btn button1 btn-block w-100 white_text text-decoration-none mt-2">Mark as Read</button>
                    </div>
                </div>
            @endforeach
        </div>
      </div>
    </div>
</div>
@endsection
@section('script')
    @if ($messages->count()>0)
        <script>
            $(document).ready(function () {
                var notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'), {
                keyboard: false
                });
                notificationModal.show();
            });
        </script>
    @endif
@endsection