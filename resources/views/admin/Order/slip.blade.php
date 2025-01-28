@extends('admin.layouts.appv2')
@section('title') Orders Slip @endsection
@section('style')
    <style>
        .vertical-caption {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            white-space: nowrap; /* Prevent text from wrapping */
        }
        td,th,.border,.table-bordered{
            border:1px solid black !important;
        }
        th{
            background-color:#ffff !important;
        }
        @media print {
            body{
                background-color: #ffff;
            }
            th{
                background-color:black !important;
            }
            .checklist {page-break-after: always;}
            td,th,.border,.table-bordered{
                border:1px solid black !important;
            }
        }

        th, td{height: unset !important; padding-top:5px !important; padding-bottom:5px !important}
    </style>
@endsection
@section('content')

<div class="main-content">
    <div class="pt-5 mb-4 container bg-white">
        <div>
            <button type="button" class="btn btn-primary" onclick="printDiv('printdiv')">Print</button>
        </div>
        <div class="row" id="printdiv">
            <div class="col-md-12 checklist">    
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tracking Id</th>
                            <th>Barcode</th>
                            <th>Product</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @if($order->courier_tracking_id)
                                <tr>
                                    <td>{{$order->courier_tracking_id}}</td>
                                    <td>
                                        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->courier_tracking_id."", 'C39E+')}}" style="width: 300px;height: 50px;" />
                                    </td>
                                    <td class="pb-0">
                                        @foreach ($order->orderitems as $orderDetail)
                                            <div class="d-flex mb-2">
                                                <img class="mr-4" src="{{$orderDetail->product->thumbnail}}" style="width:50px; height:50px">
                                                <span class="mr-4">{{$orderDetail->product->name}}</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex  flex-column">
                                            @foreach ($order->orderitems??[] as $orderDetail)
                                                <p class="">{{$orderDetail->qty??''}}</p>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @foreach ($orders as $order)
                @if($order->courier_tracking_id)
                    <div class="col-md-12">
                        <p class="fs-10 text-right mb-1">
                            <span class="fw-700">Print On:</span> {{date('d-m-Y')}}
                        </p>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="2" rowspan="2" class="p-1" style="width: 21%;">
                                        <div class="d-flex justify-content-center text-capitalize fw-bold h3">
                                            {{$order->tracking_order_type}}
                                        </div>
                                    </td> 
                                    <td colspan="3" rowspan="2" class="p-1 text-center">
                                        {{-- horizontal qr code --}}
                                        <div class="d-flex justify-content-center">
                                            @php
                                                $cn=$order->courier_tracking_id??$order->id;
                                            @endphp
                                            <img class="my-2" src="data:image/png;base64,{{DNS1D::getBarcodePNG($cn."", 'C39E+')}}" style="width: 380px;height: 50px;" />
                                        </div>
                                        <small class="text-center fw-bold">{{$cn??'N/A'}}</small>
                                    </td>
                                    <td class="p-1 text-center align-middle">COD Amount</td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-center align-middle fw-bold">{{ $order->grandTotal }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="p-1 text-center align-middle">Overnight</td>
                                    <td colspan="4" rowspan="2">
                                        <b class="d-block">Customer</b>
                                        <b class="d-block">{{$order->name}}</b>
                                        <b class="d-block">{{$order->phone}}</b>
                                        <b class="d-block">{{$order->address}}</b>
                                    </td>
                                    {{-- <td rowspan="2" class="vertical-caption p-1 text-center align-middle border" style="width: 1%;">Consignee</td>
                                    <td colspan="2" class="p-1 text-center align-middle fw-bold">{{ $order->name }}</td>
                                    <td class="p-1 text-center align-middle fw-bold">{{$order->phone}}</td> --}}
                                </tr>
                                <tr>
                                    <td class="fs-10 p-1 text-center align-middle">Order Ref No#</td>
                                    <td class="fs-10 p-1 text-center align-middle">{{$order->id??''}}</td>
                                    {{-- <td colspan="3" class="p-1 text-center align-middle fw-bold">{{$order->address}}</td> --}}
                                </tr>
                                <tr>
                                    <td class="fs-10 p-1 text-center align-middle">Process</td>
                                    <td class="fs-10 p-1 text-center align-middle">1</td>
                                    <td colspan="4" rowspan="2">
                                        <b class="d-block">Shipper</b>
                                        <b class="d-block">{{$order->userdetail->name ??''}}</b>
                                        <span class="d-block">{{$order->userdetail->phone ??''}}</span>
                                        <span class="d-block">{{$order->userdetail->bussiness_detail->store_address ?? ''}}</span>
                                    </td>
                                    {{-- <td rowspan="3" class="p-1 vertical-caption text-center align-middle border" style="width: 1%;">Shipper</td>
                                    <td colspan="2" class="p-1 text-center align-middle">{{$order->userdetail->name??''}}</td>
                                    <td style="width: 27%;" class="p-1 text-center align-middle">{{$order->userdetail->phone ??''}}</td> --}}
                                </tr>
                                <tr>
                                    <td class="fs-10 p-1 text-center align-middle">Weight</td>
                                    <td class="fs-10 p-1 text-center align-middle">0.5</td>
                                    {{-- <td class="p-1 text-center align-middle" style="width: 12%;">
                                        Pickup Address
                                        <hr class="my-0">
                                        Return Address
                                    </td> --}}
                                    
                                    {{-- <td colspan="3" class="p-1 text-center align-middle">{{$order->userdetail->bussiness_detail->store_address??''}}</td> --}}
                                </tr>
                                <tr>
                                    <td class="fs-10 p-1 text-center align-middle">Insurance Value</td>
                                    <td class="fs-10 p-1 text-center align-middle">0</td>
                                    
                                    <td colspan="4" class="py-3 px-2 align-middle">
                                        Remark: 
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="py-3 px-2 align-middle text-nowrap">
                                        @php
                                        $productNames='';
                                        foreach ($order->orderitems??[] as $key => $value) {
                                            $productNames.=$value->product->name;
                                            $productNames.=', ';
                                        }
                                        @endphp
                                        <strong>Product Detail:</strong> {{ \Illuminate\Support\Str::limit($productNames, 120, $end='...')}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        function printDiv(divPrint) {
            var printContents = document.getElementById(divPrint).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            document.title = "Order Slip";
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
        
@endsection