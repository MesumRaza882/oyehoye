@extends('admin.layouts.app')
@section('title') Edit Product profit @endsection
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
                                    <form method="POST" action="{{route('waoseller.products.update',$product->resellerUploadProductObject->id)}}" autocomplete="off">
                                        @csrf
                                        <div class="card-header d-flex justify-content-between">
                                            <h4>Edit Product Profit</h4>
                                            <a href="javascript:history.back()" class="btn text-white btn-primary mb-2">Back</a>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="">Profit on Product Sale <span class="text-danger">*</span></label>
                                                        <input type="number" min="0" name="reseller_product_profit" value="{{$product->resellerUploadProductObject->reseller_product_profit}}" required placeholder="Enter Product Profit" class="form-control" />
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