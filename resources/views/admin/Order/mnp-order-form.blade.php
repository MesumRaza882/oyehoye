<div class="container-fluid">
    <!-- dispatched -->
    <form method="POST" id="orderdoneMnp" autocomplete="off">@csrf
        <button class="mb-2 badge bg-primary p-2 view_courier_information_div">Courier Information</button><br>
        <div class="row justify-content-between courier_information_div" style="display:none;">
            <input type="hidden" value="{{$order->id}}" name="order_id">
            <div class="col-auto">
                <div class="form-group">
                    <label for="mnp_username">MNP User name <span class="fw-bold text-danger">*</span></label>
                    <input type="text" readonly name="mnp_username" value="Syed.ashraf_18w43" class="form-control">
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group">
                    <label for="mnp_password">MNP Password <span class="fw-bold text-danger">*</span></label>
                    <input type="text" readonly name="mnp_password" value="Ali7865@" class="form-control">
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group">
                    <label for="locationID">Location Id </label>
                    <input type="text" name="locationID" value="52127-MUX" class="form-control">
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group">
                    <label for="fragile">Fragile <span class="fw-bold text-danger">*</span></label>
                    <select name="fragile" id="fragile" class="form-control">
                        <option value="Yes" selected>Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group">
                    <label for="service">Service <span class="fw-bold text-danger">*</span></label>
                    <select name="service" id="service" class="form-control">
                        <option value="O" selected>Overnight</option>
                        <option value="S">Second Day</option>
                    </select>
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group">
                    <label for="estimated_weight">Weight <span class="fw-bold text-danger">*</span></label>
                    <input type="text" name="estimated_weight" value="0.5" class="form-control">
                </div>
            </div>

        </div>


        <!-- reseller deduct balance amount deduct from admin balance -->
        <input type="number" required name="purchaseTotal" value="{{$purchaseTotal}}" hidden>

        <!-- Customer Detail -->
        <h6 class="badge bg-primary p-2">Customer Information</h6>
        <div class="row justify-content-between">
            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label for="consignee_name">Name <span class="fw-bold text-danger">*</span></label>
                    <input type="text" name="consignee_name" value="{{$order->name}}" class="form-control">
                </div>
            </div>

            <div class="col-lg-2 col-12">
                <div class="form-group">
                    <label for="consignee_phone_number_1">Phone <span class="fw-bold text-danger">*</span></label>
                    <input type="number" name="consignee_phone_number_1" value="{{$order->phone}}" class="form-control">
                </div>
            </div>

            <div class="col-lg-2 col-12">
                <div class="form-group">
                    <label for="consignee_city">Select City <span class="fw-bold text-danger me-1">*</span>
                        <span>({{$order->citydetail ? $order->citydetail->c_city_name : $order->city}})</span>
                    </label>
                    <input class="form-control" name="consignee_city" list="mnpCities" placeholder="Type to search City..." required value="{{$order->citydetail ? $order->citydetail->name : ''}}">
                    <datalist id="mnpCities">
                        @if($order->city_id)
                        <option data-value="{{$order->citydetail->courier_standard}}" selected>{{$order->citydetail->courier_standard}}</option>
                        @else
                        <option data-value="" selected>Select City</option>
                        @endif
                        @foreach($mnpCities as $city)
                        <option data-value="{{$city->courier_standard}}">{{$city->courier_standard}}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="col-lg-5 col-12">
                <div class="form-group">
                    <label for="consignee_address">Address <span class="fw-bold text-danger">*</span></label>
                    <textarea required name="consignee_address" class=" form-control">{{$order->address}}</textarea>
                </div>
            </div>
        </div>

</div>

<!-- Order Detail -->
<h6 class="badge bg-primary p-2">Order Information</h6>
<div class="row">
    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label for="order_id">Order-Id</label>
            <input type="text" name="order_id" value="{{$order->id}}/{{$order->grandProfit}}/{{$order->orderitems->sum('qty')}}/APP-{{ substr($order->phone, -5) }}" class="form-control">
        </div>
    </div>

    <div class="col-lg-2 col-12">
        <div class="form-group">
            <label for="amount">Total Amount <span class="fw-bold text-danger">*</span></label>
            <input type="text" name="amount" value="{{$order->grandTotal}}" class="form-control">
        </div>
    </div>

    <div class="col-lg-2 col-12">
        <div class="form-group">
            <label for="item_quantity">Item Quantity <span class="fw-bold text-danger">*</span></label>
            <input type="text" name="item_quantity" value="{{$order->orderitems->sum('qty')}}" class="form-control">
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="form-group">
            <label for="remarks">Remarks </label>
            <textarea name="remarks" id="remarks" class="form-control"></textarea>
        </div>
    </div>

    <div class="col-lg-4 d-felx">
        <label for="item_description">Description <span class="fw-bold text-danger">*</span></label>
        <textarea name="item_description" rows="4" class="form-control">Code # @foreach($order->orderitems as $item){{$item->product->article}}( x {{$item->qty }}), @endforeach Cell#-{{ substr($order->userdetail->whatsapp, 0, 6) }}--{{ substr($order->userdetail->whatsapp, 4) }} Bill - {{$order->amount}} Dc - {{$order->charges}}
    </div>



</div>

<!-- message sent to user -->
<h6 class="badge bg-primary p-2">Message</h6>
<div class="row">
    <div class="col-lg-8 col-12">
        <textarea class="form-control" name="message_to_user" placeholder="Message..."></textarea>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <center>
            <input type="hidden" value="{{$order->id}}" name="real_order_id">
            <button class="btn btn-primary order_done_btn_mnp" type="submit">Dispatch MNP Order</button>
        </center>
    </div>
</div>
</form>
</div>





<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // add item
        $(document).on('submit', '#orderdoneMnp', function(e) {
            e.preventDefault();


            $('.order_done_btn_mnp').prop('disabled', true);
            $('.order_done_btn_mnp').text('Order Dispatching...');


            let formdata = new FormData($('#orderdoneMnp')[0]);
            $.ajax({
                type: "POST",
                url: "{{route('DispatchOrderMnp')}}",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $('.order_done_btn_mnp').prop('disabled', false);
                    $('.order_done_btn_mnp').text('Dispatch MNP Order');

                    if (response.status == 0) {
                        toastr.error(response.data);
                    } else if (response.status == 2) {
                        toastr.success(response.message);

                        if (auth_id != 1) {
                            // Get the current URL
                            const currentUrl = window.location.href;
                            const urlSegments = currentUrl.split('/');
                            urlSegments.splice(-3, 3);
                            const newUrl = urlSegments.join('/');
                            const queryParams = new URLSearchParams();
                            queryParams.set('status', 'PENDING');
                            const finalUrl = `${newUrl}/seller/getWebOrders?${queryParams.toString()}`;
                            window.location.href = finalUrl;
                        } else {
                            // Get the current URL
                            const currentUrl = window.location.href;
                            const urlSegments = currentUrl.split('/');
                            urlSegments.splice(-2, 2);
                            const newUrl = urlSegments.join('/');
                            const queryParams = new URLSearchParams();
                            queryParams.set('status', 'PENDING');
                            const finalUrl = `${newUrl}/allorders?${queryParams.toString()}`;
                            window.location.href = finalUrl;
                        }
                    } else if (response.status == 3) {
                        toastr.error(response.data);
                    }
                },
            });
        });
    });
</script>