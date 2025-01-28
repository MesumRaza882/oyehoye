@extends('admin.layouts.app')
@section('style')

<style>
    .image-container {
        position: relative;
    }

    .delete-icon {
        position: absolute;
        top: 2px;
        right: 3%;
        cursor: pointer;
    }
</style>
@endsection
@section('title') Edit Category @endsection
@section('content')

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <section class="section">
                            <div class="section-body">
                                <div class="card">
                                    <form method="POST" enctype="multipart/form-data" action="{{route('category.update',$category->id)}}" autocomplete="off">
                                        @csrf
                                        <div class="card-header d-flex justify-content-between">
                                            <h4>Edit Category</h4>
                                            <a href="javascript:history.back()" class="btn text-white btn-primary mb-2">Back</a>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <x-x-input label="Category Name" type="text" name="name" value="{{$category->name}}" required placeholder="Enter Category Name" />
                                                </div>
                                                <!--status-->
                                                <div class="col-md-4">
                                                    <label>Status</label>
                                                    <select class="form-control" name="status">
                                                        <option selected value="{{$category->status}}">{{$category->status == 0 ? 'Active' : 'In-Active'}}</option>
                                                        <option class="{{$category->status == 0 ? 'd-none' : ''}}" value="0">Active</option>
                                                        <option class="{{$category->status == 1 ? 'd-none' : ''}}" value="1">In-Active</option>
                                                    </select>
                                                </div>
                                                <!--Thumbail-->
                                                <div class="col-lg-7">
                                                    <div class="form-group">
                                                        <label>Category Image</label>
                                                        <input type="file" class="upd_image form-control" onchange="selected_preview()" id="selected_image" name="image" accept="image/*">
                                                    </div>
                                                    <span class="text-danger">@error('image'){{$message}}@enderror</span>
                                                </div>
                                                <!--Thumbnail Image-->
                                                <div class="col-lg-auto {{$category->image != null ? '' : 'd-none'}}" id="upd_img_container">
                                                    <div class="image-container pe-lg-5">
                                                        <img src="{{$category->image}}" id="selected_frame" width="200px" height="200px" alt="Category_thumbnail" class="img img-responsive" />
                                                        <button type="button" data-name="{{$category->name}}-image" 
                                                        class="delete-button btn btn-sm btn-danger px-2 delete-icon {{$category->image ? '' : 'd-none'}} " data-route="{{ route('category.image.delete', ['id' => $category->id]) }}">&#10006;</button>
                                                    </div>
                                                </div>


                                                <div class="mt-lg-0 mt-3">
                                                    <button class="m-auto d-block btn btn-primary" type="submit">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection