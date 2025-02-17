@extends('admin.layouts.app')
@section('content')
@section('title')
    Seller-Profile
@endsection

<div class="main-content">

    <!-- add Modal -->
    @section('modal_header')
        <h5 class="modal-title" id="exampleModalLabel">Add New Seller</h5>
    @endsection

    @section('modal_body')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <section class="section">
                        <div class="section-body">
                            <form method="POST" data-add-route="{{ route('inventory.seller.store') }}" id="addNewRecordForm"
                                autocomplete="off">
                                @csrf
                                <div class="row align-items-center">
                                    <!-- type reseller or manager -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role" class="group">Select Admin Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="role" id="role">
                                                <option value="4">Warehouse Team-member</option>
                                                @if (request()->query('is_partner') != 1)
                                                    <option value="2" selected>Partner</option>
                                                    <option value="3">Re-Seller</option>
                                                    <option value="1">Manager</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6"><x-x-input name="name" label="Name" required
                                            placeholder="Enter Name" /></div>
                                    <div class="col-md-6"><x-x-input name="email" type="email" label="Email" required
                                            placeholder="Enter Email" /></div>
                                    <div class="col-md-6"><x-x-input name="password" type="text" label="Password"
                                            required placeholder="Enter Password" /></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_upload_status" class="group">Product Upload Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="product_upload_status">
                                                <option selected value="published">Published</option>
                                                <option value="draft">Draft</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label for="trax_allow" class="fw-bold text-info">Enter Trax Details</label>
                                        <input type="checkbox" id="trax_allow" name="trax_allow" class="ms-2">
                                    </div>
                                    <!-- Trax details -->
                                    <div class="col-md-6"><x-x-input name="trax_api_key" label="Api-Key"
                                            placeholder="Enter Api Key" /></div>
                                    <div class="col-md-6"><x-x-input name="trax_pickup_address_id" label="Pickup-Address-id"
                                            placeholder="Enter Address-Id" /></div>

                                    <div class="d-flex align-items-center">
                                        <label for="postEx_allow" class="fw-bold text-info">Enter Post-Ex Details</label>
                                        <input type="checkbox" id="postEx_allow" name="postEx_allow" class="ms-2">
                                    </div>
                                    <!-- Trax details -->
                                    <div class="col-md-6"><x-x-input name="postEx_apiToken" label="Api-Token"
                                            placeholder="Enter Api Token" /></div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="postEx_pickupAddressCode">
                                            <option selected value="">Select PostEx-Pickup Address</option>
                                            @foreach ($codes as $code)
                                                <option value="{{ $code->postEx_pickupAddressCode }}">
                                                    {{ $code->postEx_pickupAddressCode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <label for="mnp_alllow" class="fw-bold text-info">Enter MNP Details</label>
                                        <input type="checkbox" id="mnp_alllow" name="mnp_alllow" class="ms-2">
                                    </div>
                                    <!-- mnp details -->
                                    <div class="col-md-4"><x-x-input name="mnp_username" label="Mnp Username"
                                            placeholder="Enter UserName" /></div>
                                    <div class="col-md-4"><x-x-input name="mnp_password" label="Mnp Password"
                                            placeholder="Enter Password" /></div>
                                    <div class="col-md-4"><x-x-input name="locationID" label="Mnp LocationId"
                                            placeholder="Enter Location-Id" /></div>



                                    <div class="col-md-6">
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

    <!-- delete record modal -->
    @section('delete_modal_footer')
        <x-x-delete-record deleteRoute="{{ route('inventory.seller.delete') }}" />
    @endsection
    <!-- end Delete Modal -->

    <div class="container">
        <!-- Top Row -->
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-between">
                    <x-x-record-count :total-records="$total_records">
                        <x-slot name="backArrow">
                            <p class="me-1">
                                <a href="{{ route('inventory.index') }}" class="btn btn-sm text-white btn-primary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            </p>
                        </x-slot>
                    </x-x-record-count>
                    <x-x-add-button />
                </div>
            </div>

            <!-- filter Row -->
            <div class="col-12 my-2">
                <form method="GET" action="{{ route('inventory.seller.index') }}" id="search-form">@csrf
                    <div class="row align-items-center justify-content-lg-end">

                        <!-- filter by name -->
                        <x-filter.x-input-search label="Search by Name / Email" name="search_input" class=""
                            placeholder="Name / Email..." :value="request()->get('search_input')"></x-filter.x-input-search>
                        {{-- not for partner --}}
                        @if (request()->query('is_partner') != 1)
                            <div class="col-lg-2 mb-2">
                                <label class="pb-0">Select Admin Role</label>
                                <select name="role" class="form-control me-2">
                                    <option value="" selected>All</option>
                                    <option value="4" @if (request()->get('role') == 4) selected @endif>Warehouse
                                        Team-member</option>
                                    <option value="3" @if (request()->get('role') == 3) selected @endif>Re-Seleers
                                    </option>
                                    <option value="partners" @if (request()->get('role') == 'partners') selected @endif>Partners
                                    </option>
                                    <option value="1" @if (request()->get('role') == 1) selected @endif>
                                        Managers/Super
                                        Admin</option>
                                </select>
                            </div>
                        @endif
                        <!-- filter button -->
                        <div class="col-auto">
                            <a class="btn btn-secondary btn-sm" id="reset-button">
                                <i class="fa-solid fa-arrow-rotate-right"></i>
                            </a>
                            <!-- <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> -->
                        </div>
                    </div>

                </form>
            </div>

        </div>
        <!-- end Top row -->

        <!-- Record Display -->
        <div class="row my-3">
            <div class="col-12">
                <a class="btn btn-primary fw-bold mb-2" href="{{ route('inventory.seller.renew_ssl') }}">Renew All SSL</a>
            </div>

            <div class="col-12">
                @if (count($sellers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover active_table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    @can('show_admins_balance_detail')
                                        <th>Balance</th>
                                    @endcan
                                    <th>Allow Couriers</th>
                                    <th>Status</th>
                                    <th>Product Upload Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sellers as $user)
                                    <tr>
                                        <!-- name email image -->
                                        <td>
                                            <a href="{{ route('inventory.seller.edit', $user->id) }}"
                                                class="btn btn-info">{{ $user->id }}</a><br>
                                            <span
                                                class="badge p-1 mt-1 {{ $user->is_partner === 1 ? 'bg-primary' : ($user->role === 3 ? 'bg-dark' : 'bg-warning') }}">{{ $user->is_partner === 1 ? 'Partner' : ($user->role === 3 ? 'Reseller' : ($user->role === 4 ? 'Warehouse Team-member' : '')) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $user->name }}</span><br>
                                            <span> <i class="fa fa-envelope"></i> {{ $user->email }}</span>
                                            @if ($user->byc_password)
                                                @php
                                                    try {
                                                        $decryptedPassword = Crypt::decryptString($user->byc_password);
                                                    } catch (Exception $e) {
                                                        $decryptedPassword = 'Invalid data';
                                                    }
                                                @endphp
                                                <br>
                                                @can('show_admins_balance_detail')
                                                    <span> <i class="fa-solid fa-lock"></i>
                                                        {{ $decryptedPassword }}</span>
                                                @endcan
                                            @endif
                                            @if ($user->type == 3)
                                                <div>
                                                    <span class="bg-success text-white rounded px-1">App</span>
                                                </div>
                                            @endif
                                            @if ($user->image)
                                                <a href="{{ $user->image }}" target="_blank"><img
                                                        src="{{ $user->image }}" height="50px" width="50px"
                                                        alt="Category Image" class="ms-3 rounded-circle" /></a>
                                            @endif
                                        </td>
                                        <!-- balance -->
                                        @can('show_admins_balance_detail')
                                        <td>
                                            <span
                                                class="border border-info fw-bold p-1 rounded">{{ number_format($user->balance) }}</span>
                                        </td>
                                        @endcan
                                        <td>
                                            @if ($user->trax_allow == 1 || $user->postEx_allow == 1 || $user->mnp_alllow)
                                                <span
                                                    class="{{ $user->trax_allow == 1 ?: 'd-none' }} badge bg-info me-1">Trax</span>
                                                <span
                                                    class="{{ $user->mnp_alllow == 1 ?: 'd-none' }} badge bg-warning me-1">Mnp</span>
                                                <span
                                                    class="{{ $user->postEx_allow == 1 ?: 'd-none' }} badge bg-secondary me-1">PostEx</span>
                                            @else
                                                <span>Not Allowed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="{{ $user->status == 1 ? 'text-success' : 'text-danger' }}">{{ $user->status == 1 ? 'Active' : 'In-active' }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="{{ $user->product_upload_status == 'published' ? 'text-success' : 'text-warning' }}">{{ $user->product_upload_status }}</span>
                                        </td>
                                        <td class="text-center d-flex align-items-center ">

                                            @if ($user->id != 1)
                                                @can('delete_admins')
                                                    <button value="{{ $user->id }}"
                                                        class="deleletRecordIconButton btn-danger btn  btn-sm"><i
                                                        class="fa-solid fa-delete-left"></i></button>
                                                @endcan
                                            @else
                                                <span class="text-danger fw-bold">N/A</span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $sellers->appends(request()->all())->links() !!}
                @else
                    <div class="alert alert-warning text-white">No Inventory-Seller record</div>
                @endif
            </div>
        </div>
        <!-- End Display Records -->
    </div>
</div>
@endsection
