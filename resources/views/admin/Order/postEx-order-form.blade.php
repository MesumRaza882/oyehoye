<div class="container-fluid">
    <!-- dispatched -->
    <form method="POST" id="orderdonePostEx" autocomplete="off">@csrf
        <button class="mb-2 badge bg-primary p-2 view_courier_information_div">Courier Information</button><br>
        <div class="row justify-content-between courier_information_div" style="display:none;">
            <input type="hidden" value="{{ $order->id }}" name="order_id">
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

            <div class="col-lg-auto d-none">
                <div class="form-group">
                    <label for="pickupAddressCode">Pickup Address Multan <span
                            class="fw-bold text-danger">*</span></label>
                    <!-- <select required class="form-control" name="pickupAddressCode" id="pickupAddressCode">
                        <option value="001" {{ auth()->user()->postEx_pickupAddressCode === '001' ? 'selected' : '' }}>001</option>
                    <option value="002" {{ auth()->user()->postEx_pickupAddressCode === '002' ? 'selected' : '' }}>002</option>
                    <option value="003" {{ auth()->user()->postEx_pickupAddressCode === '003' ? 'selected' : '' }}>003</option>
                    <option value="004" {{ auth()->user()->postEx_pickupAddressCode === '004' ? 'selected' : '' }}>004</option>
                    </select>  -->

                    <select class="form-control" name="postEx_pickupAddressCode">
                        <option {{ auth()->user()->postEx_pickupAddressCode ? '' : 'selected' }} value="">Select
                            City</option>
                        @for ($i = 1; $i <= 100; $i++)
                            @php $formattedValue=sprintf('%03d', $i); @endphp <option value="{{ $formattedValue }}"
                                {{ auth()->user()->postEx_pickupAddressCode === $formattedValue ? 'selected' : '' }}>
                                {{ $formattedValue }}
                            </option>
                        @endfor
                    </select>

                </div>
            </div>

            <div class="col-lg-auto">
                <div class="form-group">
                    <label for="invoiceDivision">Select Copies of InvoiceDivision <span
                            class="fw-bold text-danger">*</span></label>
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
                    <textarea name="transactionNotes" id="transactionNotes" class="form-control">Other Contact # {{ $order->userdetail->whatsapp }}</textarea>
                </div>
            </div>

        </div>

        <!-- reseller deduct balance amount deduct from admin balance -->
        <input type="number" required name="purchaseTotal" value="{{ $purchaseTotal }}" hidden>

        <!-- Customer Detail -->
        <h6 class="badge bg-primary p-2">Customer Information</h6>
        <div class="row justify-content-between">
            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label for="consignee_name">Name <span class="fw-bold text-danger">*</span></label>
                    <input type="text" name="consignee_name" value="{{ $order->name }}" class="form-control">
                </div>
            </div>

            <div class="col-lg-2 col-12">
                <div class="form-group">
                    <label for="consignee_phone_number_1">Phone <span class="fw-bold text-danger">*</span></label>
                    <input type="number" name="consignee_phone_number_1" value="{{ $order->phone }}"
                        class="form-control">
                </div>
            </div>

            <div class="col-lg-2 col-12">
                <div class="form-group">
                    <label for="consignee_city_id">Select City <span class="fw-bold text-danger me-1">*</span>
                        <span>({{ $order->citydetail ? $order->citydetail->c_city_name : $order->city }})</span>
                    </label>
                    <input class="form-control" name="consignee_city" list="postExCities"
                        placeholder="Type to search City..." required
                        value="{{ $order->citydetail ? $order->citydetail->name : '' }}">
                    <datalist id="postExCities">
                        @if ($order->city_id)
                            <option data-value="{{ $order->citydetail->c_city_name }}" selected>
                                {{ $order->citydetail->c_city_name }}</option>
                        @else
                            <option data-value="" selected>Select City</option>
                        @endif
                        <!-- @include('include.extras.postExCities') -->
                        @foreach ($postExCities as $city)
                            <option data-value="{{ $city->postex }}">{{ $city->postex }}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="col-lg-5 col-12">
                <div class="form-group">
                    <label for="consignee_address">Address <span class="fw-bold text-danger">*</span></label>
                    <textarea required name="consignee_address" class=" form-control">{{ $order->address }}</textarea>
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
            <input type="text" name="order_id"
                value="{{ $order->id }}/{{ $order->grandProfit }}/{{ $order->orderitems->sum('qty') }}"
                class="form-control">
        </div>
    </div>

    <div class="col-lg-2 col-12">
        <div class="form-group">
            <label for="amount">Total Amount <span class="fw-bold text-danger">*</span></label>
            <input type="text" name="amount" value="{{ $order->grandTotal }}" class="form-control">
        </div>
    </div>

    <div class="col-lg-2 col-12">
        <div class="form-group">
            <label for="item_quantity">Item Quantity <span class="fw-bold text-danger">*</span></label>
            <input type="text" name="item_quantity" value="{{ $order->orderitems->sum('qty') }}"
                class="form-control">
        </div>
    </div>

    <div class="col-lg-4">
        <label for="item_description">Description <span class="fw-bold text-danger">*</span></label>
        <textarea name="item_description" rows="4" class="form-control">Code # @foreach ($order->orderitems as $item)
{{ $item->product->article }}( x {{ $item->qty }}),
@endforeach Cell#-{{ substr($order->userdetail->whatsapp, 0, 4) }}--{{ substr($order->userdetail->whatsapp, 6) }} Bill - {{ $order->amount }} Dc - {{ $order->charges }}
        </textarea>
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
    <input type="hidden" value="{{ $order->id }}" name="real_order_id">

    <div class="col-12 text-center">
        <button class="btn btn-primary order_done_btn" type="button">Dispatch Multan</button>
        <button class="btn btn-dark order_done_btn order_done_btn_noshera" type="button"
            data-pickup-address="nowshera">Dispatch Nowshera</button>
    </div>
