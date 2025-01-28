<div class="container-fluid">
    <!-- dispatched -->
    <form method="POST" id="orderdoneTrax" autocomplete="off">@csrf
        <button class="mb-2 badge bg-primary p-2 view_courier_information_div">Courier Information</button><br>
        <div class="row justify-content-between courier_information_div" style="display:none;">
            <input type="hidden" value="{{$order->id}}" name="order_id">
            <div class="col-auto">
                <div class="form-group">
                    <label for="pickup_address_id">Pickup Address Id <span class="fw-bold text-danger">*</span></label>
                    <input type="number" name="pickup_address_id" value="{{auth()->user()->trax_pickup_address_id}}" class="form-control">
                </div>
            </div>

            <div class="col-auto">
                <div class="form-group">
                    <label for="item_product_type_id">Select Product Category <span class="fw-bold text-danger">*</span></label>
                    <select name="item_product_type_id" id="item_product_type_id" class="form-control">
                        <option value="1" selected>Apparel</option>
                        <!-- <option value="24" selected>Other</option> -->
                    </select>
                </div>
            </div>

            <div class="col-auto">
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

            <div class="col-auto">
                <div class="form-group">
                    <label for="payment_mode_id">Select Payment Mode <span class="fw-bold text-danger">*</span></label>
                    <select name="payment_mode_id" id="payment_mode_id" class="form-control">
                        <option value="1" selected>COD</option>
                        <!-- <option value="24" selected>Other</option> -->
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
                    <label for="consignee_city_id">Select City <span class="fw-bold text-danger me-1">*</span>
                        <span>({{$order->citydetail ? $order->citydetail->name : $order->city}})</span>
                    </label>
                    <input class="form-control" name="consignee_city" list="answers" id="answerInput" placeholder="Type to search City..." required value="{{$order->citydetail ? $order->citydetail->name : ''}}">
                    <datalist id="answers">
                        @if($order->city_id)
                        <option data-value="{{$order->citydetail->trax}}" selected>{{$order->citydetail->name}}</option>
                        @else
                        <option data-value="" selected>Select City</option>
                        @endif
                        @foreach($traxCities as $city)
                        <option data-value="{{$city->trax}}">{{$city->name}}</option>
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
<div class="row ">
    <div class="col-lg-2 col-12">
        <div class="form-group">
            <label for="order_id">Order-Id</label>
            <input type="text" name="order_id" value="{{$order->id}}/{{$order->grandProfit}}/{{$order->orderitems->sum('qty')}}" class="form-control">
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

    <div class="col-lg-4">

        <label for="item_description">Description <span class="fw-bold text-danger">*</span></label>
        <textarea name="item_description" rows="4" class="form-control">Code # @foreach($order->orderitems as $item){{$item->product->article}}( x {{$item->qty }}), @endforeach Cell#-{{ substr($order->userdetail->whatsapp, 0, 6) }}--{{ substr($order->userdetail->whatsapp, 4) }} Bill - {{$order->amount}} Dc - {{$order->charges}}</textarea>
        <!-- <div class="form-group d-none">
            <input type="text" name="item_description" id="item_description_input" required class="form-control" />
        </div>
        <p class="px-1" id="dynamic_content">
            <span>
                <span class="fw-bold">Code # </span>
                @foreach($order->orderitems as $item)
                <span>
                    {{$item->product->article}}
                    <span class="fw-bold {{$item->qty > 1 ? '':'d-none' }}">( x {{$item->qty }})</span>
                    ,
                </span>
                @endforeach
            </span>
            <span class="fw-bold">Cell # </span>{{$order->userdetail->whatsapp}}
            <span class="fw-bold">Bill </span>{{$order->amount}}
            <span class="fw-bold">Dc </span>{{$order->charges}}
        </p> -->


    </div>
</div>

<!-- message sent to user -->
<h6 class="badge bg-primary p-2 mt-lg-0 mt-3">Message</h6>
<div class="row">
    <div class="col-lg-8 col-12">
        <textarea class="form-control" name="message_to_user" placeholder="Message..."></textarea>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <center>
            <input type="hidden" value="{{$order->id}}" name="real_order_id">
            <button class="btn btn-primary order_done_btn_trax" type="submit">Dispatch Trax Order</button>
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

        // add trax order
        // $(document).on('submit', '#orderdoneTrax', function(e) {
        //     e.preventDefault();

        $(document).on('click', '.order_done_btn_trax', function(e) {
            e.preventDefault();

            $('.order_done_btn_trax').prop('disabled', true);
            $('.order_done_btn_trax').text('Order Dispatching...');


            let formdata = new FormData($('#orderdoneTrax')[0]);
            $.ajax({
                type: "POST",
                url: "{{route('DispatchOrderTrax')}}",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $('.order_done_btn_trax').prop('disabled', false);
                    $('.order_done_btn_trax').text('Dispatch Trax Order');
                    

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
                        }else{
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