@extends('admin.layouts.app')
@section('content')
  <style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{color: #fff !important;}
  </style>
  <div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      
      @include('admin.layouts.navbar')

      @include('admin.layouts.sidebar')
      <!-- Main Content -->
      <div class="main-content">
        <div class="container">
          <div class="row">
            <div class="col-md-12 mx-auto">
              <div class="table-responsive">
                 <table class="table table-hover table-striped w-100">
                  <thead>
                    <tr>
                      <th>User Name</th>
                      <th>Phone</th>
                      <th>Whatsapp</th>
                      <th>Review</th>
                      <th>Image</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($reviews as $review)
                      <tr>
                        <td><a href="{{$review->user ?  route('singleUser', $review->user->id) : '' }}">{{ $review->cus_name }}</a></td>
                        <td>{{ $review->user ? $review->user->phone : '-' }}</td>
                        <td><a href="https://wa.me/{{ $review->user ? $review->user->whatsapp : '' }}?text=" class="text-white btn-success btn btn-sm" target="_blank">{{ $review->user ? $review->user->whatsapp : '-' }}</a></td>
                        <td>{{ $review->desc }}</td>
                        <td>
                          <a href="{{ $review->attachment }}" target="_blank">
                            <img src="{{ $review->attachment }}" width="100px" height="150px" class="rounded" />
                          </a>
                        </td>
                        <td>{{ Carbon\Carbon::parse($review->created_at)->format('d-m-Y') }}</td>
                        <td>
                          @if($review->status == 0)
                            @php $rc = 'bg-primary' @endphp
                          @elseif($review->status == 1)
                            @php $rc = 'bg-success' @endphp
                          @else
                            @php $rc = 'bg-danger' @endphp
                          @endif
                          <span class="{{ $rc }} px-2 py-1 rounded text-white">{{ \App\Helpers\General::status($review->status) }}</span>
                        </td>
                        <td class="text-center">
                          @if($review->status == 0)
                            @php
                              $can_approve = 1;
                              $can_reject = 1;
                            @endphp
                          @elseif($review->status == 1)
                            @php
                              $can_approve = 0;
                              $can_reject = 1;
                            @endphp
                          @else
                            @php
                              $can_approve = 1;
                              $can_reject = 0;
                            @endphp
                          @endif
                          @if($can_approve == 1)
                            <button class="btn btn-success trigger-js mb-2"
                            data-url="{{ route('admin.review.status.update', 1) }}"
                            data-ask-confirmation="true"
                            data-id="{{$review->id}}"
                            >Click to Approve</button>
                          @endif
                          @if($can_reject == 1)
                            <button class="btn btn-danger trigger-js mb-2"
                            data-url="{{ route('admin.review.status.update', 2) }}"
                            data-ask-confirmation="true"
                            data-id="{{$review->id}}"
                            >Click to Reject</button>
                          @endif
                      </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <div>
                {{ $reviews->links() }}
              </div>
            </div>
          </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection