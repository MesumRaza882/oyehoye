<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url('https://fonts.googleapis.com/css?family=Open+Sans&display=swap');

        body {
            background-color: #eeeeee;
            font-family: 'Open Sans', serif
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px
        }


        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1)
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem
        }

        .btn-warning {
            color: #ffffff;
            background-color: #ee5435;
            border-color: #ee5435;
            border-radius: 1px
        }

        .btn-warning:hover {
            color: #ffffff;
            background-color: #ff2b00;
            border-color: #ff2b00;
            border-radius: 1px
        }

        p {
            font-size: 14px !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <article class="card">
            <header class="card-header"> My Orders / Tracking </header>
            <div class="card-body">
                <h6>Order ID: {{$order->id}}</h6>
                <article class="card">
                    <div class="card-body row gy-2">
                        <div class="col-6">
                            <p class=" mb-0"><strong>Order Created</strong> <br>{{ $order->created_at->format('j/n/Y \a\t g:i a') }}</p>
                        </div>
                        <div class="col-6">
                            <p class=" mb-0"><strong>Total Amount</strong> <br> <i class="fa-solid fa-rupee-sign"></i> - {{$order->grandTotal}}</p>
                        </div>
                        <div class="col-6 d-flex flex-column">
                            <p class=" mb-0"><strong>Status</strong> <br> {{$order->status}}</p>
                            <p class=" mb-0 {{$order->cancel_note ? '' : 'd-none'}}"><strong>Note</strong>: {{$order->cancel_note}}</p>
                        </div>
                        <div class="col-6">
                            <p class=" mb-0"><strong>Tracking #</strong> <br> {{$order->courier_tracking_id}}</p>
                        </div>
                    </div>
                </article>
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr class="bg-secondary text-white">
                                <th>
                                    <p class="mb-0">Status</p>
                                </th>
                                <th>
                                    <p class="mb-0">Time</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->history as $history)
                            <tr>
                                <td>
                                    <p class=" mb-0">{{$history->history}}</p>
                                </td>
                                <td>
                                    <p class=" mb-0">{{ $history->created_at->format('j/n/Y \a\t g:i a') }}</p>
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
        </article>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.jss"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>