@extends('admin.layouts.app')
@section('content')
@section('title') Single Product @endsection

<div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      
      @include('admin.layouts.navbar')

      @include('admin.layouts.sidebar')
      <!-- Main Content -->
      <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <!-- <nav class="mb-4  nav-pills nav-fill justify-content-center">
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active " id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                            Product Edit
                        </button>
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Product Customer Deatils</button>
                        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Product Customer Review</button>
                      </div>
                    </nav> -->
                    <div class="tab-content" id="nav-tabContent">
                        <!--product edit-->
                      <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <section class="section">
                        <div class="section-body">
                            <div class="card">
                                <form method="POST" enctype="multipart/form-data" action="{{route('update',$editLux->id)}}">
                                    @csrf
                                    <div class="card-header d-flex justify-conten   t-between">
                                    <h4>Edit Product</h4>
                                    <a href="javascript:history.back()" class="btn text-white btn-primary mb-2">Back</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Name <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="text" class="form-control" name="name" value="{{$editLux->name}}" required="">
                                                     <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--article-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Article <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="text" class="form-control" name="article" value="{{$editLux->article}}" required="">
                                                     <span class="text-danger">@error('article'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--real category-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Select Real Category <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <select class="form-control" name="category_id" required>
                                                        <option selected  value="{{$editLux->category_id}}">{{$editLux->itemcategory->name}}</option>
                                                        @foreach($categories as $cat)
                                                            <option  value="{{$cat->id}}">{{$cat->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            <span class="text-danger">@error('category_id'){{$message}}@enderror</span>
                                        </div>
                                       
                                        <!--Real profit Price -->
                                        <div class="row">
                                            <!--price-->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Price <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" class="form-control" name="price" value="{{$editLux->price}}" required="">
                                                     <span class="text-danger">@error('price'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--purchase-->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Purchase Price <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" class="form-control" name="purchase" value="{{$editLux->purchase}}" required="">
                                                     <span class="text-danger">@error('purchase'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--profit-->
                                             <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Profit <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" class="form-control" name="profit" value="{{$editLux->profit}}" required="">
                                                     <span class="text-danger">@error('profit'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--quantity-->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Quantity <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" class="form-control" name="soldItem" value="{{$editLux->soldItem}}" required="">
                                                    <span class="text-danger">@error('soldItem'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            
                                        </div>


                                        <!-- quantity and solded work -->
                                        <div class="row">
                                            <!--increase per min-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Increase SoldItem Per Minute <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" class="form-control" name="increase_perMin" required="" placeholder="Enter Minute" value="{{$editLux->increase_perMin}}">
                                                </div>
                                            <span class="text-danger">@error('increase_perMin'){{$message}}@enderror</span>
                                            </div>
                                             <!-- stop increasing fake item -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Stop Increasing Fake item After Quantity <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" required class="form-control" name="stop_fake_after_quantity" 
                                                        placeholder="Enter Quantity after Stop Increasing Fake Items" value="{{$editLux->stop_fake_after_quantity}}">
                                                        <span class="text-danger">@error('stop_fake_after_quantity'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <!--soldquantity-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Fake Solded Items</label>
                                                    <input type="number" class="form-control" name="soldAdm"  placeholder="Enter Sold Items " value="{{$editLux->soldAdm}}">
                                                </div>
                                                <span class="text-danger">@error('soldAdm'){{$message}}@enderror</span>
                                            </div>
                                        </div>

                                        <!--video/thumbnail Upload-->
                                        <div class="row">
                                             <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Upload Video</label>
                                                    <input type="file" id='videoUpload' class="form-control" name="video" accept="video/*">
                                                     <span class="text-danger">@error('video'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                              <!--video Display-->
                                            <div class="col-md-3">
                                                <video width="100%" height="150px" controls >
                                                    <source src="{{$editLux->video}}" type="video/{{ pathinfo($editLux->video, PATHINFO_EXTENSION)}}">
                                                    <source src="{{pathinfo($editLux->video, PATHINFO_FILENAME)}}.ogg" type="video/ogg" />
                                                    Your browser does not support the video tag.
                                                </video>
                                                
                                            </div>
                                             <!--Thumbail-->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Video Thumbnail</label>
                                                    <input  type="file"  class="form-control" onchange="preview()"
                                                         name="thumbnail" accept="image/*" >
                                                </div>
                                            <span class="text-danger">@error('image'){{$message}}@enderror</span>
                                            </div>
                                            <!--Thumbnail Image-->
                                            @if($editLux->thumbnail)
                                                <div class="col-md-3">
                                                     <img src="{{$editLux->thumbnail}}" width="150px" height="150px"
                                                      id="frame" alt="Video Thumbnail" class="img" />
                                                </div>
                                            @endif
                                            </div>
                                            <!--end-->
                                        </div>

                                        <!-- ExtraField Button Row -->
                                        <div class="row mb-3 justify-content-end align-items-end">
                                            <div class="col-auto">
                                                <button class=" btn btn-sm btn-primary view_extra_fields_product">Extra Fields</button>
                                            </div>
                                        </div>

                                       <!--extra fields-->
                                        <div class="row extra_fields_product" style="display:none;">
                                            <!--exceed_limit-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Customer Buying Item Limit Quantity </label>
                                                    <input type="number" class="form-control" value="{{$editLux->exceed_limit}}" name="exceed_limit"  placeholder="Update Exceed Limit" value="{{old('exceed_limit')}}">
                                                </div>
                                                <span class="text-danger">@error('exceed_limit'){{$message}}@enderror</span>
                                            </div>
                                          
                                            <!-- discount -->
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Discount</label>
                                                    <input type="number" class="form-control" name="discount"  
                                                    placeholder="Enter discount " value="{{$editLux->discount}}">
                                                </div>
                                                <span class="text-danger">@error('discount'){{$message}}@enderror</span>
                                            </div>
                                             <!--is_dc_free-->
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                <label>Is Dc Free</label>
                                                <select name="is_dc_free" id="is_dc_free" class="form-control">
                                                    <option value="0" @if($editLux->is_dc_free == 0) selected @endif>No</option>
                                                    <option value="1" @if($editLux->is_dc_free == 1) selected @endif>DC Free</option>
                                                </select>
                                                </div>
                                            </div>
                                             <!--is_hide_to_new_arrival-->
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                <label for="hide_to_new_arrival">Hide to New-Arrival</label>
                                                <select name="hide_to_new_arrival" id="hide_to_new_arrival" class="form-control">
                                                    <option value="0" @if($editLux->hide_to_new_arrival == 0) selected @endif >No</option>
                                                    <option value="1" @if($editLux->hide_to_new_arrival == 1) selected @endif >Hide</option>
                                                </select>
                                                </div>
                                            </div>
                                            <!-- is_locked -->
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="is_locked">Add to Lock Folder</label>
                                                    <select name="is_locked" id="is_locked" class="form-control">
                                                        <option value="0" @if($editLux->is_locked == 0) selected @endif >No</option>
                                                        <option value="1" @if($editLux->is_locked == 1) selected @endif >Added</option>
                                                    </select>
                                                </div>
                                            </div>

                                           <!--review-->
                                            <!-- <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Reviews </label>
                                                    <input type="text" class="form-control" name="reviews" value="{{$editLux->reviews}}" >
                                                     <span class="text-danger">@error('reviews'){{$message}}@enderror</span>
                                                </div>
                                            </div> -->
                                            <!--variety-->
                                             <!-- <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Variety </label>
                                                    <input type="text" class="form-control" name="variety" value="{{$editLux->variety}}" >
                                                     <span class="text-danger">@error('variety'){{$message}}@enderror</span>
                                                </div>
                                            </div> -->
                                            <!--color-->
                                             <!-- <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Color </label>
                                                    <input type="text" class="form-control" name="color" value="{{$editLux->color}}" >
                                                     <span class="text-danger">@error('color'){{$message}}@enderror</span>
                                                </div>
                                            </div> -->
                                            <!--{{--<div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Youtube Embed Code </label>
                                                    <textarea class="form-control" name="video_link_embed" placehlder="Youtube embed code" value="">{{old('video_link_embed',$editLux->video_link_embed)}}</textarea>
                                                </div>
                                                <span class="text-danger">@error('video_link_embed'){{$message}}@enderror</span>
                                            </div>--}}-->
                                        </div>
                                        <!--end extra-->
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                      </div>
                      
                      <!--product Customer Detail-->
                      <!-- <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <section> 
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary add_row">+</button>
                        </div>
                        <form action="{{route('product_custmer')}}" method="POST">@csrf
                            <input type="hidden" value="{{$editLux->id}}" name="prod_id">
                            <div class="main_div">
                                <div class="row d-flex">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Name <span class="fw-bold text-danger fs-5">*</span></label>
                                            <input type="text" required class="form-control" name="name[]"  placeholder="Enter Customer Name" value="{{old('name')}}">
                                        </div>
                                    <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Address <span class="fw-bold text-danger fs-5">*</span></label>
                                            <textarea  required class="form-control" name="address[]" placehlder="Enter Customer Address etc" value="{{old('address')}}"></textarea>
                                        </div>
                                        <span class="text-danger">@error('address'){{$message}}@enderror</span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary my-2 btn-lg">Save</button>
                        </form>
                        </section><hr> -->
                        
                        <!--Display all customers-->
                        <!-- <section>
                            <div class="row my-2">
                                <h6>All Customer Deatils <button class="btn btn-primary btn-sm ms-1 cus_count">{{count($editLux->SingproductRev)}}</button></h6>
                                <div class="col-sm-12">
                                    <table class="table-bordered table table-sm-responsive table-hover">
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Customer Address</th>
                                            <th>Action</th>
                                        </tr>
                                        @foreach($editLux->SingproductRev as $customer)
                                        <tr class="customer_row{{$customer->id}}">
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->address}}</td>
                                            <td><button class="btn btn-danger btn-sm " onclick="delCustomer('{{$customer->id}}')" value="{{$customer->id}}"><i class="fa-solid fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </section>
                      </div> -->
                      
                      <!--Product Customer Reviews--->
                      <!-- <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab"> -->
                            <!-- <section> 
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary add_row_review">+</button>
                                </div>
                                <form action="{{route('product_custmer_review')}}" method="POST">@csrf
                                    <input type="hidden" value="{{$editLux->id}}" name="prod_id">
                                    <div class="main_div_review">
                                        <div class="row d-flex">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Customer Name <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="text" required class="form-control" name="name[]"  placeholder="Enter Customer Name" value="{{old('name')}}">
                                                </div>
                                            <span class="text-danger">@error('name'){{$message}}@enderror</span>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Review <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <input type="number" required class="form-control" name="review[]"  placeholder="Enter Customer Review" value="{{old('review')}}">
                                                </div>
                                            <span class="text-danger">@error('review'){{$message}}@enderror</span>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Customer Review Description <span class="fw-bold text-danger fs-5">*</span></label>
                                                    <textarea  required class="form-control" name="description[]" placehlder="Enter Customer Address etc" value="{{old('description')}}"></textarea>
                                                </div>
                                            <span class="text-danger">@error('description'){{$message}}@enderror</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary my-2 btn-lg">Save</button>
                                </form>
                            </section><hr> -->
                            
                             <!--Display all customers-->
                            <!-- <section>
                                <div class="row my-2">
                                    <h6>All Customer Reviews <button class="btn btn-primary btn-sm ms-1 cus_count_rev">{{count($editLux->prodReviews)}}</button></h6>
                                    <div class="col-sm-12">
                                        <table class="table-bordered table table-sm-responsive table-hover">
                                            <tr>
                                                <th>Customer Name</th>
                                                <th>Customer Review</th>
                                                <th>Review Description</th>
                                                <th>Action</th>
                                            </tr>
                                            @foreach($editLux->prodReviews as $customer)
                                            <tr class="customer_row_rev{{$customer->id}}">
                                                <td>{{$customer->cus_name}}</td>
                                                <td>{{$customer->review}}</td>
                                                <td>{{$customer->desc}}</td>
                                                <td><button class="btn btn-danger btn-sm" onclick="delCustomer_rev('{{$customer->id}}')" value="{{$customer->id}}"><i class="fa-solid fa-trash"></i></button></td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </section> -->
                      <!-- </div> -->
                    </div>
                   
                </div>
            </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>

<script>
    
    // delete customer
    function delCustomer($id){
        var id = $id;
        var $row = $('.customer_row'+id);
        var $count = parseInt($('.cus_count').html()||0);
        
        $.ajax({
                     type:"POST",
                     url:"{{route('del_product_custmer')}}" ,
                     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                     data: {
                          'id' : id,
                     },
                     success: function(response){

                          if(response.check_num == 100)
                          {
                             $count -= 1;
                             $row.hide();
                             $('.cus_count').html($count);
                             
                        }
                           
                        },
         });
    }
    
    //delete Reviews
    function delCustomer_rev($id){
        var id = $id;
        var $row = $('.customer_row_rev'+id);
        var $count = parseInt($('.cus_count_rev').html()||0);
        
        $.ajax({
                     type:"POST",
                     url:"{{route('del_product_custmer_rev')}}" ,
                     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                     data: {
                          'id' : id,
                     },
                     success: function(response){

                          if(response.check_num == 100)
                          {
                             $count -= 1;
                             $row.hide();
                             $('.cus_count_rev').html($count);
                             
                        }
                           
                        },
         });
    }

</script>


@endsection


