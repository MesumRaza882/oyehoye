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
                                <form method="POST"  action="{{route('storePickupAddressCode')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h4 class="text-center">Add PostEx Address Code</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="postEx_pickupAddressCode"
                                                     required  placeholder="Enter Code">
                                                    <span class="text-danger">@error('postEx_pickupAddressCode'){{$message}}@enderror</span>
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
                    @if(count($settings)>0)
                    <div class="my-sm-0 my-2 d-flex justify-content-center">
                        <button class="border btn ">All PostEx Address Code</button>
                    </div>
                    <table class="table table-bordered table-md-responsive myTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $setting)
                                <tr>
                                    <td>{{$setting->postEx_pickupAddressCode}}</td>
                                    <td>

                                        <form action="{{ route('destroyPickupAddressCode', $setting->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this code?')">Delete</button>
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
