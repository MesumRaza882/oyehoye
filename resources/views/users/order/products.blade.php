@extends('admin.layouts.app')
@section('content')

<div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      
      @include('admin.layouts.navbar')

      @include('admin.layouts.sidebar')
      <!-- Main Content -->
      <div class="main-content">
        <div class="container">
            <!-- /View cart items -->
            <div class="row">
                <div class="col-md-12">
                <form action="{{route('viewcart')}}" method="GET">
                    @csrf
                    <input type="number" name="user_id" placeholder="Enter User Id" class="form-control"><br>
                    <button type="submit" class="btn btn-primary">view Cart</button>
                </form><br>
                @if(count($items)>0)
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Product name</th>
                                    <th>category</th>
                                    <th>price</th>
                                    <th>Quantity</th>
                                    <th class="text-danger">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $rate)
                                <tr>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->product->name}}</td>
                                    <td>{{$rate->product->category}}</td>
                                    <td>{{$rate->product->price}}</td>
                                    <td>{{$rate->quantity}}</td>
                                    <td>
                                        <form action="{{route('removecart')}}" method="POST">
                                            @csrf
                                            <input type="text"  value="{{$rate->product->id}}" name="prod_id">
                                            <input type="number" name="user_id" placeholder="Enter User Id">
                                            <button type="submit" class="btn btn-sm btn-danger">Remove Cart</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-9 offset-3">
                           <span class="alert alert-danger me-2">{{$message}}</span> <span class="bg-info text-white me-4 p-2">{{$orderscount}}</span><span class="fw-bold fs-4">Total Bill : <span class="fw-bold text-warning fs-4">{{$total}}</span></span>
                        </div>
                    </div>
                        @else
                        <div class="alert alert-warning text-white">No record</div>
                        @endif
                </div>
                <div class="row">
                    <h3>Place Order</h3>
                    <div class="col-sm-12">
                        <form action="{{route('orderPlace')}}" method="POST">
                            @csrf
                            <div class="row">
                            <div class="col-3">
                            <input type="text" name="user_id" class="form-control me-2" placeholder="Enter User Id">
                            </div>
                            <div class="col-3">
                                <input type="text" name="name" class="form-control me-2" placeholder="Enter User name">
                            </div>
                            <div class="col-3">
                                <input type="text" name="phone" class="form-control me-2" placeholder="Enter User phone">
                            </div>
                            <div class="col-3">
                                <input type="text" name="country" class="form-control me-2" placeholder="Enter User country">
                            </div>
                            <div class="col-3">
                                <input type="text" name="city" class="form-control me-2" placeholder="Enter User city">
                            </div>
                            <div class="col-3">
                                <input type="text" name="address" class="form-control me-2" placeholder="Enter User address">
                            </div>
                            <div class="col-3">
                                <input type="text" name="note" class="form-control me-2" placeholder="Enter User note"><br>
                            </div>
                            <div class="col-3">
                                <input type="text" name="charges" class="form-control me-2" placeholder="Enter Charges"><br>
                            </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Place</button>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <h3>View Users Order</h3>
                    <div class="col-sm-12">
                        <form action="{{route('viewOrder')}}" method="GET">
                            @csrf
                            <div class="row">
                            <div class="col-8">
                            <input type="text" name="user_id" class="form-control me-2" placeholder="Enter User Id">
                            </div>

                            </div>
                            <button type="submit" class="btn btn-primary">View Order</button>
                        </form>

                        @if(count($orders)>0)
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order id</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Phone</th>
                                    <th>details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $rate)
                                <tr>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->grandTotal}}</td>
                                    <td>{{$rate->status}}</td>
                                    <td>{{$rate->phone}}</td>
                                    <td>
                                        <form action="{{route('orderDetial')}}" method="GET">
                                            @csrf
                                            <input type="number" name="order_id" placeholder="Enter order_id" value="{{$rate->id}}">
                                            <button type="submit" class="btn btn-sm">View Detailst</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-warning text-white">No record</div>
                        @endif
                    </div>
                </div>

            </div>
            <div class="row my-3">
                <div class="col-sm-12">
                    <h3 class="text-center">All Product</h3>
                    @if(count($products)>0)
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Product name</th>
                                    <th>category</th>
                                    <th>price</th>
                                    <th>Quantity</th>
                                    <th>add to cart</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $rate)
                                <tr>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->name}}</td>
                                    <td>{{$rate->category}}</td>
                                    <td>{{$rate->price}}</td>
                                    <td>{{$rate->soldItem}}</td>
                                    <td>
                                        <form action="{{route('addcart')}}" method="POST">
                                            @csrf
                                            <input type="text" hidden value="{{$rate->id}}" name="prod_id">
                                            <input type="number" name="quantity" placeholder="Enter Quantity">
                                            <input type="number" name="user_id" placeholder="Enter User Id">
                                            <button type="submit" class="btn btn-sm">Add Cart</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-warning text-white">No record</div>
                        @endif
                </div>
            </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection