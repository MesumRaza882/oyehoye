<div>
    <section class="mt-5">
        <div class="container">
            <div class="row gx-3 gy-4 category_item_section">
                <h2 class="text-center my-3">New Arrivals</h2>

                @foreach($products as $product)
                <div class="col-lg-4">
                    <div class="row g-0 d-flex align-items-center item-container ">
                        <!-- image item -->
                        <div class="col-4">
                            <a  wire:click="openProductVideoModal({{$product->id}})">
                                <div class="img-container">
                                    <img src="{{$product->thumbnail}}"
                                        class="img-fluid"  alt="">
                                </div>
                            </a>
                        </div>
                        <!-- item desc -->
                        <div class="col-8 ps-2 d-flex align-items-start flex-column align-self-stretch position-relative">
                            <!-- is dc free -->
                            <div class="dc_free_badge {{$product->is_dc_free == 1 ? '' : 'd-none' }} ">
                                <span class="badge">Free DC</span>
                            </div>
                            <div class="pt-2">
                            
                                <h5 class="fw-bold">{{ \Illuminate\Support\Str::limit($product->name, 20, $end = '...') }}</h5>
                                <p class="fs-4 my-3">Rs. {{$product->price}}</p>
                                @if($product->soldstatus != 1 && $product->soldItem > 0)
                                    <p class="auto_sale lh-base">اب تک <span class="badge bg-info">{{$product->soldAdm}}</span> سیل ہو چکے ہیں</p>
                                @endif
                            </div>
    
                            <div class="cart_btn_div ms-auto mt-auto">
                                @if($product->soldstatus == 1 || $product->soldItem <= 0)
                                `   <span class="mt-2 badge bg-danger">Sold-Out</span>
                                @else
                                    <button class="add_to_cart"><i class="fa fa-plus"></i></button>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>
        </div>
    </section>
    @include('modals.video-modal')
</div>

<script type="text/javascript">
    window.onscroll = function(ev) {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
            window.livewire.emit('load-more');
        }
    };

    window.addEventListener('openProductVideoModal', function(event) {
      $("#videoModal").modal('show');
  })
</script>
