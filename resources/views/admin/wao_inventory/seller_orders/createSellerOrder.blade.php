@extends('admin.layouts.app')
@section('title') Seller-Order @endsection
@section('content')
<style>
    .vertical-caption {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        white-space: nowrap;
    }
</style>
<div class="main-content">
    <div class="container-fluid">

        <!-- delete record modal -->
        @section('delete_modal_footer')
        <x-x-delete-record deleteRoute="{{ route('whiteItems_delete') }}" />
        @endsection
        <!-- end Delete Modal -->

        <!-- whiteList products mark -->
        @include('include.modal.whiteListModal')
        <!-- end -->

        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-2 gy-1">
                    @if(session('information_message'))
                    <div class="alert alert-warning text-center">
                        {{ session('information_message') }}
                    </div>
                    @endif
                    <div class="col-auto d-flex align-items-center">
                        <p class="text-center my-3 fw-bold me-2"> <a class="me-1" href="{{route('waoseller.order.index')}}"><i class="fa-solid fa-arrow-left"></i></a> Add New Order</p>
                        <button type="button" class="fw-bold btn btn-primary btn-sm" data-value="whiteItems" data-bs-toggle="modal" data-bs-target="#whiteList">
                            <i class="fa fa-plus"></i> Items
                        </button>
                        <button type="button" class="fw-bold btn btn-warning btn-sm" data-bs-toggle="modal" data-value="multanItems" data-bs-target="#whiteList">
                            <i class="fa fa-plus"></i> Mult-Items
                        </button>
                    </div>
                    <div class="col-auto">
                        <a target="_blank" href="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Image_not_available.png/800px-Image_not_available.png" id="slipAnchor">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Image_not_available.png/800px-Image_not_available.png" alt="order-slip" width="65px" height="65px" class="img img-responsive" id="slipImage">
                        </a>
                        <button id="copyUrlButton" value="" class="d-none mt-2 btn btn-sm btn-outline-info">Copy and Paste</button>
                    </div>
                    <div class="col-auto">
                        <!-- only for admin -->
                        @if (auth()->user()->role === 1)
                        <button value="1" class="deleletRecordIconButton btn btn-sm btn-danger"><i class="fa fa-trash"></i> White-List</button>
                        @endif
                        <button class=" border btn btn-success view_user_orders ms-auto btn-sm d-none"><i class="fa-solid fa-eye"></i>Last 10 Orders</button>
                        <span class="badge bg-warning noRecordText d-none">No History</span>
                    </div>

                </div>
                <!-- order history of contat number user -->
                <div class="row">
                    <!--customer orders-->
                    <div class="col-12 user_orders_div" style="display:none;">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tracking-Id</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Charges</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="history-order-table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- dispatched -->
                <form method="POST" id="orderdone" autocomplete="off">@csrf

                    <div class="row align-items-center justify-content-between">
                        <div class="col-lg-auto">
                            <h6 class="badge bg-primary p-2">Courier Information</h6>
                        </div>
                        <div class="col-lg-auto">
                            @if (auth()->user()->role != 4)
                            <div class="d-flex align-items-center justify-content-between">
                                <!-- @if (auth()->user()->postEx_allow === 1) -->
                                <div>
                                    <input class="border border-info" type="radio" name="courier_option" id="postEx" value="postEx" checked> <label for="postEx">PostEx</label>
                                </div>
                                <!-- @endif -->
                                `
                                @if (auth()->user()->trax_allow === 1)
                                <div class="mx-2">
                                    <input class="border border-info" type="radio" name="courier_option" id="trax" value="trax"> <label for="trax">Trax</label>
                                </div>
                                @endif

                                @if (auth()->user()->mnp_alllow === 1)
                                <div>
                                    <input class="border border-info" type="radio" name="courier_option" id="mnp" value="mnp"> <label for="mnp">MNP</label>
                                </div>
                                @endif


                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- trax row -->
                    <div class="row trax_courier_info" style="display: none !important;">
                        <div class="col-lg-auto">
                            <x-x-input name="pickup_address_id" type="number" label="Pickup Address Id" value="{{auth()->user()->trax_pickup_address_id}}" />
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="item_product_type_id">Select Product Category <span class="fw-bold text-danger">*</span></label>
                                <select name="item_product_type_id" id="item_product_type_id" class="form-control">
                                    <option value="1" selected>Apparel</option>
                                    <!-- <option value="24" selected>Other</option> -->
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="shipping_mode_id">Select Shipping Mode <span class="fw-bold text-danger">*</span></label>
                                <select name="shipping_mode_id" id="shipping_mode_id" class="form-control">
                                    <option value="1" selected>Rush</option>
                                    <option value="2">Saver plus</option>
                                    <option value="3">Swift</option>
                                    <!-- <option value="24" selected>Other</option> -->
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="payment_mode_id">Select Payment Mode <span class="fw-bold text-danger">*</span></label>
                                <select name="payment_mode_id" id="payment_mode_id" class="form-control">
                                    <option value="1" selected>COD</option>
                                    <!-- <option value="24" selected>Other</option> -->
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="estimated_weight">Weight <span class="fw-bold text-danger">*</span></label>
                                <input type="text" required name="estimated_weight" value="0.5" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- mnp row -->
                    <div class="row mnp_courier_info" style="display: none !important;">
                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="service">Service <span class="fw-bold text-danger">*</span></label>
                                <select name="service" id="service" class="form-control">
                                    <option value="O" selected>Overnight</option>
                                    <option value="S">Second Day</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="fragile">Fragile <span class="fw-bold text-danger">*</span></label>
                                <select name="fragile" id="fragile" class="form-control">
                                    <option value="Yes" selected>Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="mnp_username">MNP User name <span class="fw-bold text-danger">*</span></label>
                                <input type="text" readonly name="mnp_username" value="{{auth()->user()->mnp_username}}" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="mnp_password">MNP Password <span class="fw-bold text-danger">*</span></label>
                                <input type="text" readonly name="mnp_password" value="{{auth()->user()->mnp_password}}" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="locationID">Location Id </label>
                                <input type="text" name="locationID" value="{{auth()->user()->locationID}}" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="remarks">Remarks </label>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="estimated_weight">Weight <span class="fw-bold text-danger">*</span></label>
                                <input type="text" required name="estimated_weight" value="0.5" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- postex row -->
                    <div class="row postEx_courier_info">
                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="postExOrderType">Select Order Type <span class="fw-bold text-danger">*</span></label>
                                <select name="postExOrderType" id="postExOrderType" class="form-control">
                                    <option value="Normal" selected>Normal</option>
                                    <option value="Reverse">Reverse</option>
                                    <option value="Replacement">Replacement</option>
                                    <option value="Overland">Overland</option>
                                </select>
                            </div>
                        </div>

                        {{-- hide for all users --}}
                        <div class="col-lg-auto d-none">
                            <div class="form-group">
                                <label for="pickupAddressCode">Pickup Address <span class="fw-bold text-danger">*</span></label>
            
                                <select class="form-control" name="pickupAddressCode" id="pickupAddressCode">
                                    {{-- for team member set dummy --}}
                                    @if (auth()->user()->role === 4)
                                        <option selected value="001">Select Pickup Address</option>
                                    @else
                                        <option {{ auth()->user()->postEx_pickupAddressCode ? '' : 'selected' }} value="">Select Pickup Address</option>
                                    @endif
                                    @foreach($codes as $code)
                                    <option value="{{ $code->postEx_pickupAddressCode }}" {{ auth()->user()->postEx_pickupAddressCode === $code->postEx_pickupAddressCode ? 'selected' : '' }}>
                                        {{ $code->postEx_pickupAddressCode }}
                                    </option>
                                    @endforeach
                                    <option value="nowshera" {{auth()->user()->postEx_pickupAddressCode === 'nowshera' ? 'selected' :''}}>Nowshera</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="form-group">
                                <label for="invoiceDivision">Select Copies of InvoiceDivision <span class="fw-bold text-danger">*</span></label>
                                <select name="invoiceDivision" id="invoiceDivision" class="form-control">
                                    <option value="1" selected>1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="transactionNotes">Remarks/Notes </label>
                                <textarea name="transactionNotes" id="transactionNotes" class="form-control"></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Customer Detail -->
                    <h6 class="badge bg-primary p-2">Customer Information</h6>
                    <div class="row">

                        <!-- whatsapp -->
                        <div class="col-lg-3 col-12">
                            <x-x-input name="consignee_whatsaapp" class="bg-success text-white" type="phone" label="User Whatsapp" required id="consignee_whatsaapp" :value="old('consignee_whatsaapp')" />
                        </div>

                        <!-- phone -->
                        <div class="col-lg-3 col-12">
                            <x-x-input name="consignee_phone_number_1" type="phone" label="Phone Number" required id="consignee_phone_number_1" :value="old('consignee_phone_number_1')" />
                        </div>

                        <!-- name -->
                        <div class="col-lg-3 col-12">
                            <x-x-input name="consignee_name" id="consignee_name" type="text" label="Name" required :value="old('consignee_name')" />
                        </div>

                        <!-- cities for trax -->
                        <div class="col-lg-3 col-12 trax_courier_info" style="display: none !important;">
                            <div class="form-group">
                                <label for="consignee_city_trax">Select City <span class="fw-bold text-danger me-1">*</span>
                                </label>
                                <input class="form-control mt-2" id="consignee_city_trax" name="consignee_city_trax" list="answers" placeholder="Type to search City Trax...">
                                <datalist id="answers">
                                    <option data-value="" selected>Select City</option>
                                    @foreach($traxCities as $city)
                                    <option data-value="{{$city->id}}">{{$city->name}}</option>
                                    @endforeach
                                </datalist>
                                <span class="text-danger">@error('consignee_city_trax'){{$message}}@enderror</span>

                            </div>
                        </div>

                        <!-- cities for mnp -->
                        <div class="col-lg-3 col-12 mnp_courier_info" style="display: none !important;">
                            <div class="form-group">
                                <label for="consignee_city_trax">Select City <span class="fw-bold text-danger me-1">*</span>
                                </label>
                                <input class="form-control mt-2" id="consignee_city_mnp" name="consignee_city_mnp" list="mnpCities" placeholder="Type to search City Mnp...">
                                <datalist id="mnpCities">
                                    <option data-value="" selected>Select City</option>
                                    @foreach($mnpCities as $city)
                                    <option data-value="{{$city->id}}">{{$city->courier_standard}}</option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <!-- cities for postEx -->
                        <div class="col-lg-3 col-12 postEx_courier_info">
                            <div class="form-group">
                                <label for="consignee_city_postEx">Select City <span class="fw-bold text-danger me-1">*</span>
                                </label>
                                <input class="form-control mt-2" id="consignee_city_postEx" name="consignee_city_postEx" list="postExCities" placeholder="Type to search City PostEx...">
                                <datalist id="postExCities">
                                    <option data-value="" selected>Select City</option>
                                    <!-- @include('include.extras.postExCities') -->
                                    @foreach($postExCities as $city)
                                    <option data-value="{{$city->postex}}">{{$city->postex}}</option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label for="consignee_address">Address <span class="fw-bold text-danger">*</span></label>
                                <textarea id="consignee_address" required name="consignee_address" class=" form-control" :value="old('consignee_address')"></textarea>
                                <span class="text-danger">@error('consignee_address'){{$message}}@enderror</span>

                            </div>
                        </div>
                    </div>


                    <!-- Order Detail -->
                    <h6 class="badge bg-primary p-2">Order Information</h6>
                    <div class="row">
                        <!-- total amount deduct from reseller balance -->
                        <input type="number" required name="purchaseTotal" id="purchaseTotal" hidden>

                        <!-- dc -->
                        <div class="col-lg-3 col-12">
                            <x-x-input name="total" type="number" required label="Total" id="total" min="0" :value="old('total')" />
                        </div>

                        <!-- dc -->
                        <div class="col-lg-3 col-12">
                            <x-x-input name="charges" type="number" required label="Delivery Charges" id="charges" min="0" :value="old('charges')" />
                        </div>

                        <!-- total + chrages  -->
                        <div class="col-lg-2 col-12">
                            <x-x-input name="grandTotal" type="number" required label="Grand Amount" id="grandTotal" min="0" :value="old('grandTotal')" />
                        </div>


                        <!-- profit -->
                        <!-- not for warehouse team member or Resellers -->
                        <div class="col-lg-2 col-12 {{auth()->user()->role === 4 || (auth()->user()->role === 3 && auth()->user()->is_partner == Null) ? 'd-none' : ''}} ">
                            <x-x-input name="grandProfit" type="number" required label="Profit" id="grandProfit" min="0" :value="old('grandProfit')" />
                        </div>

                        <!-- qty -->
                        <div class="col-lg-2 col-12">
                            <x-x-input name="item_quantity" type="number" label="Item Quantity" required id="item_quantity" min="1" :value="old('item_quantity')" />
                        </div>
                        <!-- description -->
                        <div class="col-lg-4">
                            <label for="item_description">Description <span class="fw-bold text-danger">*</span></label>
                            <div class="form-group">
                                <textarea required id="generalDescription" name="item_description" value="" class=" form-control"></textarea>
                                <span class="text-danger">@error('item_description'){{$message}}@enderror</span>
                            </div>
                        </div>

                            <!-- order id -->
                            {{-- hide from resellers --}}
                            <div class="col-lg-4 col-12 {{auth()->user()->role === 3 && auth()->user()->is_partner == Null ? 'd-none' : ''}} ">
                                <div class="form-group">
                                    <label for="order_id">Order-Id <span class="text text-danger">*</span></label>
                                    <input type="text" id="order_id" required name="order_id" value="{{old('order_id')}}" class="form-control">
                                </div>
                            </div>

                        <div class="col-lg-3">
                            <label for="adjustment_note">Adjustment Note </label>
                            <div class="form-group">
                                <textarea class="form-control" id="adjustment_note" name="adjustment_note"></textarea>
                            </div>
                        </div>
                        <!-- amount deduct from reseller balance -->
                        {{-- not for team member or reseller --}}
                        <div class="col-lg-4 col-12 {{auth()->user()->role === 4 || (auth()->user()->role === 3 && auth()->user()->is_partner == Null) ? 'd-none' : ''}}">
                            <label class="text-warning me-2">Amount Deduct from Balance</label>
                            <div class="form-group d-flex align-items-end">
                                <input type="number" disabled id="purchaseTotalDisplay" class="form-control flex-grow-1">
                                <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#deductResellerBalance"><i class="fa fa-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 d-flex align-items-center justify-content-lg-center g-2">
                        <div class="col-auto">
                            <button class="btn btn-info" type="reset">Reset</button>
                        </div>
                        @if (auth()->user()->role != 4)
                        <div class="col-auto trax_courier_info" style="display: none;">
                            <button class="btn btn-primary order_trax_btn" value="1" type="submit">Dispatch TRAX Order</button>
                        </div>

                        <div class="col-auto mnp_courier_info" style="display: none;">
                            <button class="btn btn-info order_mnp_btn" value="2" type="submit">Dispatch MNP Order</button>
                        </div>
                        <div class="col-auto postEx_courier_info">
                            @if (auth()->user()->postEx_allow === 1)
                            <button class="btn btn-dark order_postEx_btn" value="3" type="submit" data-pickup-address="defaultSelect">Dispatch Post-Ex Multan</button>
                            <button class="btn btn-outline-dark mt-sm-0 mt-2 order_postEx_btn" value="3" type="submit" data-pickup-address="nowshera">Dispatch Nowshera</button>
                            @else
                            <p class="alert alert-danger text-white p-2">Not Allowed</p>
                            @endif
                        </div>
                        @else
                        <div class="col-auto">
                            <button class="btn btn-primary order_warehouose_btn" value="1" type="submit">Confirm Order to Admin</button>
                        </div>
                        @endif
                    </div>
                </form>

                <!-- order slip -->
                <div id="box" class="d-none">
                    <div class="row pt-3 pb-5 mt-5" style="background-color: #EDEDD5;">
                        <div class="col-md-12">
                            <table class="table table-responsive table-bordered">
                                <tbody>
                                    <tr>
                                        <td colspan="2" rowspan="1" class="p-1" style="width: 21%;">
                                            <div class="d-flex justify-content-center">
                                                <h5>Tracking-Slip</h5>
                                            </div>
                                        </td>
                                        <td colspan="3" rowspan="1" class="p-1">
                                            <!-- {{-- horizontal qr code --}} -->
                                            <div class="d-flex justify-content-center">
                                                <h6> Order-Slip-Detail</h6>
                                            </div>
                                        </td>
                                        <td colspan="3" class="p-1 fw-bold">CN # :
                                            <span id="slip-order-cn" class="fw-bold"></span>
                                        </td>
                                        <td class="bg-secondary p-1 text-center align-middle fw-bold">COD Amount :
                                            <span id="slip-cod-amount" class="fw-bold" style="font-size: 16px;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1 text-center align-middle fw-bold">Order-Type</td>
                                        <td class="p-1 text-center align-middle" id="slip-order-type"></td>
                                        <td rowspan="2" class="vertical-caption p-1 text-center align-middle bg-secondary fw-bold" style="width: 2%;">Consignee Info</td>
                                        <td colspan="3" class="p-1 text-center align-middle font-weight-bold" id="slip-order-cons-name"></td>
                                        <td colspan="2" class="p-1 text-center align-middle font-weight-bold" id="slip-order-cons-city"></td>
                                        <td class="p-1 text-center align-middle" id="slip-order-consignee_whatsaapp"></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-10 fw-bold p-1 text-center align-middle ">Order-Id</td>
                                        <td class="fs-10 p-1 text-center align-middle" id="slip-order-id"></td>
                                        <td colspan="6" class="p-1 align-middle"><strong> Address : </strong> <span id="slip-order-consignee_address"></span></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-10 fw-bold p-1 text-center align-middle">Date</td>
                                        <td class="fs-10 p-1 text-center align-middle" id="slip-order-date"></td>
                                        <td rowspan="3" class="p-1 vertical-caption text-center align-middle bg-secondary fw-bold" style="width: 2%;">Shipper Info</td>
                                        <td colspan="5" class="p-1 align-middle">Wao Imported Collection / N</td>
                                        <td style="width: 27%;" class="p-1 align-middle">03007307110
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fs-10 fw-bold p-1 text-center align-middle">Pieces</td>
                                        <td class="fs-10 p-1 text-center align-middle" id="slip-order-pieces"></td>
                                        <td class="p-1 text-center align-middle" style="width: 15%;">
                                            Pickup Address
                                            <hr class="my-0">
                                            Return Address
                                        </td>
                                        <td colspan="6" class="p-1 text-center align-middle">Multan</td>
                                    </tr>
                                    <tr>
                                        <td class="fs-10 fw-bold p-1 text-center align-middle">Charges</td>
                                        <td class="fs-10 p-1 text-center align-middle" id="slip-order-charges"></td>

                                        <td colspan="6" class="py-3 px-2 text-left">
                                            <span class="fw-bold">Remarks:</span> <span id="slip-order-remarks"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" class="py-3 px-2 text-center">
                                            <span class="fw-bold">Product Details:</span> <span id="slip-order-productDetail"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="deductResellerBalance" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deductResellerBalanceLabel" aria-hidden="true">
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
                            <h6 style="font-weight: 700;">Basic Products Price : <span id="basicProductCount" style="font-weight: 16px;">(0x)</span> = <span id="basicProductAmount" style="font-weight: 800;" class="text-warning">0</span></h6>
                            <h6 class="mb-2" style="font-weight: 700;">Per Product Charges: <span id="perProductCount" style="font-weight: 800;">0 X 0</span> = <span id="perProductAmount" style="font-weight: 800;" class="text-warning">0</span></h6>
                            <h6 class="mb-2" style="font-weight: 700;">Order Charges: <span style="font-weight: 800;" class="text-warning" id="orderCharges">0</span></h6>
                            <h6 class="mb-0 pb-0" class="text-center border border-primary">Total Amount : <span class="text-warning" id="totalAmountPurchse">0</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleCourierOptionChange() {
        var selectedCourier = $('input[name="courier_option"]:checked').val();

        if (selectedCourier === 'mnp') {
            $('.trax_courier_info').hide();
            $('.postEx_courier_info').hide();
            $('.mnp_courier_info').show();
        } else if (selectedCourier === 'trax') {
            $('.mnp_courier_info').hide();
            $('.postEx_courier_info').hide();
            $('.trax_courier_info').show();
        } else if (selectedCourier === 'postEx') {
            $('.mnp_courier_info').hide();
            $('.trax_courier_info').hide();
            $('.postEx_courier_info').show();
        }
    }

    // Trigger the function when a radio input is clicked
    $('input[name="courier_option"]').click(handleCourierOptionChange);

    $(document).on('click', '.view_user_orders', function(e) {
        e.preventDefault();
        $('.user_orders_div').toggle(1000);
    });

    document.getElementById('item_quantity').addEventListener('input', updateOrderId);
    document.getElementById('grandProfit').addEventListener('input', updateOrderId);

    function updateOrderId() {
        let itemQuantityInput = document.getElementById('item_quantity');
        let grandProfitInput = document.getElementById('grandProfit');
        let orderIdInput = document.getElementById('order_id');

        let itemQuantity = itemQuantityInput.value;
        let grandProfit = grandProfitInput.value;

        if (itemQuantity && grandProfit) {
            let orderID = `00/${grandProfit}/${itemQuantity}`;
            orderIdInput.value = orderID;
        } else {
            orderIdInput.value = '';
        }
    }

    $('#copyUrlButton').on('click', function() {
        var valueToCopy = $(this).val();
        var tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(valueToCopy).select();
        document.execCommand('copy');
        tempInput.remove();
        toastr.success('Copied Url successfully');
    });


    document.getElementById('consignee_city_postEx').addEventListener('keydown', function(event) {
        // Check if the pressed key is Enter (key code 13)
        if (event.keyCode === 13) {
            event.preventDefault(); // Prevent the default action (form submission, in this case)

            var inputField = event.target;
            var datalist = document.getElementById('postExCities');
            var userInput = inputField.value.trim().toLowerCase(); // Get user input and convert to lowercase
            var options = datalist.querySelectorAll('option:not([data-value=""])');

            var matchingOption = Array.from(options).find(option => option.value.trim().toLowerCase().startsWith(userInput));

            if (matchingOption) {
                inputField.value = matchingOption.getAttribute('data-value') || matchingOption.value;
            }
        }
    });

    // Function to set input value based on user input and matching option
    function setCityInputValue(inputFieldId, datalistId) {
        document.getElementById(inputFieldId).addEventListener('keydown', function(event) {
            // Check if the pressed key is Enter (key code 13)
            if (event.keyCode === 13) {
                event.preventDefault(); // Prevent the default action (form submission, in this case)

                var inputField = event.target;
                var datalist = document.getElementById(datalistId);
                var userInput = inputField.value.trim().toLowerCase(); // Get user input and convert to lowercase
                var options = datalist.querySelectorAll('option:not([data-value=""])');

                var matchingOption = Array.from(options).find(option => option.value.trim().toLowerCase().startsWith(userInput));

                if (matchingOption) {
                    inputField.value = matchingOption.value || matchingOption.getAttribute('data-value');
                }
            }
        });
    }

    // Set up the function for each input field
    setCityInputValue('consignee_city_trax', 'answers');
    setCityInputValue('consignee_city_mnp', 'mnpCities');
    setCityInputValue('consignee_city_postEx', 'postExCities');
</script>
@endsection