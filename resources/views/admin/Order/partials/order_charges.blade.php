<div class="row mt-3 justify-content-end">
    <!--slip image-->
    @if ($order->slip)
        <div class="col-lg-3 col-sm-6">
            <div class="form-group">
                <label> Slip Image</label>
                <a target="_blank" href="{{ $order->slip }}"><img src="{{ $order->slip }}" alt="slip-order"
                        class="rounded-circle" width="150px" height="120px"></a>
            </div>
        </div>
    @endif
    <!--Amount-->
    <div class="col-lg-6 row">
        <div class="col-sm-3 col-xs-6 px-0">
            <div class="form-group">
                <label> Amount</label>
                <!-- <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">Rs: {{ $order->amount }}</p> -->
                <input class="form-control" required name="amount" value="{{ $order->amount }}" />
            </div>
        </div>
        <!--Charges-->
        <div class="col-sm-3 col-xs-6 px-0">
            <div class="form-group mx-1">
                <label> Charges</label>
                <!-- <p class="pb-2 text-center" >Rs: {{ $order->charges }}</p> -->
                <input class="form-control" name="charges" value="{{ $order->charges }}" />
            </div>
        </div>
        <!-- Discount -->
        <div class="col-sm-3 col-xs-6 px-0">
            <div class="form-group">
                <label> Discount</label>
                <!-- <p class="pb-2 text-center" >Rs: {{ $order->charges }}</p> -->
                <input class="form-control" name="order_discount" value="{{ $order->order_discount }}" />
            </div>
        </div>
        <!-- Reseller Profit -->
        <div class="col-sm-3 col-xs-6 px-0">
            <div class="form-group">
                <label> Reseller Profit</label>
                <!-- <p class="pb-2 text-center" >Rs: {{ $order->charges }}</p> -->
                <input class="form-control" name="reseller_profit" value="{{ $order->reseller_profit }}" readonly />
            </div>
        </div>
    </div>
    <!--Grand Total-->
    <div class="col-lg-6 row">
        <div class="col-sm-3 col-xs-6 px-0">
            <div class="form-group">
                <label><i class="me-1 fa-solid fa-file-invoice"></i> Grand
                    Total</label>
                <!-- <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">Rs: {{ $order->grandTotal }}</p> -->
                <input class="form-control" required name="grandTotal" value="{{ $order->grandTotal }}" />
            </div>
        </div>

        <!--Grand Profit-->
        <div class="col-sm-3 col-xs-6 px-0">
            <div class="form-group ms-1">
                <label><i class="me-1 fa-solid fa-file-invoice"></i> Grand
                    Profit</label>
                <input class="form-control" required name="grandProfit"
                    value="{{ $order->orderitems_sum_reseller_profit > 0 ? $order->orderitems_sum_reseller_profit : $order->grandProfit }}" />
            </div>
        </div>

        <!-- amount deduct from reseller balance -->
        <div class="col-sm-6 col-xs-8 px-0">
            <label class="text-warning me-2">Amount Deduct from Balance</label>
            <div class="form-group d-flex align-items-end">
                <input type="number" disabled class="form-control flex-grow-1" value="{{ $purchaseTotal }}">
                <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal"
                    data-bs-target="#deductResellerBalance"><i class="fa fa-eye"></i></button>
            </div>
        </div>
    </div>
</div>
