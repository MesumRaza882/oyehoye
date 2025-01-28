@extends('admin.layouts.app')
@section('content')
@section('title') Products @endsection


<div class="main-content">
    <!-- add Modal -->
    @section('modal_header')
    <h5 class="modal-title" id="exampleModalLabel">Add New Product</h5>
    @endsection

    @section('modal_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <form method="POST" enctype="multipart/form-data" id="additem" autocomplete="off">
                    @csrf
                    <input type="text" value="1" name="for_notification" hidden>

                    <!-- Product Price and detail -->
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Name <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="text" multiple class="form-control" name="name" required placeholder="Enter Product Name" value="{{old('name')}}">
                                <!-- <span class="text-danger">@error('name'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                        <!--Article-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Add Article <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="text" required class="form-control" name="article" value="{{old('article')}}" placeholder="Enter Article name">
                                <!-- <span class="text-danger">@error('article'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                        <!--Price-->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Price <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="number" class="form-control" name="price" required value="{{old('price')}}" placeholder="Enter Product Price">
                                <!-- <span class="text-danger">@error('price'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                    </div>

                    <!-- Video details -->
                    <div class="row">
                        <!-- Video -->
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label>Upload Video <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="file" class="form-control video" name="video" id='videoUpload' value="{{old('video')}}" accept="video/*">
                                <!-- <span class="text-danger">@error('video'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                        <div class="col-6 col-lg-3" id="video_container" style="display:none">
                            <video width="100%" height="150px" controls>
                                <source src="" type="video/ogg" />
                                Your browser does not support the video tag.
                            </video>

                        </div>
                        <!--Thumbail-->
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label>Video Thumbnail <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="file" required class="form-control thumbnail" onchange="preview()" name="thumbnail" accept="thumbnail/*">
                                <!-- <span class="text-danger">@error('thumbnail'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 img_preview_container" style="display:none">
                            <img class="img rounded-circle" width="150px" height="150px" src="" alt="Category_Thumbnail" id="frame">
                        </div>

                        <!--Product category-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Category <span class="fw-bold text-danger fs-5">*</span></label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->name}}</option>
                                    @endforeach
                                </select>
                                <!-- <span class="text-danger">@error('category_id'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Real Purchase <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="number" class="form-control" name="purchase" required placeholder="Enter Real Purchase Price" value="{{old('purchase')}}">
                                <!-- <span class="text-danger">@error('purchase'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Net Profit <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="number" class="form-control" name="profit" required placeholder="Enter Product profit" value="{{old('profit')}}">
                                <!-- <span class="text-danger">@error('profit'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                    </div>

                    <!--Solditem----------------------------------->
                    <div class="row align-items-center mb-2">
                        <!--quantity-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="number" class="form-control" name="soldItem" required placeholder="Enter Product Quantity" value="{{old('soldItem')}}">
                                <!-- <span class="text-danger">@error('soldItem'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                        <!-- per min added fake products -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Increase Fake SoldItem Per Minute <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="number" required class="form-control" name="increase_perMin" placeholder="0 Min" value="{{old('increase_perMin')}}">
                                <!-- <span class="text-danger">@error('increase_perMin'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                        <!-- per min added fake products -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stop Increasing Fake item After Quantity <span class="fw-bold text-danger fs-5">*</span></label>
                                <input type="number" required class="form-control" name="stop_fake_after_quantity" placeholder="Enter Quantity after Stop Increasing Fake Items" value="{{old('stop_fake_after_quantity')}}">
                                <!-- <span class="text-danger">@error('stop_fake_after_quantity'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                        <!-- fake sold items by admin added -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fake Solded Items </label>
                                <input type="number" class="form-control" name="soldAdm" placeholder="0" value="{{old('soldAdm')}}">
                                <!-- <span class="text-danger">@error('soldAdm'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                        <!-- Extra Fields button -->
                        <div class="col-md-2">
                            <button class=" btn btn-sm btn-primary view_extra_fields_product">Extra Fields</button>
                        </div>
                    </div>


                    <!--extra fields-->
                    <div class="row  extra_fields_product" style="display:none;">
                        <!--exceed_limit-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Customer Buying Item Limit Quantity </label>
                                <input type="number" class="form-control" name="exceed_limit" placeholder="Enter Exceed Limit" value="{{old('exceed_limit')}}">
                                <!-- <span class="text-danger">@error('exceed_limit'){{$message}}@enderror</span> -->
                            </div>
                        </div>

                        <!--discount-->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Discount </label>
                                <input type="number" class="form-control" name="discount" value="{{old('discount')}}" placeholder="0">
                                <!-- <span class="text-danger">@error('discount'){{$message}}@enderror</span> -->
                            </div>
                        </div>
                        <!--is_dc_free-->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Is Dc Free</label>
                                <select name="is_dc_free" id="is_dc_free" class="form-control">
                                    <option value="0" Selected>No</option>
                                    <option value="1">DC Free</option>
                                </select>
                            </div>
                        </div>

                        <!--is_hide_to_new_arrival-->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="hide_to_new_arrival">Hide to New-Arrival</label>
                                <select name="hide_to_new_arrival" id="hide_to_new_arrival" class="form-control">
                                    <option value="0" Selected>No</option>
                                    <option value="1">Hide</option>
                                </select>
                            </div>
                        </div>
                        <!--is_added_to_lock_folder-->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="is_locked">Add to Lock Folder</label>
                                <select name="is_locked" id="is_locked" class="form-control">
                                    <option value="0" Selected>No</option>
                                    <option value="1">Added</option>
                                </select>
                            </div>
                        </div>

                        <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Reviews </label>
                                            <input type="text" class="form-control" name="reviews"  placeholder="Enter Product Price" value="{{old('reviews')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Color </label>
                                            <input type="text" class="form-control" name="color"  placeholder="Enter Product Color" value="{{old('color')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Variety </label>
                                            <input type="text" class="form-control" name="variety"  placeholder="Enter Product Varirty" value="{{old('variety')}}">
                                        </div>
                                    </div> -->
                        <!-- <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Youtube Embed Code <span class="fw-bold text-danger fs-5">*</span></label>
                                            <textarea class="form-control" name="video_link_embed" placehlder="Youtube embed code" value="{{old('video_link_embed')}}"></textarea>
                                             <span class="text-danger">@error('video_link_embed'){{$message}}@enderror</span> -->
                    </div>
            </div>
        </div>
        <button class="btn btn-primary d-block m-auto save_item_btn" type="submit">Save</button>
        </form>
    </div>
</div>
</div>
@endsection
<!-- end Adding Modal -->


<div class="container">
    <div class="row">
        <!-- Top Row -->
        <div class="row justify-content-between">

            <!-- total record count -->
            <div class="col-auto d-flex align-items-start">
                <p class="d-block p-1 px-2 bg-primary text-white">Products:<span class="ms-1">{{$total_records}}</span></p>
                <button class="ms-2 btn  btn-danger d-none deleteAllbtnItems" id="deleteAllbtn"></button>
                <button class="ms-2 btn  btn-secondary d-none pinItems" id="pinItems"></button>
            </div>

            <!-- add new product -->
            <div class="col-auto">
                <a class="btn btn-primary  text-white" data-bs-toggle="modal" data-bs-target="#Add">
                    <i class="fa fa-plus me-1"></i>Product</a>
            </div>

            <!-- filter -->
            <div class="col-12 mb-3">
                <form method="GET" action="{{route('all')}}">@csrf

                    <div class="row align-items-center">
                        <!-- select records -->
                        <div class="col-lg-1 mb-2">
                            <label class="pe-1">Records</label>
                            <select name="records" class="form-control me-2" required>
                                <option value="15" @if(request()->get('records') == 15) selected @endif>15</option>
                                <option value="50" @if(request()->get('records') == 50) selected @endif>50</option>
                                <option value="100" @if(request()->get('records') == 100) selected @endif>100</option>
                                <option value="200" @if(request()->get('records') == 200) selected @endif>200</option>
                                <option value="300" @if(request()->get('records') == 300) selected @endif>300</option>
                                <option value="500" @if(request()->get('records') == 500) selected @endif>500</option>
                            </select>
                        </div>
                        <!-- select Category -->
                        <div class="col-lg-4 mb-2">
                            <label class="pe-1">Select Category</label>
                            <select name="category" class="form-control me-2">
                                <option value="">All Categories</option>
                                @php
                                    $filterCategories = \App\Models\category::has('product')->get();
                                @endphp
                                @foreach($filterCategories as $cat)
                                <option value="{{$cat->name}}" @if(request()->get('category') == $cat->name) selected @endif>{{$cat->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- select Status -->
                        <div class="col-lg-2 mb-2">
                            <label class="pe-1">Select Status</label>
                            <select name="status" class="form-control me-2">
                                <option value="1" @if(request()->get('status') == '1') selected @endif>Ative Stock</option>
                                <option value="2" @if(request()->get('status') == '2') selected @endif>Sold Out</option>
                                <option value="3" @if(request()->get('status') == '3') selected @endif>All</option>
                            </select>
                        </div>

                        <!--  -->
                        <div class="col-lg-3 mb-2">
                            <label class="pe-1">Select Locked+Unhiden</label>
                            <select name="status_app" class="form-control me-2">
                                <option value="1" @if(request()->get('status_app') == '1') selected @endif>All</option>
                                <option value="2" @if(request()->get('status_app') == '2') selected @endif>Locked Folder Products</option>
                                <option value="3" @if(request()->get('status_app') == '3') selected @endif>Hidden New Arrival products</option>
                            </select>
                        </div>

                        <!-- filter by name and price -->
                        <div class="col-lg-3 mb-2">
                            <label class="pe-1">Search</label><input type="search" value="{{request()->get('search_input')}}" class="me-2 form-control" name="search_input" placeholder="Name & Price">
                        </div>

                        <!-- filter by article  -->
                        <div class="col-lg-2 mb-2">
                            <label class="pe-1">Article</label><input type="search" value="{{request()->get('article')}}" class="me-2 form-control" name="article" placeholder="Search By Article">
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit">Filter Products</button>
                        </div>

                    </div>


                </form>
            </div>
        </div>
        <!-- end Top row -->

        <div class="col-md-12 mx-auto">

            @if(count($items)>0)
            <table class="table table-hover table-striped table-responsive active_table">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="main_checkbox" style="background-color: aquamarine"></th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price for Customer</th>
                        <th>Quantity</th>
                        <th>Purchase price</th>
                        <th>Real Profit</th>
                        <th>Thumbnail</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $viewLuxury)
                    <tr class="{{$viewLuxury->is_active_row == 1 ? 'highlight': ''}}">
                        <td><input type="checkbox" value="{{$viewLuxury->id}}" name="cat_checkbox"></td>
                        <td>{{$viewLuxury->name}}</td>
                        <td>{{$viewLuxury->itemcategory->name}}</td>
                        <td>Rs {{$viewLuxury->price}}</td>
                        <td>{{( $viewLuxury->soldItem <= 0 ) ? "SoldOut" : (( $viewLuxury->soldItem < 5 ) ? "Restoke Inventoey" : '' ) }}
                            <span class=" ms-1fw-bold p-2 badge {{( $viewLuxury->soldItem < 1 ) ? 'bg-success': 'bg-primary' }}
                                        ">{{$viewLuxury->soldItem}}</span>
                        </td>
                        <td>Rs: {{$viewLuxury->purchase}}</td>
                        <td>Rs: {{$viewLuxury->profit}}</td>
                        <td>
                            <a href="{{$viewLuxury->thumbnail}}" target="_blank">
                                <img src="{{$viewLuxury->thumbnail}}" alt="thumbnail" width="80px" height="80px" class="img">
                            </a>
                        </td>
                        <td>
                            <a href="{{route('edit',$viewLuxury->id)}}" class="mb-2 text-white btn btn-sm  btn-info">Edit</a>
                            <button onclick="soldoutStatus('{{$viewLuxury->id}}')" class=" {{ $viewLuxury->soldstatus == 0 ? 'btn-warning' : '' }} btn btn-success btn-sm soldoutbtn{{$viewLuxury->id}}">
                                {{ $viewLuxury->soldstatus == 0 ? "Add Soldout" : "Remove Soldout"  }}</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $items->appends(request()->all())->links() !!}

            @else
            <div class="alert alert-warning">No Products has been added yet!</div>
            @endif
        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')
<script>
    //   soldout status
    function soldoutStatus($id) {
        var id = $id;
        var $btn = $('.soldoutbtn' + id);
        $.ajax({
            type: "POST",
            url: "{{route('mark_as_sold')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id': id,
            },
            success: function(response) {

                if (response.check_num == 100) {
                    $btn.html('Remove Soldout');
                    $btn.addClass('btn-success');
                    $btn.removeClass('btn-warning');
                    // window.location.reload();
                }
                if (response.check_num == 200) {
                    $btn.html('Add Soldout');
                    $btn.removeClass('btn-success');
                    $btn.addClass('btn-warning');
                }

            },
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // add item
        $(document).on('submit', '#additem', function(e) {
            e.preventDefault();

            $('.save_item_btn').prop('disabled', true);
            $('.save_item_btn').text('Saving...');


            let formdata = new FormData($('#additem')[0]);
            $.ajax({
                type: "POST",
                url: "{{route('save')}}",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $('.save_item_btn').prop('disabled', false);
                    $('.save_item_btn').text('Save');

                    if (response.status == 0) {
                        toastr.error(response.data);
                    } else if (response.status == 2) {
                        toastr.success(response.data);
                        reset();
                    }
                },
            });
        });

        // delete slected items
        $(document).on('click', '.deleteAllbtnItems', function(e) {
            e.preventDefault();
            var x = confirm("Are you sure you want to delete Items?");
            if (x) {
                var allids = [];

                $('input[name="cat_checkbox"]:checked').each(function() {
                    allids.push($(this).val());
                });

                $.ajax({
                    type: "POST",
                    url: "{{route('delete_checked_products')}}",
                    data: {
                        ids: allids,
                    },

                    success: function(response) {
                        toastr.success('Items Deleted Duccessfully');
                        window.location.reload();
                    },

                });
            }
        });

        // add to pin for top up  slected items
        $(document).on('click', '.pinItems', function(e) {
            e.preventDefault();
            var allids = [];

            $('input[name="cat_checkbox"]:checked').each(function() {
                allids.push($(this).val());
            });
            $.ajax({
                type: "POST",
                url: "{{route('pinned_checked_products')}}",
                data: {
                    ids: allids,
                },

                success: function(response) {
                    // console.log(response)
                    toastr.success('Items Pin to Start Duccessfully');
                    // window.location.href = "{{route('all')}}";
                },
                // });

            });
        });

        $(document).on('click', '.update_modal_close_btn', function(e) {
            reset();
        });

        // reset function
        function reset() {
            // $('.upd_image').val('');  
            $('#video_container').hide(1000);
            $('.img_preview_container').hide(1000);
            $('.video').val('');
            $('.thumbnail').val('');
            $('.modal_close').removeClass("d-none");
            $('.add_modal_close').addClass("d-none");
        }
    });
</script>
@endsection