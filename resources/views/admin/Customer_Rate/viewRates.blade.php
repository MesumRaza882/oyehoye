@extends('admin.layouts.app')
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
</style>

<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <!--View All problems-->
                <div class="table-responsive">
                    @if(count($problems)>0)
                    <h4 class="text-center">User Problems <span class="badge bg-primary ms-3">{{count($problems)}}</span>
                    </h4>
                    <table class="table table-bordered table-md-responsive myTable">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>User whatsapp</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <!-- <th>Image</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($problems as $pro)
                            <tr>
                                <td>{{$pro->user_name}}</td>
                                <td>
                                    <a href="https://wa.me/+92{{$pro->whatsapp}}?text=Hi%20Welcome%20to%20WAO" target="_blank" class="text-white btn-success btn btn-sm">
                                        {{$pro->whatsapp}}
                                    </a>
                                </td>
                                <td>{{$pro->comment}}</td>
                                <!-- <td>
                                    @php
                                    $imagePath = 'complaint/' . $pro->image;
                                    @endphp

                                    <a target="_blank" href="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : '#' }}">
                                        @if(file_exists(public_path($imagePath)))
                                        <img src="{{ asset($imagePath) }}" alt="Complaint Image" class="img-fluid" width="100px" height="100px">
                                        @else
                                        No Image
                                        @endif
                                    </a>
                                </td> -->
                                <td>
                                    {{$pro->created_at->format('d')}} {{$pro->created_at->format('M')}} {{$pro->created_at->format('Y')}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $problems->appends(request()->all())->links() !!}
                    @else
                    <div class="alert alert-warning text-white">No Orders Record Available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection