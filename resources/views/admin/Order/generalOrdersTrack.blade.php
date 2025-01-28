<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Track</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<style>
    @import url('https://fonts.googleapis.com/css?family=Open+Sans&display=swap');

    body {
        background-color: #eeeeee;
        font-family: 'Open Sans', serif
    }

    .container {
        margin-top: 25px;
        margin-bottom: 25px
    }

    th {
        font-size: 14px !important;
    }

    p {
        margin-top: 0;
        margin-bottom: 1rem;
        font-size: 14px !important;
    }

    span {
        font-size: 13px;
    }

    .accordion-item {
        background-color: #F1F7E2 !important;
    }

    .accordion-button:focus {
        box-shadow: none;
    }
</style>

<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <!-- search form -->
                        <form method="GET" action="{{route('trackOrders')}}" autocomplete="off">
                            <div class="row gx-0 d-felx align-items-center">
                                <div class="col-8">
                                    <input type="text" value="{{request()->get('input')}}" name="input" class="form-control">
                                </div>
                                <div class="col-auto">
                                    <button class="ms-2 btn btn-primary btn-sm" type="submit">Search</button>
                                </div>
                            </div>
                        </form>

                        <!-- accordion to display orders -->

                        <div class="row my-3">
                            <div class="col-12">
                                <div class="accordion" id="accordionExample">
                                    @forelse ($orders as $index => $order)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{$index + 1}}">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$order->id}}" aria-expanded="true" aria-controls="collapse{{$order->id}}">
                                                <div>
                                                    <div class="d-flex mb-2 align-items-center">
                                                        <div class="text-info">
                                                            <span>Order# </span><span>{{$order->id}}</span>
                                                        </div>
                                                        <div>
                                                            <span class="ms-2">Date: </span>
                                                            <span>{{ Carbon\Carbon::parse($order->created_at)->format('d-M-Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="d-inline-block badge {{ $order->status == 'Delivered' || $order->status == 'Out For Delivery' || $order->status == 'ON-THE-WAY' ? 'bg-success' : ($order->status == 'CANCEL' || $order->status == 'Refused By Customer' ? 'bg-danger' : 'bg-info') }} text-white p-1">Status : {{$order->status}}</span>
                                                    <span class="d-inline-block badge bg-primary text-white p-1">COD : {{$order->grandTotal}}</span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{$order->id}}" class="accordion-collapse collapse {{$index === 0 ? 'show' : ''}}" aria-labelledby="heading{{$index + 1}}" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <!-- order product  -->
                                                <div class="row">
                                                    <div class="col-sm-12 ">
                                                        <article class="card">
                                                            <div class="card-body row gy-2">
                                                                <div class="col-6">
                                                                    <p class=" mb-0"><strong>Customer:</strong> {{ $order->name }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <p class=" mb-0"><strong>Contact # </strong> {{$order->phone}}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <p class=" mb-0"><strong>City: </strong> {{ $order->citydetail->c_city_name }}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <p class=" mb-0"><strong>Address: </strong> {{$order->address}}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <p class=" mb-0"><strong>Charges </strong> {{$order->charges}}</p>
                                                                </div>
                                                                <div class="col-6">
                                                                    <p class="mb-1"><strong>Total-Items</strong> # {{$order->orderitems_count}}</p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-hover table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Product Name</th>
                                                                                    <th>Qty</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($order->orderitems as $item)
                                                                                <tr>
                                                                                    <td>
                                                                                        <span>
                                                                                            {{$item->product->name}}
                                                                                        </span>
                                                                                        <a href="{{$item->product->thumbnail}}" target="_blank">
                                                                                            <img src="{{$item->product->thumbnail}}" height="50px" width="50px" alt="Item_Thumbnail" class="rounded-circle" />
                                                                                        </a>
                                                                                    </td>
                                                                                    <td>{{$item->qty }}</td>

                                                                                </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </article>
                                                        <div class="table-responsive">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr class="bg-secondary text-white">
                                                                        <th>
                                                                            <p class="mb-0">Tracking-Status</p>
                                                                        </th>
                                                                        <th>
                                                                            <p class="mb-0">Time</p>
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($order->history as $history)
                                                                    <tr>
                                                                        <td class="fw-bold {{ $history->history == 'Delivered' || $history->history == 'ON-THE-WAY' || $history->history == 'Out For Delivery' ? 'text-success' : ($history->history == 'CANCEL' || $history->history == 'Refused By Customer' ? 'text-danger' : '') }}">
                                                                            <p class=" mb-0">{{ $history->history }}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p class=" mb-0">{{ Carbon\Carbon::parse($history->created_at)->format('d-M-Y / g-a') }}</p>
                                                                        </td>
                                                                    </tr>
                                                                    @empty
                                                                    <tr>
                                                                        <td>
                                                                            <p class=" mb-0">{{$order->status}}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p class=" mb-0">{{$order->date}} {{$order->time}}</p>
                                                                        </td>
                                                                    </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="alert {{request()->get('input') ? 'alert-warning' : 'alert-info' }} ">{{request()->get('input') ? 'No Record Found' : 'Serached By Phone Number/Whatsapp'}} </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</body>

</html>