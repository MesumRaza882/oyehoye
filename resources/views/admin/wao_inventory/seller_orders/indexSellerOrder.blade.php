@extends('admin.layouts.app')
@section('title')
    Orders
@endsection
@section('content')
    <style>
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

        .table tr td {
            text-align: center;
        }

        @media (max-width: 576px) {
            .head_order p {
                font-size: 13px;
            }
        }
    </style>

    <div class="main-content">
        <div class="container">
            <div class="row">


                @include('include.modal.orderHistoryModal')

                <!-- filter records -->
                <div class="row">
                    <!-- total record count -->
                    <div class="col-sm-auto col-6 head_order">
                        <p class="p-sm-2 p-1 bg-primary text-white">Orders:<span
                                class="ms-1">{{ number_format($total_records, 0, '.', ',') }}</span></p>
                    </div>
                    @if (auth()->user()->role != 4)
                        <div class="col-sm-auto col-6 head_order">
                            <p
                                class="mx-1 border p-sm-2 p-1 fw-bold {{ $orders->sum('grandProfit') >= 0 ? 'border-success' : 'border-danger' }}">
                                {{ $orders->sum('grandProfit') >= 0 ? 'Profit' : 'Loss' }}:
                                <span class="ms-1 badge bg-primary p-sm-2 p-1"
                                    style="font-size:14px;">{{ number_format($orders->sum('grandProfit'), 0, '.', ',') }}</span>
                            </p>
                        </div>

                        <div class="col-sm-auto col-auto head_order">
                            <p class="me-1 p-sm-2 p-1 fw-bold  border border-secondary">
                                Grand Total:
                                <span class="ms-1 badge bg-primary p-sm-2 p-1"
                                    style="font-size:14px;">{{ number_format($orders->sum('grandTotal'), 0, '.', ',') }}</span>
                            </p>
                        </div>

                        <div class="col-sm-auto col-auto head_order">
                            <p class="me-1 p-sm-2 p-1 fw-bold  border border-secondary">
                                DC:
                                <span class="ms-1 badge bg-primary p-sm-2 p-1"
                                    style="font-size:14px;">{{ number_format($orders->sum('charges'), 0, '.', ',') }}</span>
                            </p>
                        </div>
                    @endif

                    <!-- Get current status orders -->
                    <div class="col-sm-auto col-6 head_order ms-auto">
                        <a href="{{ route('waoseller.order.create') }}" class="btn btn-warning">Add Order</a>
                    </div>
                    {{-- delete order --}}
                    <div class="col-auto mb-sm-0">
                        <button class="ms-2 btn  btn-danger d-none deleteAllbtnOrders" id="deleteAllbtnOrders"></button>
                    </div>

                    <div class="col-12 py-0 my-0">
                        <hr>
                    </div>


                    <!-- filter -->
                    <div class="col-12 mb-2">
                        <form method="GET" action="{{ route('waoseller.order.index') }}" id="search-form">@csrf
                            <div class="row">
                                <!-- select records -->
                                <div class="col-lg-3 mb-2 col-6">
                                    <label class="pb-0">Select Records</label>
                                    <select name="records" class="form-control me-2" required>
                                        <option value="50" @if (request()->get('records') == 50) selected @endif>50</option>
                                        <option value="100" @if (request()->get('records') == 100) selected @endif>100
                                        </option>
                                        <option value="200" @if (request()->get('records') == 200) selected @endif>200
                                        </option>
                                        <option value="300" @if (request()->get('records') == 300) selected @endif>300
                                        </option>
                                        <option value="500" @if (request()->get('records') == 500) selected @endif>500
                                        </option>
                                    </select>
                                </div>

                                <!-- select Status -->
                                <div class="col-lg-3 mb-2 col-6">
                                    <label class="pb-0">Select Status</label>
                                    <select name="status" class="form-control me-2">
                                        <option value="">All</option>
                                        <option value="PENDING" @if (request()->get('status') == 'PENDING') selected @endif>PENDING
                                        </option>
                                        <option value="DISPATCHED" @if (request()->get('status') == 'DISPATCHED') selected @endif>
                                            DISPATCHED</option>
                                        <option value="DELIVERED" @if (request()->get('status') == 'DELIVERED') selected @endif>
                                            DELIVERED</option>
                                        <option value="ON-THE-WAY" @if (request()->get('status') == 'ON-THE-WAY') selected @endif>
                                            ON-THE-WAY</option>
                                        <option value="RETURNED" @if (request()->get('status') == 'RETURNED') selected @endif>RETURNED
                                        </option>
                                        <option value="Team Review your Order"
                                            @if (request()->get('status') == 'Team Review your Order') selected @endif>Team Review your Order
                                        </option>
                                        <option value="CANCEL" @if (request()->get('status') == 'CANCEL') selected @endif>CANCEL
                                        </option>
                                        <option value="CANCIL" @if (request()->get('status') == 'CANCIL') selected @endif>CANCIL
                                            Orders By Admin</option>
                                        <option value="Shipment - Booked"
                                            @if (request()->get('order-status') == 'Shipment - Booked') selected @endif>Shipment - Booked</option>
                                        <option value="Re-Booked" @if (request()->get('status') == 'Re-Booked') selected @endif>
                                            Re-Booked</option>
                                        <option value="Shipment - Arrived at Origin"
                                            @if (request()->get('status') == 'Shipment - Arrived at Origin') selected @endif>Shipment - Arrived at Origin
                                        </option>
                                        <option value="Shipment - In Transit"
                                            @if (request()->get('status') == 'Shipment - In Transit') selected @endif>Shipment - In Transit
                                        </option>
                                        <option value="Shipment - Arrived at Destination"
                                            @if (request()->get('status') == 'Shipment - Arrived at Destination') selected @endif>Shipment - Arrived at
                                            Destination</option>
                                        <option value="Shipment - Out for Delivery"
                                            @if (request()->get('status') == 'Shipment - Out for Delivery') selected @endif>Shipment - Out for Delivery
                                        </option>
                                        <option value="Shipment - Return Confirmation Pending"
                                            @if (request()->get('status') == 'Shipment - Return Confirmation Pending') selected @endif>Shipment - Return
                                            Confirmation Pending</option>
                                        <option value="Shipment - Re-Attempt Requested"
                                            @if (request()->get('status') == 'Shipment - Re-Attempt Requested') selected @endif>Shipment - Re-Attempt
                                            Requested</option>
                                        <option value="Shipment - Rider Picked"
                                            @if (request()->get('status') == 'Shipment - Rider Picked') selected @endif>Shipment - Rider Picked
                                        </option>
                                        <option value="Shipment - Misroute Forwarded"
                                            @if (request()->get('status') == 'Shipment - Misroute Forwarded') selected @endif>Shipment - Misroute
                                            Forwarded</option>
                                        <option value="Shipment - Re-Attempt"
                                            @if (request()->get('status') == 'Shipment - Re-Attempt') selected @endif>Shipment - Re-Attempt
                                        </option>
                                        <option value="Shipment - Delivered"
                                            @if (request()->get('status') == 'Shipment - Delivered') selected @endif>Shipment - Delivered
                                        </option>
                                        <option value="Shipment - Cancelled"
                                            @if (request()->get('status') == 'Shipment - Cancelled') selected @endif>Shipment - Cancelled
                                        </option>
                                        <option value="Return - Confirm"
                                            @if (request()->get('status') == 'Return - Confirm') selected @endif>Return - Confirm</option>
                                        <option value="Return - In Transit"
                                            @if (request()->get('status') == 'Return - In Transit') selected @endif>Return - In Transit</option>
                                        <option value="Return - Arrived at Origin"
                                            @if (request()->get('status') == 'Return - Arrived at Origin') selected @endif>Return - Arrived at Origin
                                        </option>
                                        <option value="Return - Dispatched"
                                            @if (request()->get('status') == 'Return - Dispatched') selected @endif>Return - Dispatched</option>
                                        <option value="Return - Delivery Unsuccessful"
                                            @if (request()->get('status') == 'Return - Delivery Unsuccessful') selected @endif>Return - Delivery
                                            Unsuccessful</option>
                                        <option value="Return - Delivered to Shipper"
                                            @if (request()->get('status') == 'Return - Delivered to Shipper') selected @endif>Return - Delivered to
                                            Shipper</option>
                                        <option value="Return - Not Attempted"
                                            @if (request()->get('status') == 'Return - Not Attempted') selected @endif>Return - Not Attempted
                                        </option>
                                        <option value="Return - On Hold"
                                            @if (request()->get('status') == 'Return - On Hold') selected @endif>Return - On Hold</option>
                                        <option value="Replacement - In Transit"
                                            @if (request()->get('status') == 'Replacement - In Transit') selected @endif>Replacement - In Transit
                                        </option>
                                        <option value="Replacement - Arrived at Origin"
                                            @if (request()->get('status') == 'Replacement - Arrived at Origin') selected @endif>Replacement - Arrived at
                                            Origin</option>
                                        <option value="Replacement - Dispatched"
                                            @if (request()->get('status') == 'Replacement - Dispatched') selected @endif>Replacement - Dispatched
                                        </option>
                                        <option value="Replacement - Delivery Unsuccessful"
                                            @if (request()->get('status') == 'Replacement - Delivery Unsuccessful') selected @endif>Replacement - Delivery
                                            Unsuccessful</option>
                                        <option value="Replacement - Delivered to Shipper"
                                            @if (request()->get('status') == 'Replacement - Delivered to Shipper') selected @endif>Replacement - Delivered to
                                            Shipper</option>
                                    </select>
                                </div>

                                <!-- order Type -->
                                <div class="col-lg-3 mb-2 col-6">
                                    <label class="pb-0">Orders Type</label>
                                    <select name="tracking_order_type" class="form-control me-2">
                                        <option value="">All</option>
                                        <option value="trax" @if (request()->get('tracking_order_type') == 'trax') selected @endif>TRAX
                                        </option>
                                        <option value="mnp" @if (request()->get('tracking_order_type') == 'mnp') selected @endif>MNP
                                        </option>
                                        <option value="postEx" @if (request()->get('tracking_order_type') == 'postEx') selected @endif>Post-Ex
                                        </option>
                                    </select>
                                </div>

                                <!-- seller -->
                                @if (auth()->user()->role === 1 && auth()->user()->id != 58)
                                    <div class="col-lg-3 mb-2 col-6">
                                        <label class="pb-0">Select Seller</label>
                                        <select name="wao_seller_id" class="form-control me-2">
                                            <option value="">All</option>
                                            @foreach ($admins as $seller)
                                                <option value="{{ $seller->id }}"
                                                    @if (request()->get('wao_seller_id') == $seller->id) selected @endif>{{ $seller->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <!-- filter by name and city -->
                                <div class="col-lg-3 col-12 mb-2">
                                    <label class="pb-0">Search</label><input type="search"
                                        value="{{ request()->get('search_input') }}" class="me-2 form-control"
                                        name="search_input" placeholder="Order-Id /phone /name">
                                </div>

                                <!-- filter whatsapp -->
                                <div class="col-lg-3 col-12 mb-2">
                                    <label class="pb-0">Whatsapp</label><input type="search"
                                        value="{{ request()->get('whatsapp') }}" class="me-2 form-control"
                                        name="whatsapp" placeholder="User Whatsapp...">
                                </div>

                                <div class="col-lg-2 col-6 mb-2">
                                    <label>From</label><input type="date" value="{{ request()->get('fromDate') }}"
                                        class="me-2 form-control" name="fromDate" max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-lg-2 col-6 mb-2">
                                    <label>To</label><input type="date" value="{{ request()->get('toDate') }}"
                                        class="form-control" name="toDate" max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-auto my-2 mt-lg-3">
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

                        <a href="{{ route('waoseller.order.index', ['get_cancel_to_return_orders' => 1]) }}"
                            class="btn btn-sm btn-info">Cancel to return orders</a>

                    </div>
                </div>
                <!-- end filter records -->

                <!-- Display Records -->
                <div class="col-12">
                    @if (count($orders) > 0)
                        <table class="table table-striped table-hover table-responsive  active_table" id="examples">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" value="{{ request()->get('main_checkbox') }}"
                                            name="main_checkbox" style="background-color: aquamarine"></th>
                                    <th>id</th>
                                    <th>Nmae</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th>Items</th>
                                    <th>Articles</th>
                                    <th>AMOUNT + DC</th>
                                    <!-- <th>Profit</th> -->
                                    <th>Whatsapp</th>
                                    <th>Order-Dtae</th>
                                    <th>delivery_status_time</th>
                                    <th>Dispatched-By</th>
                                    <th>Payemnt-ScreenShot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $rate)
                                    <tr class="{{ $rate->is_active_row == 1 ? 'highlight' : '' }}">
                                        <td><input
                                                type="{{ $rate->status == 'Team Review your Order' || !$rate->courier_tracking_id ? 'checkbox' : 'hidden' }}"
                                                value="{{ $rate->id }}" name="cat_checkbox"></td>
                                        <td>
                                            @if ($rate->is_warehouseTeam_order && $rate->wao_seller_id === auth()->user()->id)
                                                <span class="fw-bold">{{ $rate->id }}</span>
                                            @else
                                                <a href="{{ route('editOrder', $rate->id) }}"
                                                    class="btn btn-outline-primary text-dark">{{ $rate->id }}</a>
                                                @if ($rate->slip)
                                                    <a target="_blank" href="{{ asset('slip/' . $rate->slip) }}"
                                                        class="mt-1">
                                                        <img src="{{ asset('slip/' . $rate->slip) }}" alt="slip"
                                                            width="35px" height="35px">
                                                    </a>
                                                @else
                                                    <a href="{{ route('makeOrderSlip', $rate->id) }}"
                                                        class=" mt-1 btn btn-outline-info btn-sm text-dark">Slip</a>
                                                @endif
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{ route('singleUser', $rate->userdetail->id) }}"
                                                class="fw-bold text-dark">{{ $rate->name }}</a>
                                            <span
                                                class="badge {{ $rate->userdetail->status == 1 ? 'bg-danger' : 'd-none' }}">
                                                {{ $rate->userdetail->status == 1 ? 'Blocked' : '' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $rate->citydetail ? ($rate->citydetail->c_city_name ? $rate->citydetail->c_city_name : ($rate->citydetail->name ? $rate->citydetail->name : $rate->citydetail->postex)) : $rate->city }}
                                            </span><br>
                                            <!-- not for warehouse team member -->
                                            @if ($rate->is_warehouseTeam_order && $rate->wao_seller_id === auth()->user()->id)
                                            @else
                                                <span>
                                                    @php
                                                        // Calculate the time difference in hours
                                                        $currentTime = now();
                                                        $orderCreationTime = $rate->created_at;
                                                        $hoursDifference = $currentTime->diffInHours(
                                                            $orderCreationTime,
                                                        );
                                                    @endphp
                                                    @if ($hoursDifference >= 120)
                                                        <form class="cancelOrderForm" method="POST"
                                                            action="{{ route('delOrder', $rate->id) }}">@csrf
                                                            <!-- <a href="{{ route('editOrder', $rate->id) }}" class="btn btn-outline-primary text-dark">Edit</a> -->
                                                            <!-- <button @if (
                                                                ($rate->status != 'Team Review your Order' || $rate->status != 'DISPATCHED' || $rate->status != 'Dispatched') &&
                                                                    $rate->is_cancel === 1) disabled @endif
                                                    onclick="return confirm('Are you sure to Cancel Order?');"
                                                    type="submit"
                                                    class="btn btn-sm
                                            @if ($rate->is_cancel != 1) btn-success
                                            @else
                                            btn-danger @endif
                                            cancelOrderButton"
                                                    >
                                                    Cancel
                                                </button> -->

                                                            <button
                                                                @if (
                                                                    ($rate->status === 'Team Review your Order' || $rate->status === 'DISPATCHED' || $rate->status === 'Dispatched') &&
                                                                        $rate->is_cancel != 1) @else
                                            disabled @endif
                                                                type="submit"
                                                                class=" btn
                                            {{-- @if ($rate->is_cancel != 1) --}}
                                            @if ($rate->status === 'Team Review your Order' || $rate->status === 'DISPATCHED' || $rate->status === 'Dispatched') && $rate->is_cancel != 1)
                                            btn-success
                                            @else
                                            btn-danger @endif
                                            btn-sm cancelOrderButton">Cancel</button>
                                                        </form>
                                                    @else
                                                        <span
                                                            class="fw-bold text-danger">{{ 120 - $hoursDifference }}</span>
                                                        <span> hours remaining to cancel</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold {{ $rate->status == 'CANCEL' ? 'text-danger' : '' }}"
                                            style="@if ($rate->status == 'DISPATCHED') color:green; @endif">
                                            <!-- tempoaray for admin  -->
                                            <span
                                                class="status-cell {{ $rate->is_warehouseTeam_order && $rate->wao_seller_id === auth()->user()->id && $rate->courier_tracking_id != null ? 'text-success' : '' }}">

                                                {{ $rate->is_warehouseTeam_order && $rate->wao_seller_id === auth()->user()->id ? ($rate->courier_tracking_id != null ? $rate->status : 'Dispatch By Admin') : $rate->status }}
                                            </span>
                                            <div
                                                class="d-flex mt-1 {{ $rate->courier_tracking_id == null ? 'd-none' : '' }}">
                                                <span
                                                    class="d-none badge track_number{{ $rate->id }}">{{ $rate->courier_tracking_id }}
                                                </span>
                                                <span
                                                    class="p-1 py-2  bg-info badge {{ $rate->courier_tracking_id == null ? 'd-none' : '' }}"
                                                    style="font-size:14px;">{{ substr($rate->courier_tracking_id, 0, 4) . '...' . substr($rate->courier_tracking_id, -4) }}
                                                </span>
                                                <button onclick="copy_track_number('{{ $rate->id }}')"
                                                    class="btn btn-sm btn-warning {{ $rate->courier_tracking_id == null ? 'd-none' : '' }}"><i
                                                        class="fa fa-edit"></i></button>
                                            </div>
                                        </td>

                                        <td>
                                            @if ($rate->is_multan_items_contain === 1)
                                                <span class="badge bg-primary mb-1">Multan-Items</span>
                                            @endif
                                            <span>{{ $rate->orderitems->sum('qty') }}</span>
                                            <span
                                                class="viewReturnConfirmMessag{{ $rate->id }} {{ $rate->is_returned_order === 2 ?: 'd-none' }} badge bg-success p-1">Returned<i
                                                    class="ms-1 fa-solid fa-clipboard-check"></i></span>
                                            <button
                                                class="{{ $rate->is_returned_order === 1 ? 'd-block' : 'd-none' }} m-auto p-1 btn btn-sm btn-warning text-white confirm_order_return"
                                                value="{{ $rate->id }}">
                                                Is Return ?
                                            </button>
                                        </td>

                                        <td>
                                            @foreach ($rate->orderitems as $item)
                                                {{ $item->product->article }}<span
                                                    class="{{ $item->qty > 1 ? '' : 'd-none' }}">( x
                                                    {{ $item->qty }})</span>,
                                            @endforeach
                                        </td>


                                        <td>Rs: <span class="fw-bold">{{ $rate->amount }} </span> + Rs: <span
                                                class="fw-bold">{{ $rate->charges }} </span></td>
                                        <td>
                                            @php
                                                $whatsappNumber = $rate->userdetail->whatsapp;
                                                $encodedMessage = urlencode(
                                                    "https://alzulfiqar110.com/index.php?input=$whatsappNumber",
                                                );
                                                $whatsappLink = "https://wa.me/+92$whatsappNumber?text=$encodedMessage";
                                            @endphp
                                            <a href="{{ $whatsappLink }}" target="_blank"
                                                class="text-dark btn-outline-success btn">
                                                {{ $whatsappNumber }}
                                            </a>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ $rate->created_at->format('d') }}
                                                {{ $rate->created_at->format('M') }}
                                                {{ $rate->created_at->format('Y') }}</span>
                                            <span class="fw-bold">{{ $rate->time }}</span>
                                        </td>

                                        <td>
                                            <span
                                                class="fw-bold courierDate">{{ $rate->courier_date == null ? '' : $rate->courier_date }}</span>

                                            @if ($rate->tracking_order_type === 'trax')
                                                <a data-order-id="{{ $rate->id }}"
                                                    data-track-route="{{ route('trackSingleOrderApi') }}"
                                                    class="btn btn-outline-dark track-order-btn">History</a>
                                            @elseif ($rate->tracking_order_type === 'mnp')
                                                <a data-order-id="{{ $rate->id }}"
                                                    data-track-route="{{ route('trackSingleOrderApi') }}"
                                                    class="btn btn-outline-info track-mnp-order-btn">History</a>
                                            @elseif ($rate->tracking_order_type === 'postEx')
                                                <a data-order-id="{{ $rate->id }}"
                                                    data-track-route="{{ route('trackSingleOrderApi') }}"
                                                    class="btn btn-outline-dark track-postEx-order-btn">History</a>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $rate->waoSellerDetail->email }}
                                        </td>
                                        <td>
                                            <form method="POST" enctype="multipart/form-data"
                                                action="{{ route('uploadscreenShot') }}">
                                                @csrf
                                                <!-- Hidden input to pass the order ID -->
                                                <input type="hidden" name="order_id" value="{{ $rate->id }}">

                                                @if ($rate->payment_screenshot)
                                                    <!-- Display the existing image and a label for re-uploading -->
                                                    <div class="uploaded-image">
                                                        <a href="{{ $rate->payment_screenshot }}" target="_blank">
                                                            <img src="{{ $rate->payment_screenshot }}"
                                                                alt="Payment Image" height="60px" width="60px" />
                                                        </a>
                                                        <label for="image-upload-{{ $rate->id }}"
                                                            class="btn btn-sm text-white btn-primary mt-2"><i
                                                                class="fa-solid fa-file"></i> Update</label>
                                                        <input type="file" name="payment_screenshot" accept="image/*"
                                                            class="d-none image-upload"
                                                            id="image-upload-{{ $rate->id }}">
                                                    </div>
                                                @else
                                                    <!-- Display a label for uploading a new image -->
                                                    <label for="image-upload-{{ $rate->id }}"
                                                        class="btn btn-sm text-white btn-info"><i
                                                            class="fa-solid fa-file"></i> Upload</label>
                                                    <input type="file" name="payment_screenshot" accept="image/*"
                                                        class="d-none image-upload"
                                                        id="image-upload-{{ $rate->id }}">
                                                @endif
                                            </form>
                                        </td>
                                        @section('returnConfirmOrder_modal_footer')
                                            <form method="POST" id="confirm_order_return_form">
                                                @csrf
                                                <input hidden type="text" id="confirm_order_return_id"
                                                    name="confirm_order_return_id">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                                        class="fa fa-window-close" aria-hidden="true"></i></button>
                                                <button type="submit" class="btn btn-success"><i
                                                        class="fa-solid fa-clipboard-check"></i></button>
                                            </form>
                                        @endsection
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $orders->appends(request()->all())->links() !!}
                    @else
                        <div class="alert alert-warning text-white">No Orders Record Available</div>
                    @endif
                </div>
                <!-- end records display -->
            </div>
            <!-- End Row -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Attach the onchange event listener to all file inputs with the class "image-upload" on orders listing
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.image-upload').forEach(function(input) {
                input.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });

        //   soldout status
        function copy_track_number($id) {
            var id = $id;
            // Get the text field
            var track_number_field = $('.track_number' + id).text();

            var temp = $("<input>");
            $("body").append(temp);
            temp.val(track_number_field).select();
            document.execCommand("copy");
            temp.remove();
            toastr.success("Copied Successfully");
        }

        $(document).ready(function() {
            // delete slected items
            $(document).on('click', '.filterSpecificOrders', function(e) {
                e.preventDefault();

                var allids = [];
                $('input[name="cat_checkbox"]:checked').each(function() {
                    allids.push($(this).val());
                    $('.testArray').val(allids);
                });

            });

            //confirm return order
            $(document).on('click', '.confirm_order_return', function(e) {
                e.preventDefault();

                var $clickedButton = $(this);
                var id = $clickedButton.val();
                $('#confirm_order_return_id').val(id);
                var $viewReturnConfirmMessag = $('.viewReturnConfirmMessag' + id);
                $('#ConfirmOrderModalRecord').modal('show');

                // Delete form submit
                $(document).off('submit', '#confirm_order_return_form');

                //delet form submit
                $(document).on('submit', '#confirm_order_return_form', function(e) {
                    e.preventDefault();

                    let formdata = new FormData($('#confirm_order_return_form')[0]);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('confirm_order_return') }}",
                        data: formdata,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            console.log(response);
                            if (response.status == 2) {
                                toastr.success(response.data);
                                $viewReturnConfirmMessag.removeClass('d-none');
                                $clickedButton.removeClass('d-block');
                                $clickedButton.addClass('d-none');
                                $('#ConfirmOrderModalRecord').modal('hide');
                            }
                            if (response.status == 1) {
                                toastr.error(response.data);
                            }
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                });
            });

            // cancel order by reseller/admin
            // $(document).on('click', '.cancelOrderButton', function(e) {
            //     e.preventDefault();
            //     var button = $(this);
            //     var form = $(this).closest('form');
            //     Swal.fire({
            //         title: 'Are you sure to Cancel Order?',
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonText: 'Yes, cancel it!',
            //         cancelButtonText: 'No, keep it'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             button.prop('disabled', true);
            //             form.submit();
            //         }
            //     });
            // });
        });
    </script>
@endsection
