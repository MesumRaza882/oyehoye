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
                                <form method="POST" enctype="multipart/form-data" action="{{route('updateprofile')}}">
                                    @csrf
                                    <input type="text" hidden value="{{$user->id}}" name="id">
                                    <div class="card-header">
                                    <h4 class="text-center">{{$user->name}}'s Profile</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>User Name</label>
                                                    <input type="text" class="form-control" name="user_name" required="" value="{{$user->name}}">
                                                    <span class="text-danger">@error('user_name'){{$message}}@enderror</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="phone" class="form-control" name="phone" required="" value="{{$user->phone}}">
                                                    <span class="text-danger">@error('phone'){{$message}}@enderror</span>

                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                   <textarea name="address"class="form-control">{{$user->address}}</textarea>
                                                    <span class="text-danger">@error('address'){{$message}}@enderror</span>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input type="text" name="city_name" class="form-control" value="{{$user->city_name}}">
                                                    <span class="text-danger">@error('city_name'){{$message}}@enderror</span>

                                                </div>
                                            </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Save</button>
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