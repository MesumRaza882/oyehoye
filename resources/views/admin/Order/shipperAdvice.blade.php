@extends('admin.layouts.app')
@section('title') Orders @endsection
@section('content')

<div class="main-content">

    @include('include.modal.orderHistoryModal')

    <div class="container">
        <div class="row">
            <?php /*
            <!-- filter records -->
            <div class="row">
                <!-- total record count -->
                <div class="col-sm-auto col-6 head_order">
                    <p class="p-sm-2 p-1 bg-primary text-white">Orders:<span class="ms-1">{{ number_format(($total_records), 0, '.', ',') }}</span></p>
                </div>
                <div class="col-sm-auto col-6 head_order">
                    <p class="mx-1 border p-sm-2 p-1 fw-bold {{ $orders->sum('grandProfit') >= 0 ? 'border-success' : 'border-danger'}}">
                        {{ $orders->sum('grandProfit') >= 0 ? 'Profit' : 'Loss'}}:
                        <span class="ms-1 badge bg-primary p-sm-2 p-1" style="font-size:14px;">{{ number_format($orders->sum('grandProfit'), 0, '.', ',') }}</span>
                    </p>
                </div>
                <div class="col-sm-auto col-6 head_order">
                    <p class="me-1 p-sm-2 p-1 fw-bold  border border-info">
                        Order-Items:
                        <span class="ms-1 badge bg-primary p-sm-2 p-1" style="font-size:14px;">{{ number_format(($count_order_items), 0, '.', ',') }}</span>
                    </p>
                </div>
                <div class="col-sm-auto col-6 head_order">
                    <p class="me-1 p-sm-2 p-1 fw-bold  border border-secondary">
                        DC:
                        <span class="ms-1 badge bg-primary p-sm-2 p-1" style="font-size:14px;">{{ number_format($orders->sum('charges'), 0, '.', ',') }}</span>
                    </p>
                </div>

                <div class="col-12 py-0 my-0">
                    <hr>
                </div>

                <!-- Get current status orders -->
                <!-- <div class="col-auto mb-sm-0">
                    <a href="{{route('trackViewApi')}}" class="btn btn-warning">Get Current Order Status</a>
                </div> -->
                <!-- filter -->
                <div class="col-12 mb-2">
                    <form method="GET" action="{{route('allorders')}}" id="search-form">
                        <input type="hidden" name="is_reseller_order" value="{{ request()->is_reseller_order }}" />
                        <div class="row justify-content-between">

                            <div class="col-lg-auto  mb-2 mb-2">
                                <label class="pb-0">Customer Orders Type</label>
                                <div class="align-items-center d-flex">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="blocked_orders" id="realOrders" value="" @if(!request()->has('blocked_orders') || request()->get('blocked_orders') == '') checked @endif>
                                        <label class="form-check-label" for="realOrders">Real Orders</label>
                                    </div>
                                    <div class="form-check mx-2">
                                        <input type="radio" class="form-check-input" name="blocked_orders" id="blockedOrders" value="1" @if(request()->get('blocked_orders') == 1) checked @endif>
                                        <label class="form-check-label" for="blockedOrders">Blocked Customer Orders</label>
                                    </div>
                                </div>
                            </div>

                            <!-- select records -->
                            <div class="col-lg-2 mb-2 col-6">
                                <label class="pb-0">Select Records</label>
                                <select name="records" class="form-control me-2" required>
                                    <option value="50" @if(request()->get('records') == 50) selected @endif>50</option>
                                    <option value="100" @if(request()->get('records') == 100) selected @endif>100</option>
                                    <option value="200" @if(request()->get('records') == 200) selected @endif>200</option>
                                    <option value="300" @if(request()->get('records') == 300) selected @endif>300</option>
                                    <option value="500" @if(request()->get('records') == 500) selected @endif>500</option>
                                </select>
                            </div>

                            <!-- select Status -->
                            <div class="col-lg-3 mb-2 col-6">
                                <label class="pb-0">Select Status</label>
                                <select name="status" class="form-control me-2">
                                    <option value="">All</option>
                                    <option value="PENDING" @if(request()->get('status') == 'PENDING') selected @endif>PENDING</option>
                                    <option value="DISPATCHED" @if(request()->get('status') == 'DISPATCHED') selected @endif>DISPATCHED</option>
                                    <option value="DELIVERED" @if(request()->get('status') == 'DELIVERED') selected @endif>DELIVERED</option>
                                    <option value="ON-THE-WAY" @if(request()->get('status') == 'ON-THE-WAY') selected @endif>ON-THE-WAY</option>
                                    <option value="RETURNED" @if(request()->get('status') == 'RETURNED') selected @endif>RETURNED</option>
                                    <option value="CANCEL" @if(request()->get('status') == 'CANCEL') selected @endif>CANCEL</option>
                                    <option value="Shipment - Booked" @if(request()->get('order-status') == 'Shipment - Booked') selected @endif>Shipment - Booked</option>
                                    <option value="Re-Booked" @if(request()->get('status') == 'Re-Booked') selected @endif>Re-Booked</option>
                                    <option value="Shipment - Arrived at Origin" @if(request()->get('status') == 'Shipment - Arrived at Origin') selected @endif>Shipment - Arrived at Origin</option>
                                    <option value="Shipment - In Transit" @if(request()->get('status') == 'Shipment - In Transit') selected @endif>Shipment - In Transit</option>
                                    <option value="Shipment - Arrived at Destination" @if(request()->get('status') == 'Shipment - Arrived at Destination') selected @endif>Shipment - Arrived at Destination</option>
                                    <option value="Shipment - Out for Delivery" @if(request()->get('status') == 'Shipment - Out for Delivery') selected @endif>Shipment - Out for Delivery</option>
                                    <option value="Shipment - Return Confirmation Pending" @if(request()->get('status') == 'Shipment - Return Confirmation Pending') selected @endif>Shipment - Return Confirmation Pending</option>
                                    <option value="Shipment - Re-Attempt Requested" @if(request()->get('status') == 'Shipment - Re-Attempt Requested') selected @endif>Shipment - Re-Attempt Requested</option>
                                    <option value="Shipment - Rider Picked" @if(request()->get('status') == 'Shipment - Rider Picked') selected @endif>Shipment - Rider Picked</option>
                                    <option value="Shipment - Misroute Forwarded" @if(request()->get('status') == 'Shipment - Misroute Forwarded') selected @endif>Shipment - Misroute Forwarded</option>
                                    <option value="Shipment - Re-Attempt" @if(request()->get('status') == 'Shipment - Re-Attempt') selected @endif>Shipment - Re-Attempt</option>
                                    <option value="Shipment - Delivered" @if(request()->get('status') == 'Shipment - Delivered') selected @endif>Shipment - Delivered</option>
                                    <option value="Shipment - Cancelled" @if(request()->get('status') == 'Shipment - Cancelled') selected @endif>Shipment - Cancelled</option>
                                    <option value="Return - Confirm" @if(request()->get('status') == 'Return - Confirm') selected @endif>Return - Confirm</option>
                                    <option value="Return - In Transit" @if(request()->get('status') == 'Return - In Transit') selected @endif>Return - In Transit</option>
                                    <option value="Return - Arrived at Origin" @if(request()->get('status') == 'Return - Arrived at Origin') selected @endif>Return - Arrived at Origin</option>
                                    <option value="Return - Dispatched" @if(request()->get('status') == 'Return - Dispatched') selected @endif>Return - Dispatched</option>
                                    <option value="Return - Delivery Unsuccessful" @if(request()->get('status') == 'Return - Delivery Unsuccessful') selected @endif>Return - Delivery Unsuccessful</option>
                                    <option value="Return - Delivered to Shipper" @if(request()->get('status') == 'Return - Delivered to Shipper') selected @endif>Return - Delivered to Shipper</option>
                                    <option value="Return - Not Attempted" @if(request()->get('status') == 'Return - Not Attempted') selected @endif>Return - Not Attempted</option>
                                    <option value="Return - On Hold" @if(request()->get('status') == 'Return - On Hold') selected @endif>Return - On Hold</option>
                                    <option value="Replacement - In Transit" @if(request()->get('status') == 'Replacement - In Transit') selected @endif>Replacement - In Transit</option>
                                    <option value="Replacement - Arrived at Origin" @if(request()->get('status') == 'Replacement - Arrived at Origin') selected @endif>Replacement - Arrived at Origin</option>
                                    <option value="Replacement - Dispatched" @if(request()->get('status') == 'Replacement - Dispatched') selected @endif>Replacement - Dispatched</option>
                                    <option value="Replacement - Delivery Unsuccessful" @if(request()->get('status') == 'Replacement - Delivery Unsuccessful') selected @endif>Replacement - Delivery Unsuccessful</option>
                                    <option value="Replacement - Delivered to Shipper" @if(request()->get('status') == 'Replacement - Delivered to Shipper') selected @endif>Replacement - Delivered to Shipper</option>
                                </select>
                            </div>

                            <!-- filter by blocked customers orders -->
                            <!-- <div class="col-lg-3 mb-2 mb-2">
                                <label class="pb-0">Customer Orders Type</label>
                                <select name="blocked_orders" class="form-control me-2">
                                    <option value="">Real Orders</option>
                                    <option value="1" @if(request()->get('blocked_orders') == 1) selected @endif>Blocked Customer Orders</option>
                                </select>
                            </div> -->

                            <div class="col-lg-3 mb-2 mb-2">
                                <label class="pb-0">Track Orders Type</label>
                                <select name="tracking_order_type" class="form-control me-2">
                                    <option value="">All</option>
                                    <option value="trax" @if(request()->get('tracking_order_type') == 'trax') selected @endif>TRAX</option>
                                    <option value="mnp" @if(request()->get('tracking_order_type') == 'mnp') selected @endif>MNP</option>
                                    <option value="postEx" @if(request()->get('tracking_order_type') == 'postEx') selected @endif>Post-Ex</option>
                                </select>
                            </div>

                            <!-- select Order status Status -->
                            <!-- <div class="col-lg-3 mb-2 col-6">
                                <label class="pb-0">Select Order Status</label>
                                <select name="order-status" class="form-control me-2">
                                    <option value="">All</option>
                                    <option value="Shipment - Booked" @if(request()->get('order-status') == 'Shipment - Booked') selected @endif>Shipment - Booked</option>
                                    <option value="Re-Booked" @if(request()->get('order-status') == 'Re-Booked') selected @endif>Re-Booked</option>
                                    <option value="Shipment - Arrived at Origin" @if(request()->get('order-status') == 'Shipment - Arrived at Origin') selected @endif>Shipment - Arrived at Origin</option>
                                    <option value="Shipment - In Transit" @if(request()->get('order-status') == 'Shipment - In Transit') selected @endif>Shipment - In Transit</option>
                                    <option value="Shipment - Arrived at Destination" @if(request()->get('order-status') == 'Shipment - Arrived at Destination') selected @endif>Shipment - Arrived at Destination</option>
                                    <option value="Shipment - Out for Delivery" @if(request()->get('order-status') == 'Shipment - Out for Delivery') selected @endif>Shipment - Out for Delivery</option>
                                    <option value="Shipment - Return Confirmation Pending" @if(request()->get('order-status') == 'Shipment - Return Confirmation Pending') selected @endif>Shipment - Return Confirmation Pending</option>
                                    <option value="Shipment - Re-Attempt Requested" @if(request()->get('order-status') == 'Shipment - Re-Attempt Requested') selected @endif>Shipment - Re-Attempt Requested</option>
                                    <option value="Shipment - Rider Picked" @if(request()->get('order-status') == 'Shipment - Rider Picked') selected @endif>Shipment - Rider Picked</option>
                                    <option value="Shipment - Misroute Forwarded" @if(request()->get('order-status') == 'Shipment - Misroute Forwarded') selected @endif>Shipment - Misroute Forwarded</option>
                                    <option value="Shipment - Re-Attempt" @if(request()->get('order-status') == 'Shipment - Re-Attempt') selected @endif>Shipment - Re-Attempt</option>
                                    <option value="Shipment - Delivered" @if(request()->get('order-status') == 'Shipment - Delivered') selected @endif>Shipment - Delivered</option>
                                    <option value="Shipment - Cancelled" @if(request()->get('order-status') == 'Shipment - Cancelled') selected @endif>Shipment - Cancelled</option>
                                    <option value="Return - Confirm" @if(request()->get('order-status') == 'Return - Confirm') selected @endif>Return - Confirm</option>
                                    <option value="Return - In Transit" @if(request()->get('order-status') == 'Return - In Transit') selected @endif>Return - In Transit</option>
                                    <option value="Return - Arrived at Origin" @if(request()->get('order-status') == 'Return - Arrived at Origin') selected @endif>Return - Arrived at Origin</option>
                                    <option value="Return - Dispatched" @if(request()->get('order-status') == 'Return - Dispatched') selected @endif>Return - Dispatched</option>
                                    <option value="Return - Delivery Unsuccessful" @if(request()->get('order-status') == 'Return - Delivery Unsuccessful') selected @endif>Return - Delivery Unsuccessful</option>
                                    <option value="Return - Delivered to Shipper" @if(request()->get('order-status') == 'Return - Delivered to Shipper') selected @endif>Return - Delivered to Shipper</option>
                                    <option value="Return - Not Attempted" @if(request()->get('order-status') == 'Return - Not Attempted') selected @endif>Return - Not Attempted</option>
                                    <option value="Return - On Hold" @if(request()->get('order-status') == 'Return - On Hold') selected @endif>Return - On Hold</option>
                                    <option value="Replacement - In Transit" @if(request()->get('order-status') == 'Replacement - In Transit') selected @endif>Replacement - In Transit</option>
                                    <option value="Replacement - Arrived at Origin" @if(request()->get('order-status') == 'Replacement - Arrived at Origin') selected @endif>Replacement - Arrived at Origin</option>
                                    <option value="Replacement - Dispatched" @if(request()->get('order-status') == 'Replacement - Dispatched') selected @endif>Replacement - Dispatched</option>
                                    <option value="Replacement - Delivery Unsuccessful" @if(request()->get('order-status') == 'Replacement - Delivery Unsuccessful') selected @endif>Replacement - Delivery Unsuccessful</option>
                                    <option value="Replacement - Delivered to Shipper" @if(request()->get('order-status') == 'Replacement - Delivered to Shipper') selected @endif>Replacement - Delivered to Shipper</option>
                                </select>
                            </div> -->

                            <!-- filter by name and city -->
                            <div class="col-lg-4 mb-2">
                                <label class="pb-0">Search</label><input type="search" value="{{request()->get('search_input')}}" class="me-2 form-control" name="search_input" placeholder="Order-Id /phone /name">
                            </div>

                            <!-- filter whatsapp -->
                            <div class="col-lg-4 mb-2">
                                <label class="pb-0">Whatsapp</label><input type="search" value="{{request()->get('whatsapp')}}" class="me-2 form-control" name="whatsapp" placeholder="User Whatsapp...">
                            </div>

                            <div class="col-lg-auto col-6 mb-2">
                                <label>From</label><input type="date" value="{{request()->get('fromDate')}}" class="me-2 form-control" name="fromDate" max="<?php echo date("Y-m-d"); ?>">
                            </div>
                            <div class="col-lg-auto col-6 mb-2">
                                <label>To</label><input type="date" value="{{request()->get('toDate')}}" class="form-control" name="toDate" max="<?php echo date("Y-m-d"); ?>">
                            </div>
                            <div class="col-auto mt-3">
                                <a class="btn btn-secondary btn-sm" id="reset-button">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                </a>
                                <button class="btn btn-primary btn-sm " type="submit">Filter Order
                                    <span class="d-none" id="filterSpecificIds"></span>
                                </button>
                                <input type="hidden" class="filterOrderIds" name="filterOrderIds">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end filter records -->

            <!-- Display Records -->
            
            */ ?>
            <div class="col-12">
              @if(count($records)>0)
              <table class="table table-striped table-hover table-responsive active_table-" id="examples-">
                  <thead>
                      <tr>
                          <th>Order Id</th>
                          <th>Selected Option</th>
                          <th>Remakrs</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach($records as $record)
                      <tr>
                        <td class="text-right">{{ $record->id }}</td>
                        <td>
                          @if($record->re_attempt_advice_id == 1)
                            Re Attempt
                          @else
                            Return Back
                          @endif
                        </td>
                        <td>{{ $record->re_attempt_remarks }}</td>
                        <td>
                          <button class="btn btn-primary trigger-js"
                            data-url="{{ route('order.shipper.advice.done') }}"
                            data-ask-confirmation="true"
                            data-order-id="{{$record->id}}"
                            >Done</button>  
                        </td>
                      </tr>
                      @endforeach
                  </tbody>
              </table>
              {!! $records->appends(request()->all())->links() !!}
              @else
                <div class="alert alert-warning text-white">No Shipper Advice Available</div>
              @endif
          </div>
            <!-- end records display -->
        </div>
        <!-- End Row -->
    </div>
</div>
@endsection
