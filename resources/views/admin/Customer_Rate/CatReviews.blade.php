@extends('admin.layouts.app')
@section('content')

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current
    {
    color: #fff !important;
    }
</style>
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
                <div class="col-md-12 mx-auto">
                   
                        <div class="successRev alert alert-success text-center text-white d-none" >
                            Reviews Saved Successfully!
                        </div>
                    
                        <div class="failRev alert alert-danger text-center text-white d-none" >
                            Data not accurate Missing some field
                        </div>
                    
                    <section class="section">
                        <div class="section-body">
                            <div class="card">
                                <form method="POST" enctype="multipart/form-data"  id="addReviewform">
                                    @csrf
                                    <div class="card-header">
                                    <h4 class="text-center">Add Category Reviews</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Customer Names</label>
                                                    <input type="text" class="form-control" name="customer_name" data-role="tagsinput"
                                                    id="customer_name" required="" multiple placeholder="Enter Customer's Names">
                                                    <span class="text-danger">@error('customer_name'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Cities</label>
                                                    <input type="text" class="form-control" name="city"  data-role="tagsinput"
                                                    required="" multiple placeholder="Enter Customer's Cities">
                                                    <span class="text-danger">@error('city'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--article-->
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Articles</label>
                                                    <input type="text" class="form-control" name="article"  data-role="tagsinput"
                                                    required placeholder="Enter Articles ">
                                                    <span class="text-danger">@error('article'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--category-->
                                            <div class="col-md-12">
                                                <label class="me-1">Select Category</label><span style="font-size:12px; color:gray;">Optional</span>
                                                <select name="category"  class="form-control">
                                                    <option value="" selected disabled>
                                                        Select Category
                                                    </option>
                                                    <option value="Luxury">Luxury</option>
                                                    <option value="Bag">Bag</option>
                                                    <option value="Arrival">Arrival</option>
                                                </select>
                                                <span class="text-danger">@error('category'){{$message}}@enderror</span>
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
                    <h3 class="text-center">All Customer Review On All Categories</h3>
                    @if(count($rev)>0)
                    <div class="my-sm-0 my-2 d-flex justify-content-center">
                        <!--<button class="border btn btn-sm">Luxury Reviews<span class="mx-2 badge bg-primary">{{$lux}}</span></button>-->
                        <button class="border btn btn-sm">All Reviews<span class="mx-2 badge bg-primary">{{count($rev)}}</span></button>
                        <!--<button class="border btn btn-sm">Arrival Reviews<span class="mx-2 badge bg-primary">{{$arr}}</span></button>-->
                        <button class="btn btn-sm btn-danger d-none deleteAllbtnReviews" id="deleteAllbtn">Delete All</button>
                    </div>
                    <table class="table table-bordered table-md-responsive myTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="main_checkbox" style="background-color: aquamarine"></th>
                                    <th>id</th>
                                    <th>Customer Nmae</th>
                                    <th>City</th>
                                    <th>Article</th>
                                    <!--<th>Category</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rev as $rate)
                                <tr>
                                    <td><input type="checkbox"  value="{{$rate->id}}" name="cat_checkbox"></td>
                                    <td>{{ $rate->id }} 
                                    {!! $rate->category == 'Real' ? '<button class="btn btn-sm btn-success ms-1">Real</button>' : '' !!}
                                    </td>
                                    <td>{{$rate->customer_name}}</td>
                                    <td>{{$rate->city}}</td>
                                    <td>{{$rate->article}}</td>
                                    <!--<td>{{$rate->category}}</td>-->
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
