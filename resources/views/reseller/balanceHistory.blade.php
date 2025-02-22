@extends('admin.layouts.app')
@section('content')
@section('title') Balance History @endsection


<div class="main-content">
    <div class="container">
        <div class="row">
            <!-- Top Row -->
            <div class="row justify-content-between">

                <!-- total record count -->
                <div class="col-auto d-flex align-items-start">
                    <p class="d-block p-1 px-2 bg-primary text-white">Records:<span class="ms-1">{{$recordsCount}}</span></p>
                    <p class="d-block p-1 px-2 ms-2  bg-info text-white">Total Balance:<span class="ms-1">{{number_format($totalBalance)}}</span></p>
                    <p class="d-block p-1 px-2 ms-2  bg-success text-white">Current Balance:<span class="ms-1">{{number_format(auth()->user()->balance)}}</span></p>
                </div>


                <!-- filter -->
                <div class="col-12 mb-3">
                    <form method="GET" action="{{route('waoseller.balance.history')}}" id="search-form">

                        <div class="row align-items-center">
                            <!-- select records -->
                            <div class="col-lg-2 mb-2">
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

                            @if (auth()->user()->role == 1)
                            <!-- admins -->
                            <div class="col-lg-3 mb-2 mb-2 col-6">
                                <label class="pb-0">Select Admin</label>
                                <select name="admin_id" class="form-control me-2">
                                    <option value="">All</option>
                                    @foreach ($admins as $admin)
                                    <option value="{{$admin->id}}" @if(request()->get('admin_id') == $admin->id) selected @endif>{{$admin->email}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- filter by name and city -->
                            <div class="col-lg-2 mb-2">
                                <label class="pb-0">Search</label><input type="search" value="{{request()->get('search_input')}}" class="me-2 form-control" name="search_input" placeholder="Order-Id">
                            </div>

                            <!-- select Status -->
                            <div class="col-lg-3 mb-2">
                                <label class="pe-1">Status</label>
                                <select name="status" class="form-control me-2">
                                    <option value="cancel" @if(request()->get('status') == 'cancel') selected @endif>Cancel Order Balance</option>
                                    <option value="dispatch" @if(request()->get('status') == 'dispatch') selected @endif>Dispatch Order Balance</option>
                                    <option value="add" @if(request()->get('status') == 'add') selected @endif>Add Balance</option>
                                    <option value="deduct" @if(request()->get('status') == 'deduct') selected @endif>Deduct Balance</option>
                                </select>
                            </div>

                            <div class="col-lg-auto col-6 mb-2">
                                <label>From</label><input type="date" value="{{request()->get('fromDate')}}" class="me-2 form-control" name="fromDate" max="<?php echo date("Y-m-d"); ?>">
                            </div>
                            <div class="col-lg-auto col-6 mb-2">
                                <label>To</label><input type="date" value="{{request()->get('toDate')}}" class="form-control" name="toDate" max="<?php echo date("Y-m-d"); ?>">
                            </div>

                            <div class="col-auto  mt-3">
                                <a class="btn btn-secondary btn-sm" id="reset-button">
                                    <i class="fa-solid fa-arrow-rotate-right"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end Top row -->

            <div class="col-md-12 mx-auto">
                @if(count($records)>0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped active_table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            <tr>
                                <td>
                                    <span>{{$record->order_id}}</span>
                                    <br>
                                    <span>{{$record->admin->email}}</span>
                                </td>
                                <td><span class="fw-bold p-2 fs-5 border border-info">{{number_format($record->balance)}}</span></td>
                                <td><span class="badge {{$record->status === 'cancel' || $record->status === 'deduct' ? 'bg-danger' : 'bg-info'}}">{{$record->status === 'dispatch' ? 'Dispatch Order Balance' : ($record->status === 'cancel' ?  'Cancel' : ($record->status === 'add' ? 'Add Balance' : 'Deduct Balance'))}}</span></td>
                                <td>{{$record->note ?? '-'}}</td>
                                <td>{{$record->date}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </form>
                    {!! $records->appends(request()->all())->links() !!}
                </div>
                @else
                <div class="alert alert-warning">No Records Blanace has been added yet!</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
