@extends('admin.layouts.app')
@section('content')
@section('title') Add New Item @endsection


<div class="main-content">
<div class="container">
    <div class="row">
        <div class="col-md-12 mx-auto">
            <section class="section">
                <div class="section-body">
                    <div class="card">
                        <form method="POST" enctype="multipart/form-data" action="{{route('saveLuxury')}}" autocomplete="off">
                            @csrf
                            <div class="card-header text-center d-flex justify-content-between">
                                <h4 class="fw-bold">Add New Item</h4>
                                <a class="btn btn-primary btn-sm" href="{{route('viewLuxury')}}">View All Items</a>
                            </div>
                            <div class="card-body">
                                <!-- Product Price and detail -->
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Name <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="text" multiple class="form-control" name="name" required
                                                placeholder="Enter Product Name" value="{{old('name')}}">
                                            </div>
                                        <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                    </div>
                                        <!--Article-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Add Article <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="text" required class="form-control" name="article" 
                                                value="{{old('article')}}" placeholder="Enter Article name">
                                            </div>
                                            <span class="text-danger">@error('article'){{$message}}@enderror</span>
                                    </div>
                                    <!--Price-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Price <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="number" class="form-control" name="price" 
                                                required value="{{old('price')}}" placeholder="Enter Product Price">
                                        </div>
                                    <span class="text-danger">@error('price'){{$message}}@enderror</span>
                                    </div>
                                </div>

                                <!-- Video details -->
                                <div class="row">
                                    <!-- Video -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Upload Video <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="file" class="form-control" name="video" value="{{old('video')}}" accept="video/*">
                                        </div>
                                        <span class="text-danger">@error('video'){{$message}}@enderror</span>
                                    </div>
                                    <!--Thumbail-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Video Thumbnail <span class="fw-bold text-danger fs-5">*</span></label>
                                                <input  type="file" required class="form-control" id="image" name="thumbnail" accept="thumbnail/*">
                                        </div>
                                        <span class="text-danger">@error('thumbnail'){{$message}}@enderror</span>
                                    </div>
                                    
                                    <!--Product category-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select Category <span class="fw-bold text-danger fs-5">*</span></label>
                                            <select class="form-control" name="category_id" required>
                                                <option  value="">Select Category</option>
                                                @foreach($categories as $cat)
                                                    <option  value="{{$cat->id}}">{{$cat->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="text-danger">@error('category_id'){{$message}}@enderror</span>
                                    </div>
                                    <!--End Row-->
                                </div>

                                <!--Real price sale purchase-->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Real Purchase <span class="fw-bold text-danger fs-5">*</span></label>
                                                <input type="number" class="form-control" name="purchase" required
                                                    placeholder="Enter Real Purchase Price" value="{{old('purchase')}}">
                                            </div>
                                            <span class="text-danger">@error('purchase'){{$message}}@enderror</span>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Net Profit <span class="fw-bold text-danger fs-5">*</span></label>
                                                <input type="number" class="form-control" name="profit" required
                                                    placeholder="Enter Product profit" value="{{old('profit')}}">
                                            </div>
                                            <span class="text-danger">@error('profit'){{$message}}@enderror</span>
                                        </div>
                                            <!--discount-->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Discount </label>
                                                <input type="number" class="form-control" name="discount" 
                                                    value="{{old('discount')}}" placeholder="0">
                                                </div>
                                            <span class="text-danger">@error('discount'){{$message}}@enderror</span>
                                        </div>
                                    </div>
                                    
                                <!--Solditem----------------------------------->
                                <div class="row align-items-center">
                                    <!--quantity-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Quantity <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="number" class="form-control" name="soldItem" required
                                                    placeholder="Enter Product Quantity" value="{{old('soldItem')}}">
                                        </div>
                                        <span class="text-danger">@error('soldItem'){{$message}}@enderror</span>
                                    </div>

                                    <!-- per min added fake products -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Increase SoldItem Per Minute <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="number" required class="form-control" name="increase_perMin" 
                                                    placeholder="0 Min" value="{{old('increase_perMin')}}">
                                        </div>
                                        <span class="text-danger">@error('increase_perMin'){{$message}}@enderror</span>
                                    </div>

                                     <!-- per min added fake products -->
                                     <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Stop Increasing Fake item fter Quantity <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="number" required class="form-control" name="stop_fake_after_quantity" 
                                                    placeholder="Enter Quantity after Stop Increasing Fake Items" value="{{old('stop_fake_after_quantity')}}">
                                        </div>
                                        <span class="text-danger">@error('stop_fake_after_quantity'){{$message}}@enderror</span>
                                    </div>

                                    <!--exceed_limit-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Exceed Limit Quantity </label>
                                            <input type="number" class="form-control" name="exceed_limit"  
                                                placeholder="Enter Exceed Limit" value="{{old('exceed_limit')}}">
                                        </div>
                                        <span class="text-danger">@error('exceed_limit'){{$message}}@enderror</span>
                                    </div>
                                    <!-- fake sold items by admin added -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Fake SoldItems </label>
                                            <input type="number" class="form-control" name="soldAdm" 
                                                    placeholder="0" value="{{old('soldAdm')}}">
                                        </div>
                                        <span class="text-danger">@error('soldAdm'){{$message}}@enderror</span>
                                    </div>
                                    
                                    <!-- Extra Fields button -->
                                    <div class="col-md-2">
                                        <button class=" btn btn-sm btn-primary view_extra_fields_product">Extra Fields</button>
                                    </div>
                                </div>


                                <!--extra fields-->
                                <div class="row extra_fields_product" style="display:none;">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Reviews </label>
                                            <input type="text" class="form-control" name="reviews"  placeholder="Enter Product Price" value="{{old('reviews')}}">
                                        </div>
                                    <span class="text-danger">@error('reviews'){{$message}}@enderror</span>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Color </label>
                                            <input type="text" class="form-control" name="color"  placeholder="Enter Product Color" value="{{old('color')}}">
                                        </div>
                                    <span class="text-danger">@error('color'){{$message}}@enderror</span>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Variety </label>
                                            <input type="text" class="form-control" name="variety"  placeholder="Enter Product Varirty" value="{{old('variety')}}">
                                        </div>
                                    <span class="text-danger">@error('variety'){{$message}}@enderror</span>
                                    </div>
                                    <!--{{--<div class="col-md-12">
                                        <div class="form-group">
                                            <label>Youtube Embed Code <span class="fw-bold text-danger fs-5">*</span></label>
                                            <textarea class="form-control" name="video_link_embed" placehlder="Youtube embed code" value="{{old('video_link_embed')}}"></textarea>
                                        </div>
                                        <span class="text-danger">@error('video_link_embed'){{$message}}@enderror</span>
                                    </div>--}}-->
                                </div><hr>
                                <!--foooter-->
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                        </form>
                    </div><!--End Card Body-->
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
</div>

@endsection


@section('scripts')
<script>
    $(document).ready( function () {
});
</script>
@endsection