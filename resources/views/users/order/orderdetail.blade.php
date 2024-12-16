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
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <section class="section">
                        <div class="section-body">
                            <div class="card">
                                <form method="POST" enctype="multipart/form-data" action="{{route('givenote')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h1>Add notes to Order</h1>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Order Id</label>
                                                    <input type="number" class="form-control" name="order_id" required="">
                                                    <span class="text-danger">@error('order_id'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <input type="text" class="form-control" name="note" required="">
                                                    <span class="text-danger">@error('note'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- order update -->
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <section class="section">
                        <div class="section-body">
                            <div class="card">
                                <form method="POST" enctype="multipart/form-data" action="{{route('updateOrder')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h1>Order Update if not Cancelled</h1>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Order Id</label>
                                                    <input type="number" class="form-control" name="order_id" required="">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name" required="">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>phone</label>
                                                    <input type="text" class="form-control" name="phone" required="">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>country</label>
                                                    <input type="text" class="form-control" name="country" required="">
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text" class="form-control" name="city" required="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            
            <!-- order update -->
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <section class="section">
                        <div class="section-body">
                            <div class="card">
                                <form method="POST" enctype="multipart/form-data" action="{{route('holdOrder')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h1>Order ON HOld</h1>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Order Id</label>
                                                    <input type="number" class="form-control" name="order_id" required="">
                                                </div>
                                            </div>
                                        </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            
            <!--Cancel Notification Arival-->
           <div class="row">
                    <div class="col-md-10 mx-auto">
                        <section class="section">
                            <div class="section-body">
                                <div class="card">
                                    <form method="POST" enctype="multipart/form-data" action="{{route('delarrivalNot')}}">
                                        @csrf
                                        <div class="card-header">
                                        <h1>Cancel Arrival Notification</h1>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>User Id</label>
                                                        <input type="number" class="form-control" name="user_id" required="">
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-primary" type="submit">View Arrivals</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                
                   <!--Cancel Notification bag-->
           <div class="row">
                    <div class="col-md-10 mx-auto">
                        <section class="section">
                            <div class="section-body">
                                <div class="card">
                                    <form method="POST" enctype="multipart/form-data" action="{{route('delbagNot')}}">
                                        @csrf
                                        <div class="card-header">
                                        <h1>Cancel Bag Notification</h1>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>User Id</label>
                                                        <input type="number" class="form-control" name="user_id" required="">
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-primary" type="submit">View Arrivals</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                
                
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <section class="section">
                        <div class="section-body">
                        <div class="card mt-4">
                                <form method="POST" enctype="multipart/form-data" action="{{route('cancelOrder')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h1>Order Cancel</h1>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Order Id</label>
                                                    <input type="number" class="form-control" name="order_id" required="">
                                                </div>
                                            </div>
                                        </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection