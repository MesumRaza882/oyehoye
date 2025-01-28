@extends('admin.layouts.app')
@section('content')
@section('title')
    Users
@endsection

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">

                <!-- filter records -->
                <div class="row justify-content-between">
                    <!-- total record count -->
                    <div class="col-auto">
                        <p class="d-block p-2 bg-primary text-white">Users:<span class="ms-1">{{ $total_users }}</span>
                        </p>
                    </div>
                    <!-- filter -->
                    <div class="col-12 mb-3">
                        <form method="GET" action="{{ route('waoseller.getUsers') }}" id="search-form">

                            <div class="row justify-content-between">
                                <!-- select records -->
                                <div class="col-lg-2 mb-2">
                                    <label class="pe-1">Select Records</label>
                                    <select name="records" class="form-control me-2" required>
                                        <option value="50" @if (request()->get('records') == 50) selected @endif>50
                                        </option>
                                        <option value="250" @if (request()->get('records') == 250) selected @endif>250
                                        </option>
                                        <option value="500" @if (request()->get('records') == 500) selected @endif>500
                                        </option>
                                        <option value="1000" @if (request()->get('records') == 1000) selected @endif>1000
                                        </option>
                                        <option value="2000" @if (request()->get('records') == 2000) selected @endif>2000
                                        </option>
                                        <option value="3000" @if (request()->get('records') == 3000) selected @endif>3000
                                        </option>
                                        <option value="4000" @if (request()->get('records') == 4000) selected @endif>4000
                                        </option>
                                        <option value="5000" @if (request()->get('records') == 5000) selected @endif>5000
                                        </option>
                                        <option value="7500" @if (request()->get('records') == 7500) selected @endif>7500
                                        </option>
                                        <option value="10000" @if (request()->get('records') == 10000) selected @endif>10000
                                        </option>
                                    </select>
                                </div>

                                <!-- select Status -->
                                <div class="col-lg-3 mb-2">
                                    <label class="pe-1">Select Status</label>
                                    <select name="status" class="form-control me-2">
                                        <option value="">All</option>
                                        <option value="1" @if (request()->get('status') == 1) selected @endif>Active
                                        </option>
                                        <option value="2" @if (request()->get('status') == 2) selected @endif>Block
                                        </option>
                                    </select>
                                </div>

                                <!-- select Status -->
                                <div class="col-lg-3 mb-2">
                                    <label class="pe-1">Select User Type</label>
                                    <select name="user_type" class="form-control me-2">
                                        <option value="">All</option>
                                        <option value="1" @if (request()->get('user_type') == 1) selected @endif>
                                            Customers</option>
                                    </select>
                                </div>

                                <!-- filter by city -->
                                <div class="col-lg-3 mb-2">
                                    <label class="pe-1">Select City</label>
                                    <select name="city" class="form-control me-2">
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                @if (request()->get('city') == $city->id) selected @endif>{{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- filter by name and whatsapp -->
                                <div class="col-lg-4 mb-2">
                                    <label class="pe-1">Search</label><input type="search"
                                        value="{{ request()->get('search_input') }}" class="me-2 form-control"
                                        name="search_input" placeholder="Name & Whatsapp">
                                </div>

                                <!-- by date -->
                                <div class="col-lg-4 mb-2">
                                    <label>From</label><input type="date" value="{{ request()->get('fromDate') }}"
                                        class="me-2 form-control" name="fromDate" max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-lg-4 mb-2">
                                    <label>To</label><input type="date" value="{{ request()->get('toDate') }}"
                                        class="form-control" name="toDate" max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-auto my-1 mt-2 ms-auto">
                                    <a class="btn btn-secondary btn-sm" id="reset-button">
                                        <i class="fa-solid fa-arrow-rotate-right"></i>
                                    </a>
                                    <!-- Button to export VCF -->
                                    <button type="submit" name="vcf" value="1"
                                        class="mx-2 btn btn-sm btn-success">
                                        Filtered VCF
                                    </button>
                                    <button class="btn btn-primary" type="submit">Filter Users</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- end filter records -->

                <!--View All Reviews-->
                <div class="table-responsive">
                    @if (count($users) > 0)
                        <table class="table table-hover table-striped active_table" id="examples">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>whatsapp</th>
                                    <th>Status</th>
                                    {{-- <th>type</th>
                                <th>Real-Order</th> --}}
                                    <th>Register</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr class="{{ $user->is_active_row == 1 ? 'highlight' : '' }}">
                                        <td>{{ $user->id }}</td>
                                        <td class="clearfix">
                                            <span class="float-left">{{ $user->name }}</span>
                                            @if ($user->is_reseller)
                                                <span class="float-right badge badge-primary px-1 py-0">Reseller</span>
                                            @else
                                                <span class="float-right badge badge-success px-1 py-0">Customer</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold {{ $user->city ? '' : ($user->city_name ? '' : 'text-warning') }}">
                                                {{ $user->city ? $user->city->name : ($user->city_name ? $user->city_name : 'Not Selected') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/+92{{ $user->whatsapp }}?text=Hi%20Welcome%20to%20WAO"
                                                target="_blank" class="text-white btn-success btn btn-sm">
                                                {{ $user->whatsapp }}
                                            </a>
                                        </td>
                                        <td>
                                            {!! $user->status > 0
                                                ? 'Block<i class="text-danger fw-bold ms-2 fa-solid fa-ban"></i>'
                                                : 'Active<i class="text-success fw-bold ms-2 fa-solid fa-check"></i>' !!}
                                        </td>
                                        {{-- <td>{!! $user->real_orders > 0 ? '<span class="bg-primary badge">Customer</span>' : 'User' !!}</td>
                                    <td>{{ $user->real_orders > 0 ? $user->real_orders : '0' }}</td> --}}
                                        <td>
                                            <span class="fw-bold">{{ $user->created_at->format('d') }}</span>
                                            <span class="mos">{{ $user->created_at->format('M') }}</span>
                                            <span class="yr">{{ $user->created_at->format('Y') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('singleUser', $user->id) }}"
                                                class="btn btn-info btn-sm text-white">Detail</a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $users->appends(request()->all())->links() !!}
                    @else
                        <div class="alert alert-warning text-white">No record</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
