@extends('admin.layouts.app')
@section('content')
@section('title') Categories @endsection


<div class="main-content">

    <!-- add Modal -->
    @section('modal_header')
    <h5 class="modal-title" id="exampleModalLabel">Add New Category</h5>
    @endsection

    @section('modal_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <section class="section">
                    <div class="section-body">
                        <form method="POST" data-add-route="{{route('category.store')}}" enctype="multipart/form-data" id="addNewRecordForm" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <x-x-input label="Category Name" for="" type="text" name="category_name" required placeholder="Enter Category Name" />
                                </div>
                                <!--Thumbail-->
                                <div class="col-lg-7 col-5 d-column">
                                    <div class="form-group">
                                        <label>Category Image</label>
                                        <input type="file" class="image form-control" name="image" accept="image/*" onchange="preview()">
                                    </div>
                                </div>

                                <div class="col-lg-5 col-7 img_preview_container" style="display:none">
                                    <img class="img rounded-circle" width="150px" height="150px" src="" alt="Category_Thumbnail" id="frame">
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

    <!-- delete record modal -->
    @section('delete_modal_footer')
    <x-x-delete-record deleteRoute="{{ route('category.destroy') }}" />
    @endsection
    <!-- end Delete Modal -->

    <div class="container">

        <!-- record add & count -->
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-between">
                    <!-- total record -->
                    <div class="col-auto d-flex align-items-start">
                        <x-x-record-count :total-records="$total_records" />
                        <button class="ms-2 btn  btn-info d-none pinItems" id="pinItems"></button>
                    </div>

                    <!-- add new category -->
                    <x-x-add-button />
                </div>
            </div>

            <!-- filter Row -->
            <div class="col-12 mb-3">
                <form method="GET" action="{{route('category.index')}}">@csrf
                    <div class="row align-items-center justify-content-end">

                        <x-filter.x-record-select label="Records" name="records" class="" :options="[10 => '10', 25 => '25', 50 => '50', 100 => '100']" :selected="request()->get('records')"></x-filter.x-record-select>

                        <x-filter.x-record-select label="Select Status" name="status" class="me-2" :options="['all' => 'All', 0 => 'Active', 1 => 'In-Active']" :selected="request()->get('status')"></x-filter.x-record-select>

                        <x-filter.x-input-search label="Search" name="search_input" class="" placeholder="Name.." :value="request()->get('search_input')"></x-filter.x-input-search>

                        <!-- filter button -->
                        <div class="col-auto">
                            <button class="mt-lg-4 btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <!-- Record Display -->
        <div class="row my-3">
            <div class="col-12">
                @if(count($categories)>0)
                <div class="table-responsive">
                    <table class="table table-hover  active_table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="main_checkbox" style="background-color: aquamarine"></th>
                                <th>Nmae</th>
                                <th>Products</th>
                                <th>Active Stock</th>
                                <th>Sold Out</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr class="{{ $category->is_active_row == 1 ? 'highlight': ''}}">
                                <td><input type="checkbox" value="{{$category->id}}" name="cat_checkbox"></td>
                                <td>{{$category->name}}
                                    @if($category->image)
                                    <a href="{{$category->image}}" target="_blank"><img src="{{$category->image}}" height="50px" width="50px" alt="Category Image" class="ms-3 rounded-circle" /></a>
                                    @endif
                                </td>

                                <td>{{$category->product_count}}</td>
                                <td>
                                    <span class="{{$category->active == 0 ? 'bg-success' : 'bg-primary'}} badge text-white p-2">
                                        {{$category->active}}
                                    </span>
                                </td>
                                <td>{{$category->soldout}}</td>
                                <td>
                                    <span class="fs-5 text-center p-1">{!! $category->status == 0 ? '<i class="fa-solid fa-square-check text-success"></i>' : '<i class="fa-solid fa-circle-xmark text-danger"></i>' !!}</span>
                                </td>
                                <td class="text-center d-lg-flex align-items-center ">
                                    <a href="{{route('category.edit',$category->id)}}" class="me-lg-2 btn btn-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <button value="{{$category->id}}" class="deleletRecordIconButton {{$category->product_count == 0 ? 'btn-warning' : 'btn-danger'}} btn  btn-sm"><i class="fa-solid fa-delete-left"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $categories->appends(request()->all())->links() !!}
                @else
                <div class="alert alert-warning text-white">No Category record</div>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                url: "{{route('category.pinnedCheckedTop')}}",
                data: {
                    ids: allids,
                },

                success: function(response) {
                    toastr.success('Categories Pin to Start Duccessfully');
                    window.location.reload();
                },
            });
        });
    });
</script>
@endsection