@extends('admin.layouts.app')
@section('content')
@section('title') App Manage @endsection

<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }

    .ck-editor__editable[role="textbox"] {
        /* editing area */
        min-height: 150px;
    }
</style>
<div class="main-content">
    <!-- Update Modal -->
    @section('update_modal_header')
    <h5 class="modal-title" id="exampleModalLabel">Update Category</h5>
    @endsection

    @section('update_modal_body')
    <form method="POST" id="updatemessage" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <textarea name="message" class="form-control message_to_all_update" placeholder="Enter Message"></textarea>
                </div>
            </div>
            <!--status-->
            <div class="d-flex justify-content-between align-items-center">
                <div class="form-group">
                    <label for="days">Enter Message Expire Days</label>
                    <input type="number" class="form-control upd_days" placeholder="0" name="days" id="days">
                </div>
                <input type="text" hidden class="upd_message_id" name="message_id">
                <button class="btn btn-primary float-end" type="submit">Update</button>
            </div>
        </div>
    </form>
    @endsection
    <!-- end Update Modal -->

    <!-- delete record modal -->
    @section('delete_modal_footer')
    <form id="delete_message_form" method="POST">
        @csrf
        <input hidden type="text" id="delete_message_id" name="message_id">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
    </form>
    @endsection
    <!-- end Delete Modal -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <section class="section">
                    <div class="section-body">
                        <!-- change password of lock folder -->
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{route('update_lockfolder_password')}}" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-8 m-auto">
                                            <h5 class="text-center py-2">Update Lock Folder Password</h5>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="password" required minlength="5" maxlength="20" placeholder="Enter Locked Folder Password" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @if($locked_pass)
                                            <h6 class="mt-4 px-1"> <span class="border border-info p-1"><i class="fa-solid fa-lock"></i> : {{$locked_pass->text_password}}</span> </h6>
                                            @endif
                                            <button class="btn btn-primary" type="submit">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- change product sold items input range -->
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{ route('update_product_fake_sold_range') }}" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 m-auto">
                                            <h5 class="text-center py-2">Update Product Fake Sold Range</h5>
                                            <div class="form-group d-flex align-items-center row">
                                                <div class="col-12"><label>Stop Increasing Fake Item After Quantity</label></div>
                                                <input type="number" class="form-control col-5 mx-1"  name="stop_increasing_after_qty_start" required min="0" max="500" placeholder="Start" value="{{ old('stop_increasing_after_qty_start', $product_fake_range->stop_increasing_after_qty_start) }}" />
                                                <input type="number" class="form-control col-5 mx-1"  name="stop_increasing_after_qty_end" required min="0" max="500" placeholder="End" value="{{ old('stop_increasing_after_qty_end', $product_fake_range->stop_increasing_after_qty_end) }}" />
                                            </div>

                                            <div class="form-group d-flex align-items-center row">
                                                <div class="col-12"><label>Start From Fake Sold Items</label></div>
                                                <input type="number" class="form-control col-5 mx-1"  name="start_from_items_start" required min="0" max="500" placeholder="Start" value="{{ old('start_from_items_start', $product_fake_range->start_from_items_start) }}" />
                                                <input type="number" class="form-control col-5 mx-1"  name="start_from_items_end" required min="0" max="500" placeholder="End" value="{{ old('start_from_items_end', $product_fake_range->start_from_items_end) }}" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button class="btn btn-primary" type="submit">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- update product article range from 01 restart -->
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{ route('restartProductArticleRange') }}" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 m-auto">
                                            <h5 class="text-center py-2">Restart Product Article Range</h5>
                                            <div class="form-group d-flex align-items-center row">
                                                <div class="col-12"><label>Enter Range and text</label></div>
                                                <input type="number" class="form-control col-4"  name="artcle_start" required min="1" placeholder="Range Limit" value="{{ old('artcle_start') }}" />
                                                <input type="number" class="form-control col-4 my-2 mx-lg-1"  name="artcle_end" required min="2" placeholder="Range Limit" value="{{ old('artcle_end') }}" />
                                                <input type="text" class="form-control col-md-4"  name="text_artcle" placeholder="Range Text" required min="1" value="{{ old('text_artcle') }}" />
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button class="btn btn-primary" type="submit">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- send message to all users on app -->
                        <div class="card">
                            <div class="card-body">
                                <!-- Message for all users -->
                                <form method="POST" action="{{route('message_to_all')}}" autocomplete="off">@csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="text-center py-2">Message To all Users</h5>
                                            <div class="form-group">
                                                <textarea name="message" class="form-control" required placeholder="Enter Message"></textarea>
                                                <span class="text-danger">@error('$message'){{$message}}@enderror</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-group">
                                                    <label for="days">Enter Message Expire Days</label>
                                                    <input type="number" class="form-control" placeholder="0" name="days" id="days">
                                                </div>
                                                <button class="btn btn-primary float-end" type="submit">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- update text attech with name of product for sale -->
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{route('changeProductSaleReason')}}" autocomplete="off">
                                    @csrf
                                    <div class="row gy-2 align-items-center">
                                        <h5 class="text-center py-2">Change Text for Active Products Sale Reason </h5>

                                        <div class="col-lg-6 col-12">
                                            <label for="product_sale_reason">Enter Message</label>
                                            <textarea name="product_sale_reason" class="form-control"  id="" cols="10" rows="3">{{$locked_pass->product_sale_reason}}</textarea>
                                        </div>
                                        <div class="col-auto mx-auto">
                                            <button class="btn btn-primary" type="submit">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- change order status of unbooked orders(postex) -->
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{route('changeStatusOfUnbookedOrders')}}" autocomplete="off">
                                    @csrf
                                    <div class="row gy-2 align-items-center">
                                        <h5 class="text-center py-2">Change Status Of UnderTeamReview(Postex) Orders</h5>
                                        <div class="col-lg-3 col-12">
                                            <label for="startDateTime">Select Start Date and Time</label>
                                            <input required class="form-control" type="datetime-local" id="startDateTime" name="startDateTime">
                                        </div>
                                        <div class="col-lg-3 col-12">
                                            <label for="endDateTime">Select End Date and Time</label>
                                            <input required class="form-control" type="datetime-local" id="endDateTime" name="endDateTime">
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <label for="message">Enter Message</label>
                                            <textarea name="message" class="form-control" id="" cols="10" rows="3"></textarea>
                                        </div>
                                        <div class="col-auto mx-auto">
                                            <button class="btn btn-primary" type="submit">Change Status</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-sm-12">
                <div class="table-responsive">
                    @if(count($messages)>0)
                    <table class="table table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th class="text-white">Message</th>
                                <th class="text-white">Expire Days</th>
                                <th class="text-white">User's Views</th>
                                <th class="text-white">Created</th>
                                <th class="text-white">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $msg)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::limit($msg->message, 200, $end = '...') }}</td>
                                <td>{{$msg->days == null ? 'All the Time' : $msg->days}}</td>
                                <td>{{$msg->views}}</td>
                                <td>{{$msg->created_at->toDateString()}}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm edit_message" value="{{$msg}}">Edit</button>
                                    <button value="{{$msg->id}}" class="del_message btn btn-danger mt-sm-0 mt-1  btn-sm">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @endif
                </div>
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
        // get message in  modal
        $(document).on('click', '.edit_message', function(e) {
            e.preventDefault();
            var msg = JSON.parse($(this).val());
            $('.message_to_all_update').val(msg.message);
            $('.upd_days').val(msg.days);
            $('.upd_message_id').val(msg.id);
            $('#Update').modal('show');
        });

        // update message
        $(document).on('submit', '#updatemessage', function(e) {
            e.preventDefault();

            let formdata = new FormData($('#updatemessage')[0]);
            $.ajax({
                type: "POST",
                url: "{{route('updatemessage_to_all')}}",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status == 0) {
                        toastr.error(response.data);
                    }
                    if (response.status == 2) {
                        toastr.success(response.data);
                        window.location.reload(true);
                    }
                },
            });
        });

        //delet camessagetegory 
        $(document).on('click', '.del_message', function(e) {
            e.preventDefault();
            var message_id = $(this).val();
            $('#delete_message_id').val(message_id);
            $('#DeleteModalRecord').modal('show');
            //delet form submit
            $(document).on('submit', '#delete_message_form', function(e) {
                e.preventDefault();

                let formdata = new FormData($('#delete_message_form')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{route('del_message_to_all')}}",
                    data: formdata,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        toastr.success(response.data);
                        window.location.reload(true);
                    },
                });
            });
        });
    });
</script>
@endsection