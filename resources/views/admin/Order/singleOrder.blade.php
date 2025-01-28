<?php

use App\Helpers\Helper;
?>
@extends('admin.layouts.app')
@section('content')
@section('title')
    Single Order
@endsection


<div class="main-content">
    <!-- Message Modal -->
    @section('modal_header')
        <h5 class="modal-title" id="exampleModalLabel">Message Records</h5>
    @endsection

    @section('modal_body')
        @include('admin.Order.message-form')
    @endsection
    <!-- End Modal -->

    <!-- dispatched Trax Modal -->
    @section('dispatched_modal_header')
        <h5 class="modal-title" id="exampleModalLabel">Order Trax-Dispatched</h5>
    @endsection

    @section('dispatched_modal_body')
        @include('admin.Order.trax-order-form')
    @endsection
    <!-- End dispatched Trax Modal -->


    <!-- dispatched mnp Modal -->
    @section('dispatched_mnp_modal_header')
        <h5 class="modal-title" id="exampleModalLabel">Order MNP-Dispatched</h5>
    @endsection

    @section('dispatched_mnp_modal_body')
        @include('admin.Order.mnp-order-form')
    @endsection
    <!-- End dispatched mnp Modal -->

    <!-- dispatched postEx Modal -->
    @section('dispatched_postEx_modal_header')
        <h5 class="modal-title" id="exampleModalLabel">Order PostEx-Dispatched</h5>
    @endsection

    @section('dispatched_postEx_modal_body')
        @include('admin.Order.postEx-order-form')
    @endsection
    <!-- End dispatched mnp Modal -->

    <!-- deduct balance modal -->
    <!-- Modal -->
    <div class="modal fade" id="deductResellerBalance" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="deductResellerBalanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deductResellerBalanceLabel">Balance Deduct Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0">
                    <div class="row">
                        <div class="col-12">
                            <p class="alert alert-primary">Amount deduct from Balance for Order</p>
                            <div class="card card-body">
                                <h6 style="font-weight: 700;">Basic Products Price : <span id="basicProductCount"
                                        style="font-weight: 16px;">({{ $order->orderitems->sum('qty') }}x)</span> =
                                    <span style="font-weight: 800;"
                                        class="text-warning">{{ $purchaseTotal - $perOrderCharge - $order->orderitems->sum('qty') * $perProductCharge }}</span>
                                </h6>
                                <h6 class="mb-2" style="font-weight: 700;">Per Product Charges: <span
                                        id="perProductCount"
                                        style="font-weight: 800;">{{ $order->orderitems->sum('qty') }} X
                                        {{ $perProductCharge }}</span> = <span id="perProductAmount"
                                        style="font-weight: 800;"
                                        class="text-warning">{{ $order->orderitems->sum('qty') * $perProductCharge }}</span>
                                </h6>
                                <h6 class="mb-2" style="font-weight: 700;">Order Charges: <span
                                        style="font-weight: 800;" class="text-warning"
                                        id="orderCharges">{{ $perOrderCharge }}</span></h6>
                                <h6 class="mb-0 pb-0" class="text-center border border-primary">Total Amount : <span
                                        class="text-warning" id="totalAmountPurchse">{{ $purchaseTotal }}</span></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

       <!-- whiteList products mark -->
       @include('include.modal.whiteListModalOrderEdit')
       <!-- end -->


    <div class="container">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <section class="section">
                    <div class="section-body">
                        <div class="card">
                            <form method="POST" enctype="multipart/form-data"
                                action="{{ route('updateOrder', $order->id) }}">
                                @csrf
                                <!--card Body-->
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto">
                                            <a href="javascript:history.go(-1)"
                                                class="btn btn-sm text-white btn-primary">
                                                <i class="fa-solid fa-arrow-left"></i>
                                            </a>
                                            <p class="d-inline mx-2">
                                                <span class="border border-success p-1">{{ $order->id }}</span>
                                                <button type="button" class="btn btn-sm  btn-primary" data-value="whiteItems" data-bs-toggle="modal"
                                                    data-bs-target="#productModal">
                                                    <i class="fa fa-edit"></i>Items
                                                </button>
                                            </p>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <p>
                                                <span class="fw-bold">{{ $order->created_at->format('d') }}</span>
                                                <span class="px-1 mos">{{ $order->created_at->format('M') }}</span>
                                                <span class="yr">{{ $order->created_at->format('Y') }}</span>
                                            </p>
                                            <p class="badge bg-primary p-2 ms-1 text-white d-sm-block d-none">
                                                {{ $order->status }}</p>
                                        </div>
                                    </div>

                                    <!--order basic deatail-->
                                    <div class="row mt-3">
                                        <!--name-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-regular fa-id-badge"></i> Name</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    <a href="{{ route('singleUser', $order->userdetail->id) }}"
                                                        class="btn btn-outline-primary btn-sm">{{ $order->name }}</a>
                                                    <span
                                                        class="badge {{ $order->userdetail->status == 1 ? 'bg-danger' : 'd-none' }}">
                                                        {{ $order->userdetail->status == 1 ? 'Blocked' : '' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <!--city based on id remain-->

                                        <div class="col-sm-2 col-auto">
                                            <div class="form-group">
                                                <label> <i class="me-1 fa-solid fa-city"></i> City</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    {{ $order->citydetail ? $order->citydetail->c_city_name : $order->city }}
                                                </p>
                                            </div>
                                        </div>
                                        <!--address-->
                                        <div class="col-lg-4 col-12 col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-solid fa-address-card"></i> Address</label>
                                                <textarea style=" min-height: 8em; overflow: auto;" required name="address" class=" form-control">{{ $order->address }}</textarea>
                                            </div>
                                        </div>
                                        <!--phone-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-brands fa-whatsapp fs-6"></i> Whatsapp</label>
                                                <p class="pb-1 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    <a href="https://wa.me/+92{{ $order->userdetail->whatsapp }}?text=Hi%20Welcome%20to%20WAO"
                                                        target="_blank" class=" pb-0 btn text-success fw-bold">
                                                        {{ $order->userdetail->whatsapp }}
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <!--corier contact number-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-solid fa-phone"></i> Contact Number</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    {{ $order->phone }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!--order Notes-->
                                    @if (count($order->notes) > 0 || $order->note != null)
                                        <div class="row">
                                            <div class="pt-2 d-flex">
                                                <button class="border btn btn-sm btn-info view_notes ms-auto"><i
                                                        class="fa-solid fa-eye"></i> View Notes </button>
                                                <hr>
                                            </div>
                                            <!--Notes-->
                                            <div class="col-12 notes_div" style="display:none;">
                                                <table class="table table-bordered table-hover table-md-responsive">
                                                    <tbody class="text-center">
                                                        <tr class="{{ $order->note != null ? '' : 'd-none' }}">
                                                            <td>{{ $order->note }} - <span
                                                                    class="text-info">{{ $order->created_at->format('j M, Y \a\t g:i A') }}</span>
                                                            </td>
                                                        </tr>
                                                        @foreach ($order->notes as $note)
                                                            <tr>
                                                                <td>{{ $note->note }} - <span
                                                                        class="text-info">{{ $note->created_at->format('j M, Y \a\t g:i A') }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    <!--Users Previouus Orders-->
                                    @if (count($previous_orders) > 0)
                                        <div class="row my-2  align-items-start justify-content-end">

                                            <div class="col-auto">
                                                <button
                                                    class=" border btn border-success view_user_orders ms-auto btn-sm"><i
                                                        class="fa-solid fa-eye"></i> View Last 10 Orders</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--customer orders-->
                                            <div class="col-12 user_orders_div" style="display:none;">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>Status</th>
                                                                <th>Items</th>
                                                                <th>Amount</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($previous_orders as $pre_order)
                                                                <tr>

                                                                    <td
                                                                        class="{{ $pre_order->is_blocked_customer_order == 1 ? 'text-danger' : '' }}">
                                                                        {{ $pre_order->is_blocked_customer_order == 1 ? 'Fake' : 'Real' }}<br>
                                                                        <span
                                                                            class="fw-bold">{{ $pre_order->courier_tracking_id }}</span>
                                                                    </td>
                                                                    <td
                                                                        class="{{ $pre_order->status == 'DISPATCHED' ? 'text-success' : '' }}">
                                                                        <span
                                                                            class="fw-bold {{ $pre_order->status == 'CANCEL' ? 'text-danger' : '' }}">{{ $pre_order->status }}</span>
                                                                    </td>
                                                                    <td><span
                                                                            class="fw-bold">{{ $pre_order->orderitems_count }}</span>
                                                                    </td>
                                                                    <td>{{ $pre_order->grandTotal }} /Rs</td>
                                                                    <td>
                                                                        <span
                                                                            class="fw-bold">{{ $pre_order->date }}</span>
                                                                        <span
                                                                            class="text-secondary ps-2">{{ $pre_order->time }}</span>
                                                                    </td>

                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!--order items Deatils-->
                                    <div class="row">
                                        <div class="py-2">
                                            <h5 class="mb-4">Order Items Deatils
                                                <a style="cursor: none;"
                                                    class="btn btn-outline-primary mx-1 py-1 mt-2 mt-sm-0 btn-sm">
                                                    Order-Items<span
                                                        class="badge rounded-pill bg-light text-dark">{{ count($order->orderitems) }}
                                                    </span>
                                                </a>
                                                <a style="cursor: none;"
                                                    class="btn btn-outline-secondary mx-1 py-1 mt-2 mt-sm-0 btn-sm fw-bold">
                                                    Items-Quantity<span
                                                        class="badge rounded-pill bg-light text-success">{{ $order->orderitems->sum('qty') }}
                                                    </span>
                                                </a>
                                            </h5>
                                            <hr>
                                        </div>
                                        <div class="col-sm-12 ">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Price</th>
                                                            <th>Purchase Price</th>
                                                            <th>Profit</th>
                                                            <th>Discount</th>
                                                            <th>Bill Per Item</th>
                                                            <th>Profit Per Item</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order->orderitems as $item)
                                                            <tr>
                                                                <td
                                                                    class="d-lg-flex py-lg-0 py-2 align-items-center position-relative">
                                                                    <!-- delete record modal -->
                                                                    @section('delete_modal_footer')
                                                                        <form method="POST" id="delete_order_item_form">
                                                                            @csrf
                                                                            <input hidden type="text"
                                                                                id="delete_order_item_id"
                                                                                name="delete_order_item_id">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal"><i
                                                                                    class="fa fa-window-close"
                                                                                    aria-hidden="true"></i></button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger"><i
                                                                                    class="fa fa-trash"></i></button>
                                                                        </form>
                                                                    @endsection
                                                                    <!-- end Delete Modal -->
                                                                    <button
                                                                        class="btn btn-sm btn-danger text-white del_order_item"
                                                                        value="{{ $item->id }}">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                    <span class="px-2">
                                                                        <span class="fw-bold badge bg-primary"
                                                                            style="font-size:14px !important;">( x
                                                                            {{ $item->qty }})</span>
                                                                        {{ $item->product->name }}
                                                                    </span>
                                                                    <a href="{{ $item->product->thumbnail }}"
                                                                        target="_blank">
                                                                        <img src="{{ $item->product->thumbnail }}"
                                                                            height="50px" width="50px"
                                                                            alt="Item_Thumbnail"
                                                                            class="rounded-circle" />
                                                                    </a><br>
                                                                    <span
                                                                        class=" {{ $item->is_dc_free == 0 ? 'd-none' : '' }} position-absolute  translate-middle  bg-success border border-light rounded-circle"
                                                                        style="top:18%; padding:6px;"></span>
                                                                </td>
                                                                <td>Rs {{ $item->price }}</td>
                                                                <td>Rs {{ $item->purchase }}</td>
                                                                <td>Rs {{ $item->profit }}</td>
                                                                <td
                                                                    class="position-relative {{ $item->discount > 0 ? 'fw-bold text-warning' : '' }}">
                                                                    {{ $item->discount > 0 ? 'Rs: ' . $item->discount : '0' }}
                                                                    <span
                                                                        class="position-absolute badge bg-warning top-100 {{ $item->is_dc_free == 0 ? 'd-none' : '' }}">DC</span>
                                                                </td>
                                                                <td>
                                                                    <span class="fw-bold"> Rs:
                                                                        {{ $item->price * $item->qty }}</span>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $profit =
                                                                            $item->profit * $item->qty -
                                                                            $item->discount * $item->qty;
                                                                    @endphp
                                                                    <span
                                                                        class="fw-bold {{ $profit < 0 ? 'text-danger' : 'text-success' }}">
                                                                        Rs: {{ $profit }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!--Order Charges Bill-->
                                    @if (auth()->user()->role == 1)
                                        @can('view_order_amount_details')
                                            <!-- User manager and the specific permission can see this input -->
                                            @include('admin.Order.partials.order_charges')
                                        @endcan
                                    @else
                                        @include('admin.Order.partials.order_charges')
                                    @endif

                                    {{-- order charges end --}}

                                    {{-- order status --}}
                                    @if (!$order->is_warehouseTeam_order)
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <div class="h5">Proof of Payment</div>
                                                <div>{!! Helper::advance_payment_status($order->advance_payment_status) !!}</div>
                                                @if ($order->advance_payment_proof)
                                                    <a href="{{ $order->advance_payment_proof }}" target="_blank">
                                                        <img src="{{ $order->advance_payment_proof }}" height="200px"
                                                            width="100px" />
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="col-12 mt-2">
                                                <a href="{{ route('updatePaymentStatus', ['id' => $order->id, 'advance_payment_status' => 0]) }}"
                                                    class="btn btn-primary">Pending</a>
                                                <a href="{{ route('updatePaymentStatus', ['id' => $order->id, 'advance_payment_status' => 1]) }}"
                                                    class="btn btn-success">Approved</a>
                                                <a href="{{ route('updatePaymentStatus', ['id' => $order->id, 'advance_payment_status' => 2]) }}"
                                                    class="btn btn-danger">Rejected</a>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- order status end --}}

                                </div>
                                <!-- card Body -->


                                <!-- Foter submit Button and work -->
                                <div class="card-footer text-right pt-0">
                                    <!-- Order status Update -->
                                    <div class="row d-flex justify-content-between">
                                        <div class="col-auto text-start">
                                            <h5>Order Status</h5>
                                            <p
                                                class=" mx-2 fs-6 badge p-2 {{ $order->status == 'CANCEL' ? 'bg-danger' : ($order->status == 'DELIVERED' || $order->status == 'DISPATCHED' ? 'bg-success' : 'bg-primary') }}">
                                                <span class="fw-bold  ">{{ $order->status }}</span>
                                            </p>
                                            <div>
                                                <a @if ($currentDayOrder) onclick="confirmDispatch('{{ $currentDayOrder }}', 'dispatched')" @else data-bs-toggle="modal" data-bs-target="#dispatched" @endif
                                                    class="dec_para_fun btn btn-sm mb-sm-3 mb-2 text-white btn-primary
                                        {{ $order->tracking_order_type != '' || $order->status == 'DELIVERED' || $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                    TRAX-DISPATCH
                                                </a>


                                                <a @if ($currentDayOrder) onclick="confirmDispatch('{{ $currentDayOrder }}', 'dispatched-mnp')" @else data-bs-toggle="modal" data-bs-target="#dispatched-mnp" @endif
                                                    class="
                                        dec_para_fun btn btn-sm mb-sm-3 mb-2 text-white btn-info
                                        {{ $order->tracking_order_type != '' || $order->status == 'DELIVERED' || $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                    MNP-DISPATCH
                                                </a>
                                                @if ($order->is_reseller_order == 1)
                                                    {{-- if user reseller has not postex_address_code --}}
                                                    @if (!empty($order->userDetail->bussiness_detail->postex_address_code))
                                                        <a @if ($currentDayOrder) onclick="confirmDispatch('{{ $currentDayOrder }}', 'dispatched-postEx')" @else data-bs-toggle="modal" data-bs-target="#dispatched-postEx" @endif
                                                            class="
                                                        dec_para_fun btn btn-sm mb-sm-3 mb-2 text-white btn-dark
                                                        {{ $order->tracking_order_type != '' || $order->status == 'DELIVERED' || $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                            PostEx-DISPATCH
                                                        </a><br>
                                                        <span class="badge bg-success px-2 mb-2">
                                                            Post-Ex address available
                                                            <a href="{{ route('singleUser', ['id' => $order->user_id]) }}"
                                                                target="_blank" class="btn btn-primary">User
                                                                Detail</a>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger px-2 mb-2">
                                                            Post-Ex address code not found
                                                            <a href="{{ route('singleUser', ['id' => $order->user_id]) }}"
                                                                target="_blank" class="btn btn-primary">User
                                                                Detail</a>
                                                        </span>
                                                    @endif
                                                @else
                                                    <a @if ($currentDayOrder) onclick="confirmDispatch('{{ $currentDayOrder }}', 'dispatched-postEx')" @else data-bs-toggle="modal" data-bs-target="#dispatched-postEx" @endif
                                                        class="
                                            dec_para_fun btn btn-sm mb-sm-3 mb-2 text-white btn-dark
                                            {{ $order->tracking_order_type != '' || $order->status == 'DELIVERED' || $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                        PostEx-DISPATCH
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- detail of editable -->
                                        <div class="col-auto mt-1 mt-sm-0">
                                            <p class="px-1 description-text">
                                                <span>
                                                    <span class="fw-bold">Code # </span>
                                                    @foreach ($order->orderitems as $item)
                                                        <span>
                                                            {{ $item->product->article }}
                                                            <span
                                                                class="fw-bold {{ $item->qty > 1 ? '' : 'd-none' }}">(
                                                                x {{ $item->qty }})</span>
                                                            ,
                                                        </span>
                                                    @endforeach
                                                </span>
                                                <span class="fw-bold">Cell # </span>{{ $order->userdetail->whatsapp }}
                                                <span class="fw-bold">Bill </span>{{ $order->amount }}
                                                <span class="fw-bold">Dc </span>{{ $order->charges }}
                                            </p>
                                        </div>

                                        <!-- cancel note -->
                                        <!-- <div class="col-auto">
                                                <p>{{ $order->cancel_note }}</p>
                                            </div> -->
                                    </div>

                                    <!--Status-->
                                    @if (!$order->wao_seller_id || $order->is_warehouseTeam_order)
                                        <div class="row">
                                            <div class="col-lg-4 my-sm-0 my-3">
                                                <select class="form-control text-center fw-bold py-2" name="status">
                                                    <option value="">Select Status</option>
                                                    <option value="DISPATCHED">
                                                        {{ $order->is_blocked_customer_order == 1 ? 'FAKE DISPATCHED ORDER' : 'DISPATCHED/Team Review ORDER' }}
                                                    </option>
                                                    <option value="CANCEL"
                                                        class="{{ $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                        CANCEL ORDER
                                                    </option>
                                                    <option value="CANCELHOLD">
                                                        <span>
                                                            <span>{{ $order->is_blocked_customer_order == 1 ? 'FAKE HOLD & CANCEL ORDER' : 'HOLD & CANCEL ORDER' }}</span>
                                                        </span>
                                                    </option>

                                                    <option value="BLOCK_CANCEL"
                                                        class="{{ $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                        BLOCK CUSTOMER & CANCEL ORDER
                                                    </option>
                                                    <option value="BLOCK_HOLD"
                                                        class="{{ $order->is_blocked_customer_order == 1 ? 'd-none' : '' }}">
                                                        BLOCK CUSTOMER & HOLD ORDER
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-lg-4">
                                                <textarea class="form-control" name="message_to_user" placeholder="Message to User..."></textarea>
                                            </div>

                                            <div class="col-lg-3">
                                                <textarea class="form-control" name="adjustment_note" placeholder="Adjustment Note...">{{ $order->adjustment_note }}</textarea>
                                            </div>

                                            <div
                                                class="{{ count($order->message) > 0 ? '' : 'd-none' }} col-auto my-sm-0 my-2">
                                                <a data-bs-toggle="modal" data-bs-target="#Add"
                                                    class="ms-2 btn btn-warning">
                                                    <i class="fa-solid fa-message"></i>
                                                </a>
                                            </div>

                                            <center>
                                                <div class="col-auto mt-2">
                                                    <input type="hidden" name="customer_id"
                                                        value="{{ $order->userdetail->id }}" />
                                                    <button class=" btn btn-primary d-block "
                                                        type="submit">Submit</button>
                                                </div>
                                            </center>

                                        </div>
                                    @endif
                                </div>
                                <!-- End Footer -->
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
    // View User Orders
    $(document).on('click', '.view_user_orders', function(e) {
        e.preventDefault();
        $('.user_orders_div').toggle(1000);
    });

    // courier information
    $(document).on('click', '.view_courier_information_div', function(e) {
        e.preventDefault();
        $('.courier_information_div').toggle(1000);
    });

    // Display SweetAlert confirmation if current day orders for user
    function confirmDispatch(currentDayOrder, openDispatchModal) {
        var currentDayOrderJson = JSON.parse(currentDayOrder);
        console.log(currentDayOrderJson);
        var currentDayOrderStatus = currentDayOrderJson.status.toLowerCase();

        var swalTitle = '';
        var swalText = '';

        // Constructing order details HTML markup
        var orderDetails = `<div style="border: 1px solid #ccc; padding: 10px;">
                        <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Order ID:</strong> ${currentDayOrderJson.id}</p>
                            <p><strong>Total:</strong> ${currentDayOrderJson.grandTotal}</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Whatsapp:</strong> ${currentDayOrderJson.user_detail.whatsapp}</p>
                            <p><strong>Name:</strong> ${currentDayOrderJson.name}</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Status:</strong> ${currentDayOrderJson.status}</p>
                        </div>`;

        // Conditionally include additional information
        orderDetails += currentDayOrderJson.wao_seller_detail ? `
                        <h5 class="text-info">By Reseller From Admin</h5>
                        <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Admin Name:</strong> ${currentDayOrderJson.wao_seller_detail.name}</p>
                            <p><strong>Email:</strong> ${currentDayOrderJson.wao_seller_detail.email}</p>
                        </div>` :
            currentDayOrderJson.wao_admin_detail ? `
                        <h5>By Web/App Admin</h5>
                        <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Admin Name:</strong> ${currentDayOrderJson.wao_admin_detail.name}</p>
                            <p><strong>Email:</strong> ${currentDayOrderJson.wao_admin_detail.email}</p>
                        </div>` :
            ''; // Empty string if neither wao_seller_detail nor wao_admin_detail exists

        orderDetails += `</div>`;


        if (currentDayOrderStatus === 'returned') {
            swalTitle = 'Last Returned Order Confirmation';
            swalText = 'User last Order is returned. Are you sure you want to dispatch a new order?<br><br>' +
                orderDetails;
        } else {
            swalTitle = 'Duplicate Order Confirmation';
            swalText = 'You have existing orders for today. Do you still want to dispatch a new order?<br><br>' +
                orderDetails;
        }

        Swal.fire({
            title: swalTitle,
            html: swalText,
            icon: currentDayOrderStatus === 'returned' ? 'error' : 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, dispatch!',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
            // If user confirms, open the modal
            if (result.isConfirmed) {
                $('#' + openDispatchModal).modal('show');
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //delet order itens 
        $(document).on('click', '.del_order_item', function(e) {
            e.preventDefault();
            var id = $(this).val();
            $('#delete_order_item_id').val(id);
            $('#DeleteModalRecord').modal('show');
            //delet form submit
            $(document).on('submit', '#delete_order_item_form', function(e) {
                e.preventDefault();

                let formdata = new FormData($('#delete_order_item_form')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{ route('delete_order_item') }}",
                    data: formdata,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status == 2) {
                            toastr.success(response.data);
                            window.location.reload(true);
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
    });
</script>
@endsection
