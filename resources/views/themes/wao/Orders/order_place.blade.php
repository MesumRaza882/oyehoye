@extends('themes.wao.layouts.main')
@php
  if(auth()->check()){
    $name = auth()->user()->name;
    $phone = auth()->user()->phone;
    $address = auth()->user()->address;
  }else{
    $name = '';
    $phone = '';
    $address = '';
  }
@endphp
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-md-5 col-lg-4 scree_bg position-relative">
      <a href="{{url('/')}}" class="btn back-btn white_text text-decoration-none mt-2">
        <i class="fa fa-chevron-left"></i>
      </a>

      <div class="row my-5" style="padding-bottom: 100px;">
        @if($name)
          <div class="user_information rounded">
            <div class="d-flex justify-content-between">
              <div class="detail white_text">
                <h5 class="p-0 m-0">
                  {{ $name }}
                </h5>

                <h6 class="p-0 m-0">
                  {{ $phone }}
                </h6>
                <h6 class="p-0 m-0">{{ $address }}</h6>
              </div>
            </div>
          </div>
        @endif

        <div class="delivery_details">
          <h5 class="yellow_text">Delivery Details</h5>
        </div>

        <form action="{{ route('order.place') }}" method="POST" id="submitOrder">
            @csrf
            <div class="row">
              <div class="col-12">
                <div class="form-group py-2">
                  <label for="" class="white_text py-1">Name:</label>
                  <input type="text" placeholder="Name" name="name" id="name" value="{{ auth()->check() ? auth()->user()->name : '' }}" class="form-control" required>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group py-2">
                  <label for="" class="white_text py-1">Whatsapp:</label>
                  <input type="number" placeholder="Whatsapp" name="whatsapp" id="whatsapp" value="{{ auth()->check() ? auth()->user()->whatsapp : '' }}" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group py-2">
                  <label for="" class="white_text py-1">Phone:</label>
                  <input type="number" placeholder="Phone" name="phone" id="phone" value="{{ auth()->check() ? auth()->user()->phone : '' }}" class="form-control">
                </div>
              </div>
              <div class="col-12">
                <div class="form-group py-2">
                  <label for="" class="white_text py-1">City:</label>
                  <select name="city_id" class="form-control" id="city_id" required>
                    <option value="">Select City</option>
                    @php
                      $cityId=auth()->check() ? auth()->user()->city_id : '';
                    @endphp
                    @foreach($cities as $item)
                      <option value="{{$item->id}}" @if($item->id == $cityId) selected @endif>{{$item->c_city_name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-12">
                  <div class="form-group py-2">
                    <label for="" class="white_text py-1">Address:</label>
                    <textarea type="text" placeholder="Address" name="address" id="address" class="form-control" style="border-radius: 20px;" required>{{ auth()->check() ? auth()->user()->address : '' }}</textarea>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group py-2">
                    <label for="" class="white_text py-1">Notes (Optional):</label>
                    <textarea type="text" placeholder="Enter here" name="note" class="form-control" style="border-radius: 20px;"></textarea>
                  </div>
                </div>
                <div class="d-flex justify-content-between">
                  <h5 class="white_text">Cart Items</h5>
                  <a href="javascript:void(0)" class="rose_text text-decoration-none" onclick="removeAllCartItem()">
                    Clear
                  </a>
                </div>
                <!--{{--<div class="card-products d-flex">
                    <div>
                        <a href="javascript:void(0)" class="mx-3 bg_rose pt-0 black_text text-decoration-none">x</a>
                    </div>
                    <img src="{{asset('themes/wao/images/t-shirt.webp')}}" width="100px" class="rounded" alt="">
                    <h5 class="white_text p-2">T-shirt</h5>
                    <span class="pt-4 white_text"><span class="yellow_text">1 x</span> 1490</span>
                </div>--}}-->
                <div id="totalCartItems">
                  @php
                    $totalResellerProfit=0;
                  @endphp
                  @foreach($cartItems as $key=> $cartItem)
                    @php
                      $totalResellerProfit+=$cartItem->reseller_product_profit;
                    @endphp
                    <div id="cartItems{{$key}}">
                      <input type="hidden" name="product_id[]" value="{{$cartItem->product_id}}" />
                      <div class="card-products clearfix mb-2">
                        <div style="width:25px" class="float-start">
                          <a href="javascript:void(0)" class="bg_rose text-white text-decoration-none" onclick="removeCartItem('{{$key}}')">
                            <i class="fa fa-times-circle text-danger"></i>
                          </a>
                        </div>
                        <div style="width:80px" class="float-start">
                          <img src="{{$cartItem->thumbnail}}" width="70px" height="70px" class="rounded float-left" alt="">
                        </div>
                        <div style="width:calc(100% - 200px); overflow: hidden; text-overflow: ellipsis;display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; -webkit-line-clamp: 3; line-height: 1.5; max-height: calc(1.5 * 2);" class="float-start">
                          <h5 class="white_text p-2 d-inline" style="font-size: 16px;">{{$cartItem->name}}</h5>
                        </div>
                        <div style="width:90px" class="text-end float-start">
                          {{-- yellow_text --}}
                          <span class="pt-4 white_text d-inline"><span class="text-white">{{$cartItem->qty}} x</span> {{$cartItem->product_price+$cartItem->reseller_profit_price}}</span>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  <input type="hidden" name="profit" id="profit" value="{{ $totalResellerProfit }}" class="form-control">
                </div>
            </div>
        </form>
      </div>

      <div style="position:fixed;bottom:0px;margin-top:10px;left:0px;right:0px;">
        <div class="row justify-content-center">
          <div class="col-12 col-md-5 col-lg-4 px-0" style="background-color:#000">
            <div class="px-4 py-3">
              <div class="d-flex justify-content-between">
                <h4 class="white_text">Delivery Charges</h4>
                <h6 class="text-white">RS <span id="delivery_amount">{{$delivery_charges}}</span></h6>
              </div>
              <div class="d-flex justify-content-between">
                <h4 class="white_text">Current Order Total</h4>
                <h6 class="text-white">RS <span id="currentOrderTotal">{{$total_amount}}</span></h6>
              </div>
              <button type="submit" form="submitOrder" class="btn button1 btn-block w-100 white_text text-decoration-none">Confirm Order</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#city_id').select2({
        width: '100%'
      });
    });
  </script>

  <script>
    toastr.options = {
      'closeButton': true,
      'debug': false,
      'newestOnTop': false,
      'progressBar': false,
      'positionClass': 'toast-top-right',
      'preventDuplicates': false,
      'showDuration': '1000',
      'hideDuration': '1000',
      'timeOut': '5000',
      'extendedTimeOut': '1000',
      'showEasing': 'swing',
      'hideEasing': 'linear',
      'showMethod': 'fadeIn',
      'hideMethod': 'fadeOut',
    }

    $("#submitOrder").submit(function(e){
      e.preventDefault();
      var formData = $(this).serialize(); // Serialize the form data
      // Example: Send form data using AJAX
      $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: formData,
        success: function(response) {
          console.log(response);
          // Handle success response
          console.log('Form submitted successfully');
          // toastr["success"](response.message);
          window.location.replace('{{route("order.complete")}}?order_id='+response.data.order_id);
        },
        error: function(xhr, status, error) {
          if (xhr.status == 422) {
            // if input validation failed
            // every input after should have empty div tag <div></div>
            // $.each(xhr.responseJSON.error,function(field_name,error){
              // findFiled = $(thisForm).find('[name='+field_name+']');
              // var errorELementId = 'ajErrorDiv'+field_name;
              // if ($("#"+errorELementId).length > 0){
              //   $("#"+errorELementId).text(error);
              // }else{
              //   findFiled.after('<div class="text-strong text-danger" id="' +errorELementId+ '">' +error+ '</div>')
              // }
            // })
            toastr["error"](xhr.responseJSON.message);
          }else{
            toastr["error"]('something went wrong');
          }

          // Handle error
          console.error('Error:', xhr.status);
          console.error('Error:', xhr.responseJSON.message);
          console.error('Error:', error);
        }
      });
    });

    $("#whatsapp").on('change', function(){

      whatsapp = $(this).val();
      $.ajax({
        type: 'GET',
        url: "{{ route('user.info.by.whatsapp') }}?whatsapp="+whatsapp,
        success: function(response) {
          if(response.data){
            if(response.data.name){
              $('#name').val(response.data.name);
            }

            if(response.data.order && response.data.order.address){
              $('#address').val(response.data.order.address);
            }

            if(response.data.order && response.data.order.city_id){
              $('#city_id').val(response.data.order.city_id);
            }
          }
        },
      });
    });
    function removeCartItem(index) {
      $.ajax({
        type: 'GET',
        url: "{{ route('order.remove.cart.item') }}",
        data:{'remove_item':index},
        success: function(response) {
          // console.log(response);
          $('#cartItems'+index).remove();
          $('#delivery_amount').html(response.delivery_charges);
          $('#currentOrderTotal').html(response.total_amount);
          $('#profit').val(response.totalResellerProfit);
        },
      });
    }

    function removeAllCartItem() {
      $.ajax({
        type: 'GET',
        url: "{{ route('order.remove.all.cart.item') }}",
        success: function(response) {
          $('#delivery_amount').html(0);
          $('#currentOrderTotal').html(0);
          $('#profit').val(0);
          $('#totalCartItems').empty();
          // console.log(response);
        },
      });

    }
    
  </script>
@endsection