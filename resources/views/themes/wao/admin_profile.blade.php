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
                {{-- <form action="{{route('wao.profile.update')}}" method="POST">
                    @csrf  --}}
                    @if(session('success'))
                        <div class="alert alert-danger text-center">
                            {{ session('success') }}
                        </div>
                    @endif
                    {{-- @method('PUT') --}}
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Name:</label>
                        <input type="text" placeholder="Name" name="name" value="{{$user->name}}" class="form-control" disabled>
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Email:</label>
                        <input type="text" placeholder="Name" name="name" value="{{$user->email}}" class="form-control" disabled>
                    </div>
                    <div class="d-grid">
                        <a href="{{route('web_logout')}}" class="btn button1 btn-block white_text">Logout</a>
                    </div>
                {{-- </form> --}}
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