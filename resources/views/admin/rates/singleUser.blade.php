@extends('admin.layouts.app')
@section('content')
@section('title')
    User Detail
@endsection

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <section class="section">
                    <div class="section-body">
                        <div class="card">
                            <form method="POST" enctype="multipart/form-data" action="{{ route('updateUser') }}">
                                @csrf
                                <!--card Body-->
                                <div class="card-body">
                                    <div class="row align-items-center justify-content-between gy-2">
                                        <div class="col-auto">
                                            <a href="javascript:history.go(-1)" class="btn text-white btn-primary">
                                                <i class="fa-solid fa-arrow-left"></i>
                                            </a>
                                            <h6 class="d-inline mx-2"> ID <span
                                                    class="border border-success p-1">{{ $user->id }}</span></h6>
                                        </div>
                                        <div class="col-auto mt-2 mt-sm-0">
                                            <p class="border border-dark p-1">
                                                <span class="text-secondary"> Register:</span>
                                                <span class="fw-bold">{{ $user->created_at->format('d') }}</span>
                                                <span class="mos">{{ $user->created_at->format('M') }}</span>
                                                <span class="yr">{{ $user->created_at->format('Y') }}</span>
                                            </p>
                                        </div>
                                    </div><br>

                                    <!--User basic deatail-->
                                    <div class="row justify-content-between">
                                        <!--name-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-regular fa-id-badge"></i> Name</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    {{ $user->name }}
                                                    <span
                                                        class="badge {{ $user->status == 1 ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $user->status == 1 ? 'blocked' : 'active' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <!--city based on id remain-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label> <i class="me-1 fa-solid fa-city"></i> City</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    {{ $user->city ? $user->city->name : $user->city_name }}</p>
                                            </div>
                                        </div>

                                        <!--address-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-solid fa-address-card"></i> Address</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    {{ $user->address }}</p>
                                            </div>
                                        </div>

                                        <!--whastappp-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-brands fa-whatsapp fs-6"></i> Whatsapp</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    <a href="https://wa.me/+92{{ $user->whatsapp }}?text=Hi%20Welcome%20to%20WAO"
                                                        target="_blank" class=" pb-0 btn text-success fw-bold">
                                                        {{ $user->whatsapp }}
                                                    </a>
                                                </p>
                                            </div>
                                        </div>

                                        <!--corier contact-->
                                        <div class="col-auto">
                                            <div class="form-group">
                                                <label><i class="me-1 fa-solid fa-phone"></i> Contact Number</label>
                                                <p class="pb-2 text-center" style="border-bottom:1px solid #bfb5b5;">
                                                    {{ $user->phone }}</p>
                                            </div>
                                        </div>
                                        @if ($user->bussiness_detail)
                                            <h5 class="mt-2 fw-bold">User Business Detail</h5>
                                            <div class="col-auto">
                                                <div class="form-group">
                                                    <label>Store Name</label>
                                                    <p class="pb-2 text-center"
                                                        style="border-bottom:1px solid #bfb5b5;">{{ $user->bussiness_detail->store_name }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-group">
                                                    <label>Store Address</label>
                                                    <p class="pb-2 text-center"
                                                        style="border-bottom:1px solid #bfb5b5;">{{ $user->bussiness_detail->store_address }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-group">
                                                    <label>Bank Name</label>
                                                    <p class="pb-2 text-center"
                                                        style="border-bottom:1px solid #bfb5b5;">{{ $user->bussiness_detail->bank_name }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-auto">
                                                <div class="form-group">
                                                    <label>Account Title</label>
                                                    <p class="pb-2 text-center"
                                                        style="border-bottom:1px solid #bfb5b5;">{{ $user->bussiness_detail->account_title }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-auto">
                                                <div class="form-group">
                                                    <label>Account Number</label>
                                                    <p class="pb-2 text-center"
                                                        style="border-bottom:1px solid #bfb5b5;">{{ $user->bussiness_detail->account_number }}
                                                    </p>
                                                </div>
                                            </div>

                                        @endif
                                    </div>

                                    <!--order Notes-->
                                    @if (count($orders))
                                        <div class="row align-items-start justify-content-between">
                                            <!--total Order-->
                                            <div class="col-auto">
                                                <div class="form-group">
                                                    <label><i class="me-1 fa-regular fa-id-badge"></i> Total
                                                        Orders</label>
                                                    <p class="text-center" style="border-bottom:1px solid #bfb5b5;">
                                                        {{ $user->order_count }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a class=" border btn border-success view_user_orders ms-auto"><i
                                                        class="fa-solid fa-eye"></i> View Last 10 Orders</a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--Notes-->
                                            <div class="col-12 user_orders_div" style="display:none;">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Type</th>
                                                            <th>Status</th>
                                                            <th>Order-Items</th>
                                                            <th>Total-Bill</th>
                                                            <th>Date & Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-center table table-hover">
                                                        @foreach ($orders as $order)
                                                            <tr>
                                                                <td>{{ $order->id }}</td>
                                                                <td
                                                                    class="{{ $order->is_blocked_customer_order == 1 ? 'text-danger' : '' }}">
                                                                    {{ $order->is_blocked_customer_order == 1 ? 'Fake Order' : 'Real Order' }}
                                                                </td>
                                                                <td
                                                                    class="{{ $order->status == 'DISPATCHED' ? 'text-success' : '' }}">
                                                                    <span
                                                                        class="fw-bold {{ $order->status == 'CANCEL' ? 'text-danger' : '' }}">{{ $order->status }}</span>
                                                                </td>
                                                                <td><span
                                                                        class="fw-bold">{{ $order->orderitems_count }}</span>(Items)
                                                                </td>
                                                                <td><span class="fw-bold">Rs :</span>
                                                                    {{ $order->grandTotal }}</td>
                                                                <td>
                                                                    <span class="fw-bold">{{ $order->date }}</span>
                                                                    <span
                                                                        class="text-secondary ps-2">{{ $order->time }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <!-- card Body -->

                                <!-- Foter submit Button and work -->
                                <div class="card-footer  pt-0">

                                    <!--Status-->
                                    <div class="row align-items-end">
                                        <!-- select Status -->
                                        <div class="col-lg-3">
                                            <label class="">Select Status</label>
                                            <select name="status" class="form-control">
                                                <option value="" disabled>Select Status</option>
                                                <option value="2"
                                                    @if ($user->status == 2) selected @endif>Active</option>
                                                <option value="1"
                                                    @if ($user->status == 1) selected @endif>Block</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <label class="">Enter New Password</label>
                                            <input type="text" class="form-control" minlength="5" maxlength="20"
                                                name="password" placeholder="Password for User">
                                        </div>
                                        @if ($user->bussiness_detail)
                                            <div class="col-lg-4">
                                                <label class="">Post-Ex Address Code</label>
                                                <input type="text" class="form-control" name="postex_address_code"
                                                    placeholder="Post-Ex address code"
                                                    value="{{ $user->bussiness_detail->postex_address_code ?? '' }}">
                                            </div>
                                        @endif

                                        <div class="col-auto my-sm-0 my-3">
                                            <input type="hidden" name="user_id" value="{{ $user->id }}" />
                                            <button class=" btn btn-primary d-block " type="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Footer -->
                            </form>
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
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // View User Orders
        $(document).on('click', '.view_user_orders', function(e) {
            e.preventDefault();
            $('.user_orders_div').toggle(1000);
        });
    });
</script>
@endsection
