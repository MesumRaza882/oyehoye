@extends('admin.layouts.app')
@section('content')
@section('title') Inventories @endsection

<div class="main-content">

    <!-- add Modal -->
    @section('modal_header')
    <h5 class="modal-title" id="exampleModalLabel">Add New Inventory</h5>
    @endsection

    @section('modal_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <section class="section">
                    <div class="section-body">
                        <form method="POST" data-add-route="{{route('inventory.store')}}" enctype="multipart/form-data" id="addNewRecordForm" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <x-x-input label="Inventory" type="number" min="1" name="inventory" required placeholder="Enter Inventory" />
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
    @endsection
    <!-- end Adding Modal -->


    <!-- Minus inventory Modal -->
    <div class="modal fade" id="MinusInventory" tabindex="-1" aria-labelledby="AddLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close add_modal_close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <button type="button" class="btn-close modal_close d-none" onclick='window.location.reload()' data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mx-auto">
                                <section class="section">
                                    <div class="section-body">
                                        <form method="POST" data-add-route="{{route('inventory.minusInventory')}}" id="minusInventoryForm" autocomplete="off">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <x-x-input label="Minus Inventory" type="number" min="1" name="minus_inventory" required placeholder="Enter Minus Inventory" />
                                                </div>
                                                <div>
                                                    <button class="btn btn-warning" type="submit">Minus Inventory</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal  -->

    <!-- delete record modal -->
    @section('delete_modal_footer')
    <x-x-delete-record deleteRoute="{{ route('inventory.delete') }}" />
    @endsection

    <div class="container">
        <!-- Top Row -->
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-between">
                    <!-- <div class="col-auto d-flex align-items-start">
                        <p class="p-sm-2 p-1 fw-bold  border border-info">
                            <span class="badge bg-dark p-sm-2 p-1"><i class="fa fa-user"></i> {{$inventoryUsers ? $inventoryUsers : 0 }}</span>
                            <a href="{{route('inventory.seller.index')}}" class="btn btn-sm">Add New Seller</a>
                        </p>
                    </div> -->

                    <div class="col-auto">
                        @if($inventoryRecord)
                        <button type="button" class="fw-bold btn btn-warning" data-bs-toggle="modal" data-bs-target="#MinusInventory">
                            <i class="fs-5 align-middle fa-solid fa-person-walking-arrow-loop-left"></i>
                        </button>
                        @endif
                        <button type="button" class="ms-2 fw-bold btn btn-primary" data-bs-toggle="modal" data-bs-target="#Add">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <!-- end Top row -->

        <div class="row align-items-center">
            <!-- select records -->
            {{--<div class="col-auto">
                <p class="p-sm-2 p-1 fw-bold  border border-info">
                    Total-Inventories:
                    <span class="ms-1 badge bg-primary p-sm-2 p-1"></span>
                </p>
            </div>
            <div class="col-auto">
                <p class="p-sm-2 p-1 fw-bold  border border-info">
                    Sale-Inventories:
                    <span class="ms-1 badge bg-success p-sm-2 p-1">{{$inventoryRecord ? $inventoryRecord->sale_inventory : 0 }}</span>
            </p>
        </div>--}}
    </div>

    <!-- Record Display -->
    <div class="row my-3">
        <div class="col-12">
            @if($inventoryRecord)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Inventory</th>
                            <th>Picked</th>
                            <th>Returned</th>
                            <th>Stock-inhand</th>
                            <th>Inventory-Date</th>
                            <th>Reset</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="fw-bold">{{$inventoryRecord->total_inventory}}</span></td>
                            <td><span class="text-success fw-bold">{{$inventoryRecord->sale_inventory}}</span></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <a href="{{ route('allorders') }}?is_returned_order=1?records=50}}" class="mx-1 btn btn-sm btn-warning mb-2  fw-bold {{$returAppOrders > 0 ?: 'd-none'}}">
                                        App-Orders : {{$returAppOrders}}
                                    </a>
                                    <a href="{{ route('waoseller.order.index') }}?is_returned_order=1?records=50}}" class="mx-1 btn btn-sm btn-warning mb-2  fw-bold {{$returSellerOrders > 0 ?: 'd-none'}}">
                                        Seller-Orders : {{$returSellerOrders}}
                                    </a>
                                </div>

                                <span class="{{($returSellerOrders || $returAppOrders) ?: 'text-center'}} badge bg-success d-block fw-bold">
                                    Confirmed : {{$inventoryRecord->return_inventory}}
                                </span>

                            </td>
                            <td>
                                <span class="d-block text-info fw-bold">Total : {{$inventoryRecord->total_inventory - ($inventoryRecord->sale_inventory + $inventoryRecord->minus_inventory)}}</span>
                                <span>Inhand : {{$inventoryRecord->total_inventory - $inventoryRecord->sale_inventory }} - Minus : {{$inventoryRecord->minus_inventory}} </span>
                            </td>
                            <td>{{$inventoryRecord->updated_at->format('Y-m-d')}}</td>
                            <td class="text-center d-flex align-items-center ">
                                <button value="{{$inventoryRecord->id}}" class="deleletRecordIconButton btn btn-danger btn-sm"><i class="fa-solid fa-delete-left"></i></button>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-warning text-white">No Inventory record</div>
            @endif
        </div>
    </div>
    <!-- End Display Records -->
</div>
</div>
@endsection


@section('scripts')


<script>
    $(document).ready(function() {
        // add Record
        $(document).on('submit', '#minusInventoryForm', function(e) {
            e.preventDefault();

            let formdata = new FormData($('#minusInventoryForm')[0]);
            let addRoute = $(this).data('add-route');
            $.ajax({
                type: "POST",
                url: addRoute,
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status == 0) {
                        toastr.error(response.message);
                    } else if (response.status == 2) {
                        toastr.success(response.message);
                        window.location.reload();
                    }
                },
            });
        });
    });
</script>
@endsection