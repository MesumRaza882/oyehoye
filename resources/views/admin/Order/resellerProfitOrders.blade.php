@extends('admin.layouts.app')
@section('title') Reseller Profit Orders @endsection
@section('content')

<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }

    .table tr td {
        text-align: center;
    }

    @media (max-width: 576px) {
        .head_order p {
            font-size: 13px;
        }
    }
</style>

<div class="main-content">

    <!-- modal for mark as paid profit -->
    <div class="modal fade" id="markasPaidProfit" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="markasPaidProfitLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markasPaidProfitLabel">Make Orders as Paid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paidProfitForm" enctype="multipart/form-data">@csrf
                        <div class="row">
                            <div class="col-lg-8 offset-lg-2">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p>Total Paid Profit:</p>
                                        <p class="border border-info p-2" id="totalPaidProfit"></p>
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_method">Select Payment Method <span class="fw-bold text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method" required class="form-control">
                                            <option value="jazcash">JazCash</option>
                                            <option value="easypaisa">EasyPiasa</option>
                                            <option value="bank">Bank Account</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <x-x-input for="profit_screenshot" label="Profit Transaction Screenshot" type="file" name="profit_screenshot" id='profit_screenshot' accept="image/*" />
                                    </div>
                                </div>
                                <center>
                                    <button type="button" class="paidProfitButton m-auto btn btn-primary">Save changes</button>
                                </center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- filter records -->
            <div class="row">
                <!-- total record count -->
                <div class="col-auto d-flex align-items-center head_order">
                    <p class="p-sm-2 p-1 bg-primary text-white">Orders:<span class="ms-1">{{ number_format(($total_records), 0, '.', ',') }}</span></p>
                    <button class="ms-2 btn btn-sm btn-info d-none profitMarkasPaid" id="profitMarkasPaid">Mark as Paid</button>
                </div>
                <div class="col-auto head_order">
                    <p class="mx-1 border p-sm-2 p-1 fw-bold border-info">
                        Pending Profit:
                        <span class="ms-1 badge bg-primary p-sm-2 p-1" style="font-size:14px;">{{ number_format($orders->where('profit_transaction_status','pending')->sum('reseller_profit'), 0, '.', ',') }}</span>
                    </p>
                </div>

                <div class="col-auto head_order">
                    <p class="mx-1 border p-sm-2 p-1 fw-bold border-success">
                        Paid Profit:
                        <span class="ms-1 badge bg-primary p-sm-2 p-1" style="font-size:14px;">{{ number_format($orders->where('profit_transaction_status','paid')->sum('reseller_profit'), 0, '.', ',') }}</span>
                    </p>
                </div>

                <div class="col-12 py-0 my-0">
                    <hr>
                </div>

                <!-- filter -->
                <div class="col-12 mb-2">
                    <form method="GET" action="{{route('ordersProfit')}}" id="search-form">
                        <input type="hidden" name="is_reseller_order" value="{{ request()->is_reseller_order }}" />
                        <div class="row justify-content-between">

                            <!-- select records -->
                            <div class="col-lg-2 mb-2 col-6">
                                <label class="pb-0">Records</label>
                                <select name="records" class="form-control me-2" required>
                                    <option value="50" @if(request()->get('records') == 50) selected @endif>50</option>
                                    <option value="100" @if(request()->get('records') == 100) selected @endif>100</option>
                                    <option value="200" @if(request()->get('records') == 200) selected @endif>200</option>
                                    <option value="300" @if(request()->get('records') == 300) selected @endif>300</option>
                                    <option value="500" @if(request()->get('records') == 500) selected @endif>500</option>
                                </select>
                            </div>

                            <!-- seller -->
                            <div class="col-lg-3 mb-2 mb-2 col-6">
                                <label class="pb-0">Select Admins</label>
                                <select name="admin_id" class="form-control me-2">
                                    <option value="">All</option>
                                    @foreach ($admins as $seller)
                                    <option value="{{$seller->id}}" @if(request()->get('admin_id') == $seller->id) selected @endif>{{$seller->email}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- select Status -->
                            <div class="col-lg-3 mb-2 col-6">
                                <label class="pb-0"> Order Status</label>
                                <select name="status" class="form-control me-2">
                                    <option value="">All</option>
                                    <option value="PENDING" @if(request()->get('status') == 'PENDING') selected @endif>PENDING</option>
                                    <option value="DISPATCHED" @if(request()->get('status') == 'DISPATCHED') selected @endif>DISPATCHED</option>
                                    <option value="DELIVERED" @if(request()->get('status') == 'DELIVERED') selected @endif>DELIVERED</option>
                                    <option value="ON-THE-WAY" @if(request()->get('status') == 'ON-THE-WAY') selected @endif>ON-THE-WAY</option>
                                    <option value="RETURNED" @if(request()->get('status') == 'RETURNED') selected @endif>RETURNED</option>
                                    <option value="Team Review your Order" @if(request()->get('status') == 'Team Review your Order') selected @endif>Team Review your Order</option>
                                    <option value="CANCEL" @if(request()->get('status') == 'CANCEL') selected @endif>CANCEL</option>
                                </select>
                            </div>

                            <!-- select Status -->
                            <div class="col-lg-3 mb-2 col-6">
                                <label class="pb-0"> Profit Transaction Status</label>
                                <select name="profit_transaction_status" class="form-control me-2">
                                    <option value="">All</option>
                                    <option value="pending" @if(request()->get('profit_transaction_status') == 'pending') selected @endif>Pending</option>
                                    <option value="paid" @if(request()->get('profit_transaction_status') == 'paid') selected @endif>Piad</option>
                                </select>
                            </div>

                            <!-- filter by orderId -->
                            <div class="col-lg-4 mb-2">
                                <label class="pb-0">Search</label><input type="search" value="{{request()->get('search_input')}}" class="me-2 form-control" name="search_input" placeholder="Order-Id">
                            </div>

                            <div class="col-lg-auto col-6 mb-2">
                                <label>From</label><input type="date" value="{{request()->get('fromDate')}}" class="me-2 form-control" name="fromDate" max="<?php echo date("Y-m-d"); ?>">
                            </div>
                            <div class="col-lg-auto col-6 mb-2">
                                <label>To</label><input type="date" value="{{request()->get('toDate')}}" class="form-control" name="toDate" max="<?php echo date("Y-m-d"); ?>">
                            </div>
                            <div class="col-auto mt-3">
                                <a class="btn btn-secondary btn-sm" id="reset-button">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display Records -->
            <div class="col-12">
                @if(count($orders)>0)
                <table class="table table-striped table-hover table-responsive  active_table" id="examples">
                    <thead>
                        <tr>
                            <th><input type="checkbox" value="{{request()->get('main_checkbox')}}" name="main_checkbox" id="select_all" style="background-color: aquamarine"></th>
                            <th>id</th>
                            <th>User</th>
                            <th>Admin</th>
                            <th>Order Status</th>
                            <th>Items</th>
                            <th>Articles</th>
                            <th>Reseller Profit</th>
                            <th>Profit Transc Status</th>
                            <th>Profit Transc Proof</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $rate)
                        <tr class="{{ $rate->is_active_row == 1 ? 'highlight': ''}}">
                            <td>
                                <input type="checkbox" class="checkbox" value="{{$rate->id}}" name="cat_checkbox">
                                {{-- @if ($rate->profit_transaction_status === 'pending' && ($rate->status === 'Delivered' || $rate->status === 'DELIVERED'))
                                @endif --}}
                            </td>

                            <td>
                                <a href="{{route('editOrder',$rate->id)}}" class="btn btn-outline-primary text-dark">{{$rate->id}}</a>
                                <!-- type 2 means website 1 for app -->
                                @if($rate->type === 2)
                                <span class="badge badge-primary px-1 py-0">website</span>
                                @elseif($rate->type === 1)
                                <span class="badge badge-info px-1 py-0">App</span>
                                @endif
                            </td>

                            <td>
                                <span>{{ $rate->name }}</span>
                                <br>
                                <span class="badge bg-info">
                                    {{ $rate->citydetail ? ($rate->citydetail->c_city_name ? $rate->citydetail->c_city_name : ($rate->citydetail->name ? $rate->citydetail->name : $rate->citydetail->postex)) : $rate->city }}
                                </span>
                            </td>

                            <td>
                                <span class="fw-bold">{{$rate->waoAdminDetail->name}}</span><br>
                                <span>{{$rate->waoAdminDetail->website}}</span>
                            </td>
                            <td class="fw-bold {{$rate->status == 'CANCEL' ? 'text-danger' : ''}}" style="@if($rate->status == 'DISPATCHED') color:green;  @endif">
                                ge- <span class="status-cell">{{$rate->status}}</span>
                            </td>

                            <td>
                                <span>{{ $rate->orderitems->sum('qty') }}</span>
                            </td>
                            <td>
                                @foreach ($rate->orderitems as $item)
                                    {{ $item->product->article ?? 'N/A' }}<span
                                        class="{{ $item->qty > 1 ? '' : 'd-none' }}">( x
                                        {{ $item->qty }})</span>,
                                @endforeach
                            </td>
                            <td>
                                <h6 class="fw-bold"> Rs: {{$rate->reseller_profit}}</h6>
                            </td>
                            <td>
                                @if($rate->profit_transaction_status === 'pending')
                                <span class="badge badge-primary p-1">pending</span>
                                @else
                                <span class="badge badge-success p-1">paid</span><br>
                                @endif
                                <span class="badge badge-info p-2 mt-1">{{$rate->payment_method}}</span>
                            </td>
                            <td>
                                @if($rate->profit_screenshot)
                                <a href="{{ $rate->profit_screenshot }}" target="_blank" />
                                <img src="{{ $rate->profit_screenshot }}" height="80px" width="60px" />
                                </a>
                                @endif
                            </td>
                            <td>
                                Rs: <span class="fw-bold">{{$rate->amount}}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{$rate->created_at->format('d')}} {{$rate->created_at->format('M')}} {{$rate->created_at->format('Y')}}</span>
                                <span class="fw-bold">{{$rate->time}}</span>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $orders->appends(request()->all())->links() !!}
                @else
                <div class="alert alert-warning text-white">No Orders Record Available</div>
                @endif
            </div>
            <!-- end records display -->
        </div>
        <!-- End Row -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        $('.checkbox').on('click', function() {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });

        $('#select_all').on('click', function() {
            if (this.checked) {
                $('.checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function() {
                    this.checked = false;
                });
            }
        });

        // add to white list for custom order
        $(document).on('click', '.profitMarkasPaid', function(e) {
            e.preventDefault();
            var allids = [];

            $('input[name="cat_checkbox"]:checked').each(function() {
                allids.push($(this).val());
            });

            $.ajax({
                type: "GET",
                url: "{{route('ordersProfitCalc')}}",
                data: {
                    ids: allids,
                },

                success: function(response) {
                    console.log(response.data);
                    $('#markasPaidProfit').modal('show');
                    $('#totalPaidProfit').text(response.data);
                },

            });
        });

        $('.paidProfitButton').click(function(e) {
            e.preventDefault();

            const button = $(this);
            button.prop('disabled', true);

            var allids = [];

            $('input[name="cat_checkbox"]:checked').each(function() {
                allids.push($(this).val());
            });

            var form = $('#paidProfitForm')[0];
            var formData = new FormData(form);

            // Append allids to formData
            formData.append('allids', JSON.stringify(allids));

            $.ajax({
                type: 'POST',
                url: '{{ route("ordersProfitPaid") }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status === 2) {
                        toastr.success(response.message);
                        window.location.reload();
                    }
                    if (response.status === 1) {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    button.prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
