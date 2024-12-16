<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Generate Order Slip</title>
    <script src="{{asset('assets/js/dom-to-image.js')}}"></script>
</head>

<body>
    <div class="container">
        <div id="box">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="ticket">
                        <table class="table table-responsive table-bordered table-warning">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="p-1" style="width: 21%;">
                                        <div class="d-flex justify-content-center">
                                            <h5>Postex</h5>
                                        </div>
                                    </td>
                                    <td colspan="2" rowspan="1" class="p-1">
                                        <!-- {{-- horizontal qr code --}} -->
                                        <div class="d-flex justify-content-center">
                                            <h6> Order-Slip-Detail</h6>
                                        </div>
                                    </td>
                                    <td colspan="4" class="p-1 fw-bold">CN # :
                                        <span id="slip-order-cn" class="fw-bold">{{$order->courier_tracking_id}}</span>
                                    </td>
                                    <td colspan="2" class="bg-secondary text-white p-1 text-center align-middle fw-bold">COD Amount :
                                        <span id="slip-cod-amount" class="fw-bold" style="font-size: 16px;">{{$order->grandTotal}}.00/-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-1 text-center align-middle fw-bold">Order-Type</td>
                                    <td class="p-1 text-center align-middle" id="slip-order-type">Normal</td>
                                    <td rowspan="2" class="vertical-caption p-1 text-center align-middle bg-secondary text-white fw-bold" style="width: 2%;">Consignee Info</td>
                                    <td colspan="3" class="p-1 text-center align-middle font-weight-bold" id="slip-order-cons-name">{{$order->name}} <br> Contact # {{$order->userdetail->whatsapp}}</td>
                                    <td colspan="2" class="p-1 text-center align-middle font-weight-bold" id="slip-order-cons-city">{{$order->citydetail->c_city_name}}</td>
                                    <td style="width: 35%;" class="p-1 text-center align-middle" id="slip-order-consignee_whatsaapp">{{$order->phone}}</td>
                                </tr>
                                <tr>
                                    <td class="fs-10 fw-bold p-1 text-center align-middle ">Order-Id</td>
                                    <td class="fs-10 p-1 text-center align-middle" id="slip-order-id">{{$order->id}}/{{$order->grandProfit}}/{{$order->orderitems_count}}</td>
                                    <td colspan="6" class="p-1 align-middle"><strong> Address : </strong> <span id="slip-order-consignee_address">{{$order->address}}</span></td>
                                </tr>
                                <tr>
                                    <td class="fs-10 fw-bold p-1 text-center align-middle">Date</td>
                                    <td class="fs-10 p-1 text-center align-middle" id="slip-order-date">{{$order->date}}</td>
                                    <td rowspan="3" class="p-1 vertical-caption text-center align-middle bg-secondary text-white fw-bold" style="width: 2%;">Shipper Info</td>
                                    <td colspan="5" class="p-1 align-middle">Wao Imported Collection / N</td>
                                    <td style="width: 35%;" class="p-1 align-middle">03007307110
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fs-10 fw-bold p-1 text-center align-middle">Pieces</td>
                                    <td class="fs-10 p-1 text-center align-middle" id="slip-order-pieces">{{$order->orderitems_count}}</td>
                                    <td class="p-1 text-center align-middle" style="width: 15%;">
                                        Pickup Address
                                        <hr class="my-0">
                                        Return Address
                                    </td>
                                    <td colspan="6" class="p-1 text-center align-middle">Multan</td>
                                </tr>
                                <tr>
                                    <td class="fs-10 fw-bold p-1 text-center align-middle">Charges</td>
                                    <td class="fs-10 p-1 text-center align-middle" id="slip-order-charges">{{$order->charges}}</td>

                                    <td colspan="6" class="py-3 px-2 text-left">
                                        <span class="fw-bold">Remarks:</span> <span id="slip-order-remarks">Other Contact # {{$order->userdetail->whatsapp}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10" class="py-3 px-2 text-center">
                                        <span class="fw-bold">Product Details:</span> <span id="slip-order-productDetail">{{$order->description}}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <center>
            <button data-route-url="{{ route('waoseller.order.index') }}" id="dw_bt" value="{{$order->id}}" class="btn btn-sm btn-info text-white m-auto">Generate Slip</button>
        </center>
    </div>
    <script>
        var download_button = document.getElementById("dw_bt");

        download_button.addEventListener("click", () => {
            makeSlip(download_button.value);
        });

        function makeSlip(orderId) {

            domtoimage.toJpeg(document.getElementById("box")).then((dataUrl) => {

                var formData = new FormData();
                formData.append('image', dataUrl);
                formData.append('order_id', orderId);

                // Add CSRF token to the FormData
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("waoseller.slip.store") }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => {
                        console.log(response);
                        if (!response.ok) {
                            throw new Error('Network response was not okk');
                        }
                        return response.json();
                    })
                    .then(data => {
                        var routeUrl = document.querySelector('[data-route-url]').dataset.routeUrl;
                        window.location.href = routeUrl;
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                        alert(error);
                    });


            });
        }
    </script>

</body>

</html>