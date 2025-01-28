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
                     @if (Session::get('success'))
                            <div class="alert alert-info text-center text-white">
                                {{Session::get('success')}}
                            </div>
                        @endif
                    <section class="section">
                        <div class="section-body">
                            <div class="card">
                                <form method="POST"  action="{{route('addAddress')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h4 class="text-center">Add Restrict Address</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Restrict Address</label>
                                                    <input type="text" class="form-control" name="address" data-role="tagsinput"
                                                    id="address" required  placeholder="Enter Address Names">
                                                    <span class="text-danger">@error('address'){{$message}}@enderror</span>
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

            <div class="row my-3">
                <div class="col-sm-12">
                    @if(count($address)>0)
                    <div class="my-sm-0 my-2 d-flex justify-content-center">
                        <button class="border btn ">All Restric Addresses<span class="mx-2 badge bg-primary">{{count($address)}}</span></button>
                        <button class="btn btn-sm btn-danger d-none deleteAllbtnAddresses" id="deleteAllbtn">Delete All</button>
                    </div>
                    <table class="table table-bordered table-md-responsive myTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="main_checkbox" style="background-color: aquamarine"></th>
                                    <th>id</th>
                                    <th>Restrict Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($address as $rate)
                                <tr>
                                    <td><input type="checkbox"  value="{{$rate->id}}" name="cat_checkbox"></td>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->address}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-warning text-white">No Resrict record</div>
                        @endif
                </div>
            </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
  
@endsection