</div>
</form>
</div>



<script>
    var is_reseller_order = `@php echo request()->is_reseller_order; @endphp`;
    var auth_id = `@php echo auth()->user()->id @endphp`;
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $(document).on('submit', '#orderdonePostEx', function(e) {
        $(document).on('click', '.order_done_btn', function(e) {
            e.preventDefault();
         

            $('.order_done_btn').prop('disabled', true);
            $('.order_done_btn').text('Order Dispatching...');

            if ($(this).hasClass('order_done_btn_noshera')) {
                pickupAddressCode = $(this).attr('data-pickup-address');
            } else {
                // pickupAddressCode = $('#pickupAddressCode').val();
                pickupAddressCode = 'multan';
            }
            $('#orderdonePostEx').append('<input type="hidden" name="pickupAddressCode" value="' +
                pickupAddressCode + '" /> ');


            let formdata = new FormData($('#orderdonePostEx')[0]);
            $.ajax({
                type: "POST",
                url: "{{ route('DispatchOrderPostEx') }}",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $('.order_done_btn').prop('disabled', false);
                    $('.order_done_btn').text('Dispatch Order');

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
                            const finalUrl = `${newUrl}/seller/getWebOrders?is_partner=1&${queryParams.toString()}`;
                            window.location.href = finalUrl;

                            // window.location.reload();

                        } else {
                            // Get the current URL
                            const currentUrl = window.location.href;
                            const urlSegments = currentUrl.split('/');
                            urlSegments.splice(-2, 2);
                            const newUrl = urlSegments.join('/');
                            const queryParams = new URLSearchParams();
                            queryParams.set('status', 'PENDING');
                            const finalUrl =
                                `${newUrl}/allorders?${queryParams.toString()}&is_reseller_order=${is_reseller_order}`;
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