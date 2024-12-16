@extends('admin.layouts.app')
@section('content')

<style>
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_info, 
    .dataTables_wrapper .dataTables_paginate {
        display:none !important;
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
                    
                    <!--Navtabs Content-->
                    <div class="tab-content" id="nav-tabContent">
                        <!--All orders-->
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="table-responsive">
                        @if(count($orders)>0)
                        <div class="row justify-content-between mb-3">
                            <div class="col-md-5 col-sm-12 d-flex">
                            `   <h5>Delivered/Dispatched Orders  <span class="badge bg-info ms-1">{{count($orders)}}</span></h5>
                                <p class="px-3">{{ $msg  }}</p>
                            </div>
                            <!--Filter Order-->
                            <div class="col-md-7 col-sm-12">
                                 <form class="form-inline" method="GET" action="{{route('reportorders')}}">@csrf
                                        <label class="pe-1">From</label><input type="date" class="me-2 form-control" name="fromDate" required max="<?php echo date("Y-m-d"); ?>">
                                         <label class="pe-1">To</label><input type="date" class="form-control" name="toDate" required max="<?php echo date("Y-m-d"); ?>">
                                        <button class="btn btn-primary mx-2 my-2 my-sm-0" type="submit" >Filter Order</button>
                                </form>
                            </div>
                        </div>
                        <table class="table table-bordered table-md-responsive" id="examples">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nmae</th>
                                    <th>City</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Charges</th>
                                    <th>Grand Total</th>
                                    <th>Discount</th>
                                    <th>Grand Profit</th>
                                    <th >Whatsapp</th>
                                    <th  class="text-center">Order Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $rate)
                                <tr>
                                    <td>{{$rate->id}}</td>
                                    <td>{{$rate->name}}</td>
                                    <td>{{$rate->city}}</td>
                                    <td>{{$rate->phone}}</td>
                                    <td class="fw-bold {{$rate->status == 'CANCEL' ? 'text-danger' : ''}}" style="@if($rate->status == 'DISPATCHED') color:green;  @endif">
                                        {{$rate->status == 'PENDING' ? 'PENDING' : $rate->status}}
                                        {!! $rate->cancel_note == '' ? '' : '<i class="fa-solid fa-check-to-slot"></i>' !!}
                                    </td>
                                    <td> Rs: <span class="fw-bold">{{$rate->charges}}</span></td>
                                    <td>Rs: <span class="fw-bold">{{$rate->grandTotal}}</span></td>
                                    <td>
                                        @php $disc = 0 ; $distotal = 0 ; @endphp
                                        @foreach($rate->orderitems as $item)
                                            @php 
                                                $disc =  ($item->discount * $item->qty)  ;
                                                $distotal += $disc;
                                            @endphp
                                        @endforeach
                                        Rs: <span class="fw-bold">{{$distotal}}</span>
                                    </td>
                                     <td>Rs: <span class="fw-bold text-success">{{$rate->grandProfit}}</span></td>
                                    <td>
                                         <a href="https://wa.me/+92{{$rate->userdetail->whatsapp}}?text=Hi%20Welcome%20to%20WAO"
                                         target="_blank" class="text-success">
                                              {{$rate->userdetail->whatsapp}}
                                         </a>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{$rate->created_at->format('d')}}</span>
                		                  <span class="mos">{{$rate->created_at->format('M')}}</span>
                		                  <span class="yr">{{$rate->created_at->format('Y')}}</span>
                                    </td>
                                    <td>
                                        <!--<form method="POST" action="{{route('delOrder',$rate->id)}}">@csrf-->
                                            <a href="{{route('editOrder',$rate->id)}}" class="btn btn-primary btn-sm">Edit</a>
                                            <!--<button onclick="return confirm('Are you sure to delete Order?');" type="submit" class="btn btn-danger btn-sm">Delete</button>-->
                                        <!--</form>-->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $orders->links() !!}
                         
                        @else
                        <div class="alert alert-warning text-white">No record</div>
                        @endif
                    </div>
                      </div>
                      
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection

