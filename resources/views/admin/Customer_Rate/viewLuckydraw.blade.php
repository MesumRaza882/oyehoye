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
                        @if (Session::get('fail'))
                            <div class="alert alert-danger text-center text-white">
                                {{Session::get('fail')}}
                            </div>
                        @endif
                        @if(count($rates)>0)
                        <div class="d-flex justify-content-around mb-3">
                            <h4 class="text-center">All LuckyDraws  <span class="badge bg-info ms-1">{{count($allrates)}}</span></h4>
                           <button class="btn btn-sm btn-danger d-none deleteAllbtnLucky" id="deleteAllbtn">Delete All</button>
                        </div>
                        <div class="table-responsive">
                        </span></h4>
                        <table class="table table-bordered table-md-responsive myTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="main_checkbox" style="background-color: aquamarine"></th>
                                    <th>Lucky Draw Name</th>
                                    <th>Whatsapp</th>
                                    <th>Facebook Nmae</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rates as $rate)
                                <tr>
                                    <td><input type="checkbox"  value="{{$rate->id}}" name="cat_checkbox"></td>
                                    <td>{{$rate->user_name}}</td>
                                    <td>
                                         <a href="https://wa.me/+92{{$rate->whatsapp}}?text=Hi%20Welcome%20to%20WAO"
                                         target="_blank" class="text-success">
                                              {{$rate->whatsapp}}
                                         </a>
                                    </td>
                                    <td>{{$rate->facebook_name}}</td>
                                     <td>
                                        <a href="{{route('dellucky',$rate->id)}}" onclick="return confirm('Are you sure to delete LuckyDraw?');" class="btn btn-sm btn-danger">Delete</a>
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
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function(){
        
    });
</script>
@endsection