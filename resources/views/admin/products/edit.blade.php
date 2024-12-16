@extends('admin.layouts.app')
@section('content')
@section('title') Edit Product @endsection

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <section class="section">
                    <div class="section-body">
                        <div class="card">
                            <form method="POST" enctype="multipart/form-data" action="{{route('update',$product->id)}}">
                                @csrf
                                <div class="card-header d-flex justify-content-between">
                                    <h4>Edit Product</h4>
                                    <a href="javascript:history.back()" class="btn text-white btn-primary mb-2">Back</a>
                                </div>
                                <div class="card-body">
                                    @include('admin.products.form-inputs')
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary" type="submit">Update</button>
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
@endsection