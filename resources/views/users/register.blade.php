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
                                <form method="POST" enctype="multipart/form-data" action="{{route('create')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h4>User LOgin/Signup</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>User Name</label>
                                                    <input type="text" class="form-control" name="user_name" required="">
                                                    <span class="text-danger">@error('user_name'){{$message}}@enderror</span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="phone" class="form-control" name="phone" required="">
                                                    <span class="text-danger">@error('phone'){{$message}}@enderror</span>

                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <select class="form-control" required name="country" id="country">
                                                        <option value="Pakistan">Pakistan</option>
                                                    </select>
                                                    <span class="text-danger">@error('country'){{$message}}@enderror</span>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <select class="form-control" required name="city" id="city">
                                                    <option value="">Select City</option>
                                                    @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                    @endforeach
                                                    </select>
                                                    <span class="text-danger">@error('city'){{$message}}@enderror</span>

                                                </div>
                                            </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Accept</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row my-3">
                <div class="col-sm-12">
                    <h3 class="text-center">All User Reported Problems</h3>
                    @if(count($users)>0)
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>User name</th>
                                    <th>phone</th>
                                    <th>country</th>
                                    <th>city</th>
                                    <th>Profile</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->phone}}</td>
                                    <td>{{$user->country}}</td>
                                    <td>{{$user->city_name}}</td>
                                    <td>
                                        <form action="{{route('profile')}}" method="GET">
                                            <input type="text" value="{{$user->id}}" name="id" hidden>
                                            <button type="submit" class="btn btn-sm btn-primary">Profile</button>
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