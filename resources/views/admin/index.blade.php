@extends('admin.layouts.app')
@section('content')

<style>
  @import url('https://fonts.googleapis.com/css?family=Oswald:300,400,500,700');
  @import url('https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800');


  * {
    transition: .5s;
  }

  .column {
    margin-top: 2rem;
    /* padding-left:3rem; */
  }

  .card {
    min-height: 170px;
    margin: 0;
    padding: 1.7rem 1.2rem;
    border: none;
    border-radius: 0;
    color: rgba(0, 0, 0, 1);
    letter-spacing: .05rem;
    font-family: 'Oswald', sans-serif;
    box-shadow: 0 0 21px rgba(0, 0, 0, .27);

  }

  .dotmain {
    position: relative;
    font-size: 20px;
  }

  .dot {
    position: absolute;
    bottom: 0px;
    font-size: 30px;
    border-radius: 50%;
  }

  .viedeticon {
    border-radius: 50px;
    background-color: #061121;
    padding: 5px;
    color: yellow;
  }

  .viedeticon:hover {
    margin-left: 5px;
  }

  .badge {
    border: 2px solid #061121 !important;
  }
</style>

<!-- Main Content -->
<div class="main-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="d-flex align-items-center justify-content-between">
          <h5>Current Balance</h5>
          <h5 class="fw-bold text-success p-2 border border-info">{{number_format(auth()->user()->balance)}}</h5>
        </div>
      </div>
      @if (auth()->user()->role === 1)
      @can('view_orders')
      <!--Orders-->
      <div class="col-md-6 col-lg-4 col-sm-12 column">
        <div class="card gr-1">
          <div class="txt">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h4 class="text-success"><i class=" me-3 fa-solid fa-cart-shopping"></i>ORDERS</h4>
                <span class="text-success ms-1">From : {{$formattedResetDate}}</span>
              </div>
              <span class="fs-6 float-end badge text-info">{{$data['overallOrdersCount']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain fw-bold"> <i class="fa-solid fa-bell "></i> Pending Orders</p>
              <a href="{{ route('allorders') }}?status=PENDING&fromDate={{ $formattedResetDate }}&toDate={{ date('Y-m-d') }}"><span class="fs-6 float-end badge text-success">
                  {{$data['pendingOrders']}}
                </span>
              </a>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-check "></i> Dispatched/Team Reviewd Orders </p>
              <a href="{{ route('allorders') }}?status=Team Review your Order&fromDate={{ $formattedResetDate }}&toDate={{ date('Y-m-d') }}">
                <span class="fs-6 float-end badge text-dark">
                  {{$data['dispatchedOrders']}}
                </span>
              </a>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-ban"></i> Cancel Orders </p>
              <a href="{{ route('allorders') }}?status=CANCEL&fromDate={{ $formattedResetDate }}&toDate={{ date('Y-m-d') }}">
                <span class="fs-6 float-end badge text-dark">
                  {{$data['cancelOrders']}}
                </span>
              </a>
            </div>
            <br>

            {{-- for managers set order amount section --}}
            @can('view_order_amount_details')
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-arrow-up-wide-short"></i> Earn Profit </p><span class="fs-6 float-end badge text-success">{{$data['dispatchedProfit']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-sign-hanging"></i> Pending Profit </p><span class="fs-6 float-end badge text-dark">{{$data['pendingProfit']}}</span>
            </div><br>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <a href="{{route('allorders')}}" class="btn btn_view">Visit<span class="viedeticon ms-1"><i class="fa-solid fa-arrow-right"></i></span></a>
            <form method="post" action="{{ route('updateResetOrdersDate') }}">
              @csrf
              <button type="submit"><i class="fa-solid fa-arrow-rotate-left"></i></button>
            </form>
            @endcan
          </div>
        </div>
      </div>
      @endcan
      @can('view_categories')
      <!--CATEGORIES-->
      <div class="col-md-6 col-lg-4 col-sm-12 column">
        <div class="card gr-1">
          <div class="txt">
            <div>
              <h4 class="d-inline text-warning"><i class="fs-4 fa-solid fa-bag-shopping me-2"></i>CATEGORIES</h4><span class="fs-6 float-end badge text-info">{{$data['allCategories']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain fw-bold"> <i class="fa-solid fa-check"></i> Active</p><span class="fs-6 float-end badge text-success">{{$data['activeCategories']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-ban"></i> Disabled </p><span class="fs-6 float-end badge text-dark">{{$data['InactiveCategories']}}</span>
            </div><br>
          </div>
          <a href="{{route('category.index')}}" class="btn btn_view">View All CATEGORIES <span class="viedeticon"><i class="fa-solid fa-arrow-right"></i></span></a>
        </div>
      </div>
      @endcan

      @can('view_products')
      <!--Products-->
      <div class="col-md-6 col-lg-4 col-sm-12 column">
        <div class="card gr-1">
          <div class="txt">
            <div>
              <h4 class="d-inline  text-info"><i class="fs-4 fa-solid fa-bag-shopping me-2"></i>PRODUCTS</h4><span class="fs-6 float-end badge text-info">{{$data['allProducts']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain fw-bold"> <i class="fa-solid fa-check"></i> Active</p><span class="fs-6 float-end badge text-success">{{$data['activeProducts']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-ban"></i> In-Active </p><span class="fs-6 float-end badge text-dark">{{$data['InactiveProducts']}}</span>
            </div><br>
          </div>
          <a href="{{route('all')}}" class="btn btn_view">View All Products <span class="viedeticon"><i class="fa-solid fa-arrow-right"></i></span></a>
        </div>
      </div>
      @endcan

      @else

      <!--Orders-->
      <div class="col-md-6 col-lg-4 col-sm-12 column">
        <div class="card gr-1">
          <div class="txt">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h4 class="text-success"><i class=" me-3 fa-solid fa-cart-shopping"></i>ORDERS</h4>
              </div>
              <span class="fs-6 float-end badge text-info">{{$data['overallOrdersCount']}}</span>
            </div><br>
            <div>
              <p class="d-inline dotmain fw-bold"> <i class="fa-solid fa-bell "></i> Pending Orders</p>
              <a href="{{ route('waoseller.order.index') }}?status=PENDING&fromDate={{ $formattedResetDate }}&toDate={{ date('Y-m-d') }}"><span class="fs-6 float-end badge text-success">
                  {{$data['pendingOrders']}}
                </span>
              </a>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-check "></i> Dispatched/Team Reviewd Orders </p>
              <a href="{{ route('waoseller.order.index') }}?status=Team Review your Order&fromDate={{ $formattedResetDate }}&toDate={{ date('Y-m-d') }}">
                <span class="fs-6 float-end badge text-dark">
                  {{$data['dispatchedOrders']}}
                </span>
              </a>
            </div><br>
            <div>
              <p class="d-inline dotmain  fw-bold"><i class="fa-solid fa-ban"></i> Cancel Orders </p>
              <a href="{{ route('waoseller.order.index') }}?status=CANCEL&fromDate={{ $formattedResetDate }}&toDate={{ date('Y-m-d') }}">
                <span class="fs-6 float-end badge text-dark">
                  {{$data['cancelOrders']}}
                </span>
              </a>
            </div>
            <br>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <a href="{{route('waoseller.order.index')}}" class="btn btn_view">Visit<span class="viedeticon ms-1"><i class="fa-solid fa-arrow-right"></i></span></a>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
<script>
  // $(document).ready(function() {
  //   @if(auth()->user()->id == 1)
  //     Swal.fire({
  //       title: 'Password Expiry ',
  //       text: 'Welcome Admin! Your Password Expiry Date is Coming Soon',
  //       icon: 'warning',
  //     });
  //   @endif
  // });
</script>
@endsection