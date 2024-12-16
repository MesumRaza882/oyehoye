@extends('themes.wao.layouts.main')
@section('content')

<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-12 col-md-5 col-lg-4 scree_bg">
			<a href="{{url('/orders')}}" class="btn back-btn white_text text-decoration-none mt-2">
				<i class="fa fa-chevron-left"></i>
			</a>
			<div class="row my-5 justify-content-center">
				<div class="col-12">
					<div class="price d-flex justify-content-end">
						<h4 class="yellow_text">Rs.{{ $order->orderitems->sum('price') + $order->charges }}</h4>
					</div>
					<h5 class="white_text">Order Details</h5>
					<h6 class="yellow_text">Order id: {{$order->id}}</h6>
					<div class="status text-center py-4">
						<span class="white_text">Status:</span> <span class="yellow_text">{{$order->status}}</span>
					</div>
					
					<div class="d-flex justify-content-center mb-3">
						<a href="" class="btn button2 black_text px-5">{{$order->date}}</a>
					</div>
					<span class="white_text">Status: {{$order->status}}</span>
					<div class="product my-3">
						@foreach($order->orderitems as $item)
							<div class="d-flex white_text">
								<img src="{{asset($item->product->thumbnail)}}" width="100px" height="80px" class="rounded" alt="">
								<h5 class="px-3">{{ $item->product->name }}</h5>
							</div>
							<div class="d-flex justify-content-end">
								<p class="p-3 white_text"><span class="yellow_text">{{ $item->qty }} x</span>{{ $item->price }}</p>
							</div>
						@endforeach
					</div>
					<div class="d-flex justify-content-between white_text mt-3">
						<p>Delivery Charges</p>
						<p>Rs.{{$order->charges}}</p>
					</div>
					<div class="d-flex justify-content-between white_text">
						<p>Adjustment</p>
						<p>0</p>
					</div>
					<div class="d-flex justify-content-between">
						<p class="white_text">Total</p>
						<h5 class="yellow_text">Rs. {{ $order->orderitems->sum('price') + $order->charges }}</h5>
					</div>
					
					<hr class="white_text">
					<div id="orderNotes" class=" my-2">
						@foreach ($order->notes??[] as $item)
							<p><span style="color: #0ec80e;">Your Msg =></span>&nbsp;&nbsp;&nbsp;<span class="white_text">{{$item->note}}</span></p>
						@endforeach
					</div>
					<p class="white_text">For advance payment or any adjustment and other query</p>

					<form class="my-2" id="orderNoteForm" action="{{ route('order.note') }}" method="post">
						@csrf
						<div class="col-12 mx-auto my-2">
							<input type="hidden" name="order_id" value="{{ $order->id }}">
							<input type="text" class="form-control" name="note" id="note" placeholder="Enter your note here">
						</div>
						<div class="d-flex justify-content-end">
							<button type="button" id="sendNotesBtn" class="btn button1 white_text">Send Notes</button>
						</div>
					</form>

					<div class="user_information rounded">
						<div class="d-flex justify-content-between">
							<div class="detail white_text">
								<h5 class="p-0 m-0" id="nameLabel">{{$order->name}}</h5>
								<h6 class="p-0 m-0" id="phoneLabel">{{$order->phone}}</h6>
								<h6 class="p-0 m-0" id="addressLabel">{{$order->address}}</h6>
							</div>
							@if($order->status == 'PENDING')
								<div class="edit_button white_text" data-bs-toggle="modal" data-bs-target="#addressModal">
									<i class="fa-solid fa-pen"></i>
								</div>
							@endif
						</div>
					</div>

					@if($order->status == 'PENDING')
						<button id="cancelOrder" class="btn d-block w-100 cancel-order">Cancel Order</button>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>

