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
                        <div class="alert alert-success text-center text-white">
                            {{Session::get('success')}}
                        </div>
                    @endif
                    
                    <!--View All Reviews-->
                    <div class="table-responsive">
                        @if(count($users)>0)
                        <div class="d-flex justify-content-center">
                            <h4 class="text-center">
                                <span class="badge bg-info">Trashed Users  {{count($users)}}</span>
                            </h4>
                            <h4 class="text-center ms-2">
                                <a href="{{route('viewUsers')}}" class="btn btn-primary btn-sm">view All users<span class="badge bg-info ms-1"></span></a>
                            </h4>
                        </div>
                        <table class="table table-bordered table-md-responsive myTable">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>whatsapp</th>
                                    <th>Delete</th>
                                    <th>Restore</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $rate)
                                <tr>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->name}} {!! $rate->status > 0 ? '<i class="ms-2 fa-solid fa-ban"></i>' : '<i class="ms-2 fa-solid fa-check"></i>'!!}</td>
                                    <td>{{$rate->city_name}}</td>
                                    <td>{{$rate->whatsapp}}</td>
                                    <td><a href="{{route('perDelUser',$rate->id)}}" class="btn btn-outline-danger btn-sm">Permanet Delete</a></td>
                                    <td><a href="{{route('restoreUser',$rate->id)}}" class="btn btn-outline-success btn-sm">Restore</a></td>
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
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection