@foreach($products as $index => $item)
@php $index = $index + $plusIndex @endphp
<div class="@if($layout == 1) col-12 @else col-4 @endif product_col_section p-0 video-card bg-white" id="video-card{{$item->id}}" style="calc(height:100% - 500px) !important" data-video='@json($item)'>
  {{-- product id --}}
  <input type="hidden" id="id" value="{{$item->id}}" />
  {{-- original price --}}
  <input type="hidden" id="original_price" value="{{(double)$item->price }}" />
  {{-- price = product price + reseller profit --}}
  <input type="hidden" id="price" class="price{{$item->id}}" value="{{(double)$item->price + (double)$item->reseller_product_profit}}" />
  {{-- reseller profit --}}
  <input type="hidden" id="reseller_profit" class="reseller_profit{{$item->id}}" value="{{(double)$item->reseller_product_profit}}" />
  {{-- qty in cart --}}
  <input type="hidden" id="qtyInCart" value="0" />
  {{-- qty in stock --}}
  <input type="hidden" id="qtyInStock" value="{{$item->soldItem}}" />
  
  <!-- large cards -->          
  <div class="h-100 large-cards @if($layout == 2) d-none @endif">
    <div style="">
      <img loading="lazy" src="{{$item->thumbnail}}" alt="{{$item->name}}" width="100%" height="100%" class="play-video-thumbnail">
    </div>

    {{-- <div id="videoModal" class="video video-section"></div> --}}

    {{-- done/sold button --}}
    <div class="product_footer_section px-2 py-4 w-100 clearfix d-none-">
      <div class="float-start">
        @if($item->soldItem > 0 && $item->soldStatus == 0)
          @if (auth()->guard('admin')->check() && request()->showUpdate == 1)
            <button class="btn update-seller-profit text-color mb-1 sellerProfitModalShow update-seller-profit{{$item->id}}" data-productid="{{$item->id}}">Update</button><br>
          @endif
          <button class="btn done-button text-color" data-index="{{$index}}">Done</button>
          <div class="btn done-button-counter counter w-100" style="display: none;" data-index="{{$index}}">
            <span class="minusBtn text-color">-</span>
            <span class="count" data-productid="{{$item->id}}">0</span>
            <span class="plusBtn text-color">+</span>
          </div>
        @else
          <button class="btn sold-out w-100" style="">Sold Out</button>
        @endif
      </div>
    </div>
    {{-- <div class="product-bottom-right-icons text-end"></div> --}}
  </div>

  <!-- medium cards -->
  <div class="p-1 medium-cards @if($layout == 1) d-none @endif">
    <div class="w-100 bg_yellow m-0">
        <h5 class="text-center mb-0">{{$item->article}}</h5>
    </div>
    <div class="w-100 bg_gray">
        <p class="text-center text-white mb-2">Rs. <span class="price{{$item->id}}">{{(double)$item->price + (double)$item->reseller_product_profit}}</span></p>
    </div>
    <div class="medium-full-height-video" style="margin-top: -8px;">
      <div class="main-medium-video-section position-relative">

        <div class="medium-video medium-video-section" muted controls style="width:100%;height: 100%;">
          <img src="{{$item->thumbnail}}" alt="Thumbnail Image" style="height: 100%" width="100%" class="thumbnail play-video-thumbnail">
        </div>
      </div>
    </div>
    @if($item->soldItem > 0 && $item->soldStatus == 0)
      <button class="btn done-button w-100 rounded-0 py-1 btn-block text-color" data-index="{{$index}}">Done</button>
      <div class="btn done-button-counter counter w-100 rounded-0" style="display: none; width:100%" data-index="{{$index}}">
        <span class="minusBtn text-color">-</span>
        <span class="count" data-productid="{{$item->id}}">0</span>
        <span class="plusBtn text-color">+</span>
      </div>
    @else
      <button class="btn sold-out w-100" style="">Sold Out</button>
    @endif
  </div>
</div>
@endforeach
@push('script')
  <script>
    function imageLoaded(index) {
      document.getElementById('image' + index).style.display = 'block';
    }
  </script>
@endpush