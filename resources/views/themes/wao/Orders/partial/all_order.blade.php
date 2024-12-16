
@if($orders->isEmpty())
<p class="text-center white_text">No Orders Found !</p>
@else 
@foreach ($orders as $item)
    <div class="col-12 my-2">
        <div class="card orderCard">
            <p class="order_id scree_bg">
                Order Id: {{$item->id}} <br>
                <span class="text-white">profit: {{$item->reseller_profit??0}}</span>
            </p>
            <div class="card-body px-2 py-1">
                <div class="row">
                    <div class="col-12">
                        <p class="card-title mb-0 fw-bold">{{$item->date}} at {{$item->time}}</p>
                    </div>
                    <div class="col-12">
                        <p class="card-title mb-0 fw-bold">Amount: {{$item->amount}}</p>
                    </div>
                    <div class="col-6">
                        <p class="card-title mb-0 fw-bold">Status: {{$item->status}}</p>
                        <p class="card-title mb-0 fw-bold">Phone: {{$item->phone}}</p>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-end mt-3">
                            <form action="{{route('order.detail')}}" method="post">
                                @csrf
                                <input type="hidden" value="{{$item->id}}" name="order_id" id="">
                                <button type="submit" class="btn btn-sm button1  text-white">Detail</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
@endforeach
@endif