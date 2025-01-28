@extends('admin.layouts.app')
@section('content')
@section('title') Products @endsection


<div class="main-content">
    <div class="container">
        <div class="row">
            <!-- Top Row -->
            <div class="row justify-content-between">

                <!-- total record count -->
                <div class="col-auto d-flex align-items-start">
                    <p class="d-block p-1 px-2 bg-primary text-white">Products:<span class="ms-1">{{$total_records}}</span></p>
                    <!-- update qty -->
                    <!-- <button id="editBtn" class=" ms-2 btn btn-sm btn-primary">Edit Quantity</button>
                    <button id="submitBtn" class=" ms-2 btn btn-sm btn-success" style="display:none;">Submit</button>
                    <button class="ms-2 btn  btn-danger d-none deleteAllbtnItems" id="deleteAllbtn"></button>
                    <button class="ms-2 btn text-dark  btn-secondary d-none pinItems" id="pinItems"></button>
                    <button class="ms-2 btn  btn-info  d-none whiteItems" id="whiteItems"></button>
                    <button class="ms-2 btn  btn-outline-dark  d-none draftToPublishItems" id="draftToPublishItems"></button> -->
                </div>


                <!-- filter -->
                <div class="col-12 mb-3">
                    <form method="GET" action="{{route('waoseller.products')}}" id="search-form">

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
                                </select>
                            </div>

                            <!-- select Status for product  -->
                            <div class="col-lg-3 mb-2">
                                <label class="pe-1">Product Upload Status</label>
                                <select name="product_upload_status" class="form-control me-2">
                                    <option value="" selected>All</option>
                                    <option value="published" @if(request()->get('product_upload_status') == 'published') selected @endif>Published</option>
                                    <option value="draft" @if(request()->get('product_upload_status') == 'draft') selected @endif>Drfat</option>
                                </select>
                            </div>

                            <!-- if reseller has app option then select options -->
                            @if (auth()->user()->type === 3)
                            <!-- select products for web all app -->
                            <div class="col-lg-2 mb-2 col-6">
                                <label class="pb-0">Select Products For</label>
                                <select name="for_app_reseller" class="form-control me-2">
                                    <option value="">All</option>
                                    <option value="1" @if(request()->get('for_app_reseller') == 1) selected @endif>For App Products</option>
                                </select>
                            </div>
                            @endif

                            <!-- filter by name and price -->
                            <div class="col-lg-3 mb-2">
                                <label class="pe-1">Search</label><input type="search" value="{{request()->get('search_input')}}" class="me-2 form-control" name="search_input" placeholder="Name & Price">
                            </div>

                            <!-- filter by article  -->
                            <div class="col-lg-3 mb-2">
                                <label class="pe-1">Article</label><input type="search" value="{{request()->get('article')}}" class="me-2 form-control" name="article" placeholder="Search By Article">
                            </div>

                            <div class="col-auto  mt-3">
                                <a class="btn btn-secondary btn-sm" id="reset-button">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                </a>
                                <button class="btn btn-primary" type="submit">Filter Products</button>
                            </div>

                        </div>


                    </form>
                </div>
            </div>
            <!-- end Top row -->

            <div class="col-md-12 mx-auto">
                @if(count($items)>0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped active_table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="main_checkbox" style="background-color: aquamarine"></th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Article</th>
                                <!-- <th>Orignal Price</th>
                                <th>Orignal Profit</th>
                                <th>Quantity</th> -->
                                <th>Your Own Put Profit</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <form id="editForm" action="{{ route('updateQuantities') }}" method="post"> -->
                            @csrf
                            @foreach($items as $viewLuxury)

                            <tr class="{{$viewLuxury->is_active_row == 1 ? 'highlight': ''}}">
                                <td><input type="checkbox" value="{{$viewLuxury->id}}" name="cat_checkbox"></td>
                                <td>{{$viewLuxury->name}}</td>
                                <td>{{$viewLuxury->itemcategory->name}}</td>
                                <td>
                                    <span class="d-block">{{$viewLuxury->article}}</span>
                                    <span class="badge bg-primary p-1 {{$viewLuxury->resellerUploadProductObject->for_app_reseller === 1 ? '' : 'd-none'}}">For App</span>
                                </td>
                                <!-- <td>Rs {{$viewLuxury->resellerUploadProductObject->price}}</td>
                                <td>
                                    <span>{{$viewLuxury->resellerUploadProductObject->profit}}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{( $viewLuxury->soldItem <= 0 ) ? "SoldOut" : (( $viewLuxury->soldItem < 5 ) ? "Restoke Inventoey" : '' ) }}</span>
                                    <span>{{$viewLuxury->soldItem}}</span>
                                </td> -->
                                <td>
                                    <span>{{$viewLuxury->resellerUploadProductObject->reseller_product_profit > 0 ? 'Rs '.$viewLuxury->resellerUploadProductObject->reseller_product_profit : '-'}}</span>
                                </td>
                                <td data-status-id="{{ $viewLuxury->resellerUploadProductObject->id }}">
                                    <div class="d-flex flex-column">
                                        <span class="badge {{$viewLuxury->resellerUploadProductObject->product_upload_status == 'published' ? 'bg-success' : 'bg-danger'}}">
                                            {{$viewLuxury->resellerUploadProductObject->product_upload_status}}
                                        </span>
                                        @if ($viewLuxury->resellerUploadProductObject->product_upload_status)
                                        <form method="POST" action="{{ route('toggleStatus', $viewLuxury->resellerUploadProductObject->id) }}" id="toggleForm{{ $viewLuxury->resellerUploadProductObject->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group form-switch">
                                                <input class="form-check-input" type="checkbox" onclick="submitForm('{{ $viewLuxury->resellerUploadProductObject->id }}', '{{ 'ResellerSetting' }}');" role="switch" id="flexSwitchCheckDefault{{ $viewLuxury->resellerUploadProductObject->id }}" {{ $viewLuxury->resellerUploadProductObject->product_upload_status == 'published' ? 'checked' : '' }} />
                                            </div>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{$viewLuxury->thumbnail}}" target="_blank">
                                        <img src="{{$viewLuxury->thumbnail}}" alt="thumbnail" width="100px" height="100px" class="img img-responsive">
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">

                                    </div>
                                    <div class="d-flex align-items-center">
                                        <a href="{{route('waoseller.products.edit',$viewLuxury->id)}}" class="mb-lg-0 text-white btn btn-sm  btn-info">Edit</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </form>
                    {!! $items->appends(request()->all())->links() !!}
                </div>
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    

    function submitForm(Id, modelName) {

        // Get the specific form element by combining the ID
        const form = document.getElementById('toggleForm' + Id);

        // Serialize the form data
        const formData = new FormData(form);

        // Add modelName to the form data
        formData.append('modelName', modelName);
        formData.append('id', Id);

        // Submit the form via AJAX
        $.ajax({
            type: 'POST',
            url: form.action,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                if (response.status === 1) {
                    toastr.success(response.message);

                    console.log(response.data);
                    // Update the status text in the corresponding <td> element
                    const modelId = response.data.id;
                    const tdElement = $('td[data-status-id="' + modelId + '"]');
                    const statusText = response.data.product_upload_status;

                    // Update the status text and badge class
                    tdElement.find('span').text(statusText);
                    tdElement.find('span').toggleClass('bg-success bg-danger');

                } else if (response.status === 2) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Status Not Updated!',
                        icon: 'warning',
                    });

                    const checkbox = document.getElementById('flexSwitchCheckDefault' + modelId);
                    checkbox.checked = false;
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }
</script>
@endsection