<!-- address modal -->
<!-- Modal -->
<div class="modal fade-" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="position:fixed;top:auto;right:auto;left:auto;bottom:0;">
        <div class="modal-content">
            <div class="modal-body" style="background: black; border-radius:5px;">
                <form id="addressForm" action="{{route('order.update.delivery.address')}}" method="post">
									<input type="hidden" name="order_id" value="{{ $order->id }}">
                    @csrf
                    <div class="row">
                        <div class="col-12 clearfix">
													<h6 class="yellow_text float-start">Delivery Details</h6>
													<i id="closeAddressModal" class="fa fa-times text-white float-end"></i>
                        </div>
    
                        <div class="col-12">
                          <div class="form-group py-2">
                            <label for="" class="white_text py-1">Name:</label>
                            <input type="text" placeholder="Name" name="name" id="name" value="{{ $order->name??''  }}" class="form-control" required>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form-group py-2">
                            <label for="" class="white_text py-1">Phone:</label>
                            <input type="number" placeholder="Phone" name="phone" id="phone" value="{{ $order->phone??'' }}" class="form-control" required>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form-group py-2">
                            <label for="" class="white_text py-1">City:</label>
                            <select name="city_id" class="form-control select2" id="city_id" required>
                              <option value="">Select City</option>
                              @foreach(\App\Models\City::orderBy('id','desc')->get() as $item)
                                <option value="{{$item->id}}" @if (($order->city_id??'')==$item->id) selected @endif>{{$item->c_city_name}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group py-2">
                                <label for="" class="white_text py-1">Address:</label>
                                <textarea type="text" placeholder="Address" name="address" id="address" class="form-control" style="border-radius: 20px;" required>{{ $order->address??'' }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn button1 white_text" id="addressSubmitbtn">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
      $('#city_id').select2({
        width: '100%'
      });
    });
</script> --}}
<script>
	$(document).ready(function() {
		$('#cancelOrder').click(function(event) {
			order_id = '{{$order->id}}';
			$.ajax({
				url: "{{ url('order/cancel') }}/"+order_id,
				type: 'get',
				success: function(response) {
					if(response.status == 1){
						toastr["success"](response.message);
					}else{
						toastr["error"](response.message);
					}
				},
				error: function(xhr, status, error) {
					console.error(xhr.responseText);
					var response = JSON.parse(xhr.responseText);
					console.error(response.status);
					if(response.status == 0){
						toastr["error"](response.message);
					}
				}
			});
		});
	});
</script>
<script>
	$(document).ready(function() {
		$('#sendNotesBtn').click(function(event) {
			event.preventDefault();
			var formData = $('#orderNoteForm').serialize();
			$.ajax({
				url: $('#orderNoteForm').attr('action'),
				type: 'POST',
				data: formData,
				success: function(response) {
					// Handle successful response, if needed
					toastr["info"]('Note Submitted Successfully!');
					console.log(response);
					var note=`<p><span style="color: #0ec80e;">Your Msg =></span>&nbsp;&nbsp;&nbsp;<span class="white_text">`+$('#note').val()+`</span></p>`;
					$('#orderNotes').append(note);
					$('#orderNoteForm')[0].reset();
				},
				error: function(xhr, status, error) {
					// Handle errors, if any
					console.error(xhr.responseText);
				}
			});
		});
	});
</script>



<script>
	$(document).ready(function() {
		
		$('#closeAddressModal').click(function(event) {
			$('#addressModal').removeClass('show');
			$('body').removeClass('modal-open');
			$('.modal-backdrop').removeClass('show');
			$('.modal-backdrop').fadeOut();

			$('#addressModal').attr('aria-modal', 'false');
			$('#addressModal').css('display', 'none');
		});

		$('#addressSubmitbtn').click(function(event) {
			event.preventDefault();
			var formData = $('#addressForm').serialize();
			$.ajax({
				url: $('#addressForm').attr('action'),
				type: 'POST',
				data: formData,
				success: function(response) {
					// Handle successful response, if needed
					console.log(response);
					// $('#addressForm')[0].reset();

					toastr["info"](response.message);

					if (response.status==1) {
						$('#nameLabel').html(response.data.name);
						$('#phoneLabel').html(response.data.phone);
						$('#addressLabel').html(response.data.address);

						$('#addressModal').removeClass('show');
						$('body').removeClass('modal-open');
						$('.modal-backdrop').removeClass('show');
						$('.modal-backdrop').fadeOut();

						$('#addressModal').attr('aria-modal', 'false');
						$('#addressModal').css('display', 'none');
					}
				},
				error: function(xhr, status, error) {
					// Handle errors, if any
					console.error(xhr.responseText);
				}
			});
		});
	});
</script>
@endsection