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
                                <form method="POST" enctype="multipart/form-data" action="{{route('addrate')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h4>Add Rate</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Review</label>
                                                    <input type="number" class="form-control" name="review" required="">
                                                    <span class="text-danger">@error('review'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Review Content</label>
                                                    <input type="text" class="form-control" name="review_text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>User Id</label>
                                                    <input type="number" class="form-control" name="user_id" required="">
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

            <div class="row my-3">
                <div class="col-sm-12">
                    <h3 class="text-center">All User Ratesss</h3>
                    @if(count($rates)>0)
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Review</th>
                                    <th>Review Content</th>
                                    <th>User_id</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rates as $rate)
                                <tr>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->review}}</td>
                                    <td>{{$rate->review_text}}</td>
                                    <td>{{$rate->user_id}}</td>
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