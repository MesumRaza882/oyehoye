@extends('admin.layouts.app')
@section('title') Markeet Pickup @endsection
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

                <div class="row mb-3">
                    <div class="col-auto">
                        <form action="{{ route('markeetPickupQty.destroy', 1) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="ms-2 btn-sm btn btn-danger delete-button" data-bs-toggle="tooltip" title="delete record" data-name="Markeet Pickup Qty">
                                <i class="icon ion-md-trash"></i> Reset Markeet Qty
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Aricle Number</th>
                                <th>Product Name</th>
                                <th>Markeet Pickup Quantity</th>
                                <th>thumbnail</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($articles as $product)
                            <tr>
                                <td><span class="fw-bold">{{$product->article}}</span></td>
                                <td>{{$product->name}}</td>
                                <td>
                                    <span class=" fs-6 badge bg-primary">{{$product->markeetPickup}}</span>
                                </td>
                                <td>
                                    <a href="{{$product->thumbnail}}" target="_blank">
                                        <img src="{{$product->thumbnail}}" alt="thumbnail" width="200px" height="200px" class="img">
                                    </a>
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
    $('.delete-button').click(function(e) {
        e.preventDefault();

        const deleteForm = $(this).closest('form');
        const dataName = $(this).data('name');

        Swal.fire({
            title: `Are you sure to delete "${dataName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteForm.submit();
            }
        });
    });
</script>
@endsection