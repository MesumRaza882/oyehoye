@extends('users.layouts.user_app')
@section('content')


<header class="header">
    <div class="container-fluid ">
        <div class="row g-0 d-flex position-relative align-items-center justify-content-md-between justify-content-end px-2">
            <!-- contact button -->
            <div class="col-auto order-md-1 order-2 text-end">
                <div class="top_contact d-flex align-items-center">
                    <button class="btn btn-design p-2 text-white fw-bold">
                    <i class="fa-solid fa-phone me-1"></i> 03214545453
                    </button>
                    <button class="btn btn-design p-2  mx-md-2 mx-1 text-white fw-bold">
                    <i class="fa-solid fa-phone me-1"></i> 03214545453
                    </button>
                </div>
            </div>

            <!-- image  -->
            <div class="col-auto order-1 order-md-2 ">
                <div class="position-absolute img-container">
                    <img src="https://picsum.photos/200" class="img-fluid rounded-circle" width="120px" height="100px" alt="">
                </div>
            </div>

            <!-- icon toggle -->
            <div class="col-auto text-end order-3 d-flex align-items-center">
                <span class="position-relative me-3 fs-5 text-white mt-2 d-lg-block d-none">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="position-absolute  top-0 start-100 translate-middle badge bg-info p-1 rounded-pill ">
                        3
                    </span>
                </span>

                <span>
                    <i class=" btn-design p-md-2  p-1 text-white fs-md-4 fs-5 fa-solid fa-sliders"></i>
                </span>
            </div>
        </div>
    </div>
</header>

<!-- main section category item items -->
<livewire:products/>


@endsection