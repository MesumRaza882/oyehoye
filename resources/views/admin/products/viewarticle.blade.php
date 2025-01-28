@extends('admin.layouts.app')
@section('title') Articles @endsection
@section('content')

<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }
</style>
<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                @if(count($articles)>0)
                <!--Filter Article-->
                <div class="row justify-content-between mb-3">
                    <div class="col-md-5 col-sm-12">
                        <form class="form-inline" method="GET" action="{{route('viewarticle')}}">
                            <label class="pe-1"></label>
                            <x-filter.x-record-select label="Select Hours" name="hours" class="me-2" :options="['Select Hours' => 'Select Hours' ,1 => '1', 2 => '2',3 => '3',4 => '4',6 => '6',12 => '12',24 => '24']" :selected="request()->get('hours')"></x-filter.x-record-select>
                            <button class="btn btn-primary mx-2 my-2 my-sm-0 btn-sm" type="submit">Filter Order</button>
                        </form>
                    </div>
                    <div class="col-md-auto">
                        <button id="resetMarkeetPickup" class=" ms-2 btn btn-sm btn-success">Update MarkeetPickup</button>
                    </div>
                    <!--Filter Order-->
                    <!--<div class="col-md-6 col-sm-12 my-sm-0 my-3">-->
                    <!--     <form class="form-inline" method="GET" action="{{route('viewarticle')}}">@csrf-->
                    <!--            <label class="pe-1">From</label><input type="date" class="me-2 form-control" name="fromDate" required max="<?php echo date("Y-m-d"); ?>">-->
                    <!--             <label class="pe-1">To</label><input type="date" class="form-control" name="toDate" required max="<?php echo date("Y-m-d"); ?>">-->
                    <!--            <button class="btn btn-primary mx-2 my-2 my-sm-0 btn-sm" type="submit" >Filter Order</button>-->
                    <!--    </form>-->
                    <!--</div>-->
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Aricle Number</th>
                                <th>Product Name</th>
                                <th>thumbnail</th>
                                <th>Markeet Pickup</th>
                                <th>Availbale Quantity</th>
                                <!-- <th>Delivered</th> -->
                                <th>Pending</th>
                                <th>Dispatched/Deliverd</th>
                                <th>Hold</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($articles as $product)
                            <tr>
                                <td><span class="fw-bold">{{$product->article}}</span>
                                    <span class="ms-1 badge bg-danger">{{$product->soldItem == 0 ? 'SoldOut' : ''}}</span>
                                </td>
                                <td>{{$product->name}}</td>
                                <td>
                                    <a href="{{$product->thumbnail}}" target="_blank">
                                        <img src="{{$product->thumbnail}}" alt="thumbnail" width="80px" height="80px" class="img">
                                    </a>
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-white text-center bg-dark" style="width: 70px;" type="text" name="markeetPickup[]" value="{{$product->markeetPickup}}" data-product-id="{{$product->id}}" >
                                </td>
                                <td>
                                    <span class=" fs-6 badge bg-primary">{{$product->soldItem}}</span>

                                </td>
                                <!-- <td class="text-center">
                                                <span class="badge bg-info p-2 fs-5">{{$product->productitems->where('order_status','DELIVERED')->sum('qty')}}</span>
                                        </td> -->
                                <td class="text-center">
                                    <span class="badge bg-warning p-2 fs-6">
                                        {{ $product->order_items_pending_sum_qty ? $product->order_items_pending_sum_qty : 0  }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success p-2 fs-6">
                                        {{ $product->order_items_dispatched_delivered_sum_qty ? $product->order_items_dispatched_delivered_sum_qty : 0  }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger p-2 fs-6">
                                        {{ $product->order_items_cancel_hold_sum_qty ? $product->order_items_cancel_hold_sum_qty : 0  }}
                                    </span>
                                </td>
                                <td>

                                    <a href="{{route('edit',$product->id)}}" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $articles->appends(request()->all())->links() !!}
                @else
                <div class="alert alert-warning">No Articles has been added yet!</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Use the submit button id to trigger the Ajax call
        $('#resetMarkeetPickup').on('click', function() {

            // Gather the data
            var formData = {
                _token: $('input[name="_token"]').val(),
                products: [],
                action:'resetPickup',
            };

            $('input[name="markeetPickup[]"]').each(function() {
                var productId = $(this).data('product-id');
                var markeetPickup = $(this).val();

                formData.products.push({
                    id: productId,
                    markeetPickup: markeetPickup,
                });
            });

            // Submit data using Ajax
            $.ajax({
                url: "{{ route('updateQuantities') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    console.log(response);
                    if (response.status === 2)
                        toastr.success(response.message);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
    });
</script>

@endsection