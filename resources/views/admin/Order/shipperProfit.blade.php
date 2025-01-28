@extends('admin.layouts.app')
@section('title') Orders @endsection
@section('content')

<div class="main-content">

    @include('include.modal.orderHistoryModal')

    <div class="container">
        <!-- filter records -->
        <div class="row">
            <!-- filter -->
            <div class="col-12 mb-2">
                <form method="GET" action="" id="search-form">
                    <div class="row">
                        <!-- select Status -->
                        <div class="col-lg-3 mb-2 col-6">
                            <label class="pb-0">Flter Commission Status</label>
                            <select name="status" class="form-control me-2">
                                <option value="">All</option>
                                <option value="1" @if(request()->get('status') == '1') selected @endif>Paid</option>
                                <option value="0" @if(request()->get('status') == '0') selected @endif>Un-Paid</option>
                            </select>
                        </div>
                        <div class="col-lg-3 mb-2 col-6 pt-4">
                            <a href="{{route('order.shipper.profit')}}" class="btn btn-secondary btn-sm" id="reset-button">
                                <i class="fa-solid fa-arrow-rotate-right"></i>
                            </a>
                            <button class="btn btn-primary btn-sm " type="submit">Filter Order
                                <span class="d-none" id="filterSpecificIds"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- end filter records -->
        <div class="row">

            <!-- Display Records -->
            

            <div class="col-md-8 table-responsive">
              @if(count($records)>0)
              <table class="table table-striped table-hover active_table-" id="examples-">
                  <thead>
                      <tr>
                          <th>Order Id</th>
                          <th>Commission</th>
                          <th>Status</th>
                          <th>Remakrs</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach($records as $record)
                      <tr>
                        <td class="text-right">{{ $record->id }}</td>
                        <td>
                            {{$record->reseller_profit+$record->advance_payment}}
                        </td>
                        <td>
                            @if ($record->is_commission_paid==1)
                                <span class="badge badge-success w-100">Paid</span>
                            @else
                                <span class="badge badge-danger w-100">Un-Paid</span>
                            @endif    
                        </td>
                        <td>
                            @if ($record->is_commission_paid==1)
                                {{$record->commission_paid_note}}
                            @else
                                <input type="text" class="form-control" id="commission_paid_note{{$record->id}}" 
                                value="{{$record->commission_paid_note}}"
                                >    
                            @endif    
                        </td>
                        <td>
                            @if ($record->is_commission_paid==0)
                                <button 
                                    class="btn btn-primary trigger-js-with-remark"
                                    data-url="{{ route('order.shipper.profit.done',[
                                        'id'=>$record->id
                                    ]) }}"
                                    data-remarlele="commission_paid_note{{$record->id}}"
                                    data-ask-confirmation="true"
                                >
                                    Done
                                </button>  
                            @endif
                        </td>
                      </tr>
                      @endforeach
                  </tbody>
              </table>
              {!! $records->appends(request()->all())->links() !!}
              @else
                <div class="alert alert-warning text-white">No Shipper Profit Available</div>
              @endif
          </div>
            <!-- end records display -->
        </div>
        <!-- End Row -->
    </div>
</div>
@endsection
