@extends('themes.wao.layouts.main')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4">
        <div class="row">
            <div class="col-12 mx-auto" style="margin-top: 100px;">
                <a href="{{ url('/') }}" class="d-flex justify-content-center text-decoration-none">
                    <h3 class="white_text">{{Domain::admin('name')}}</h3>
                    <h4 class="yellow_text" style="padding-top:5px;padding-left:4px">Collection</h4>
                </a>
                <form action="{{route('wao.profile.update')}}" method="POST">
                    @csrf 
                    @if(session('success'))
                        <div class="alert alert-danger text-center">
                            {{ session('success') }}
                        </div>
                    @endif
                    {{-- @method('PUT') --}}
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Name:</label>
                        <input type="text" placeholder="Name" name="name" value="{{$user->name}}" class="form-control">
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Whatsapp:</label>
                        <input type="text" placeholder="Whatsapp" name="courier_phone" value="{{$user->whatsapp}}" class="form-control">
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">City:</label>
                        {{-- <input type="text" placeholder="city" name="city_name" value="@if($user->city) {{ $user->city->name }} @endif" name="city" class="form-control"> --}}
                        <select name="city_id" class="form-control" id="city_id">
                                <option value="">select option</option>
                            @foreach($cities as $item)
                                <option value="{{$item->id}}" @if($item->id == $user->city_id) selected @endif>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn button1 btn-block white_text">Update</button>
                        <a href="{{route('user_logout')}}" class="btn button1 btn-block white_text mt-2">Logout</a>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@section('script')
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#city_id').select2({
        width: '100%'
      });
    });
  </script>
@endsection