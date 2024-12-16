@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4 scree_bg">
        <a href="{{url('/')}}" class="btn back-btn white_text text-decoration-none mt-2" style="z-index: 9999;">
            <i class="fa fa-chevron-left"></i>
        </a>
        <div class="row my-5">
            <h4 class="text-white">My Orders</h4>
            <div class="row text-white">
                <div class="col-md-6">
                    <h5>Paid Balance:</h5>
                    <p class="p-0 m-0">Rs.{{$paid_balance}}/-</p>
                </div>
                <div class="col-md-6">
                    <h5>Un-paid Balance:</h5>
                    <p class="p-0 m-0">Rs.{{$unpaid_balance}}/-</p>
                </div>
                <div class="col-md-6 py-2">
                    <h5>Pending Balance:</h5>
                    <p class="p-0 m-0">Rs.{{$pending_balance}}/-</p>
                </div>
                <div class="col-md-6 py-2">
                    <h5>Cancel Balance:</h5>
                    <p class="p-0 m-0">Rs.{{$cancel_balance}}/-</p>
                </div>
            </div>
            {{-- @if($orders->isEmpty())
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
            @endif --}}
            <div class="orders-div">
                @include('themes.wao.Orders.partial.all_order', ['orders' => $orders])
            </div>

            
            {{-- <div class="col-12 col-lg-6 my-2">
                <div class="card orderCard">
                    <p class="order_id">Order Id: 10451</p>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">16/02/2024 at 10:52 AM</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Amount: 3430</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Status: CANCEL</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Phone: 123456789</h6>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end p-2">
                        <a href="{{route('order.detail')}}" class="btn button1 text-white">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <div class="card orderCard">
                    <p class="order_id">Order Id: 10451</p>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">16/02/2024 at 10:52 AM</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Amount: 3430</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Status: CANCEL</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Phone: 123456789</h6>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end p-2">
                        <a href="{{route('order.detail')}}" class="btn button1 text-white">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 my-2">
                <div class="card orderCard">
                    <p class="order_id">Order Id: 10451</p>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">16/02/2024 at 10:52 AM</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Amount: 3430</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Status: CANCEL</h6>
                        </div>
                        <div class="col-md-6 col-12">
                            <h6 class="card-title">Phone: 123456789</h6>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-end p-2">
                        <a href="{{route('order.detail')}}" class="btn button1 text-white">Detail</a>
                    </div>
                </div>
            </div> --}}
        </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    
	let offset = 10;
	var canLoad = true;
    $(document).ready(function(){
		$(window).scroll(function(){
			if($(window).scrollTop()>= $(document).height() - $(window).height()){
				// console.log(canLoad);
				if (canLoad == true) {
					console.log("Reached bottom of page");
					canLoad = false;
					loadMore();
				}
			}
		});
	});

	function loadMore(){
		offset += 10;
		$.ajax({
			url: "{{ route('web.profit.adminOrderLoadMore') }}?offset="+offset,
			type: 'GET',
			dataType: 'json',
			success: function(response){
                // console.log(response);
				$(".orders-div").append(response.html);
				canLoad = true;
			},
			error: function(xhr, status, error) {
				console.error(xhr.responseText);
			}
		});
	}
</script>
@endsection