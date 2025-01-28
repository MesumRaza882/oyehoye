@extends('admin.layouts.app')
@section('content')

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
                <div class="col-md-10 mx-auto">
                    <section class="section">
                        <div class="section-body">
                            <div class="card">
                                @if (Session::get('success'))
                                    <div class="alert alert-success text-center text-white">
                                        {{Session::get('success')}}
                                    </div>
                                @endif
                                @if (Session::get('fail'))
                                    <div class="alert alert-danger text-center text-white">
                                        {{Session::get('fail')}}
                                    </div>
                                @endif
                                <form method="POST" enctype="multipart/form-data" action="{{route('addcharge')}}">
                                    @csrf
                                    <div class="card-header">
                                    <h4>Add Charges</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Suit</label>
                                                    <input type="number" placeholder="Add Suit" class="form-control" name="suit" required="">
                                                    <span class="text-danger">@error('suit'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Charges</label>
                                                    <input type="number" placeholder="Add Delivery Charges" class="form-control" name="charges" required="">
                                                    <span class="text-danger">@error('charges'){{$message}}@enderror</span>
                                                </div>
                                            </div>
                                           
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row my-3">
                <div class="col-sm-12">
                    <h3 class="text-center">All Charges</h3>
                    @if(count($charges)>0)
                    <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Suits</th>
                                    <th>Charges</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($charges as $rate)
                                <tr>
                                    <td>{{$rate->suit}}</td>
                                    <td>Rs {{$rate->charges}}</td>
                                    <td>
                                         <a href="{{route('delcharge',$rate->id)}}" onclick="return confirm('Are you sure to delete Charge?');" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-warning text-white">No Charges</div>
                        @endif
                </div>
            </div>
        </div>
      </div>
      @include('admin.layouts.footer')
    </div>
  </div>
@endsection