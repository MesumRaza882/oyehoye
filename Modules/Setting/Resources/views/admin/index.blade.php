@extends('admin.layouts.app')
@section('title') Settings @endsection
@section('content')

<div class="main-content">
  <div class="container">
    <div class="row">
      <div class="col-12 clearfix my-2">
          <div class="float-start pt-1"><h6 class="app-page-title mb-0 d-inline fs-5">Settings</h6></div>
      </div>

      <div class="col-12">
        <div class="bg-white rounded-c px-3 py-3">
          <form action="{{ route('admin.settings.save')}}" method="POST" enctype="multipart/form-data">
            @csrf
            @foreach ($settings as $setting)
              @if ($setting->type=='text')
                <div class="form-group row">
                    <label for="name" class="form-label fw-bold">{{$setting->label}}</label>
                    <div class="w-100">
                      <input type="text" name="{{$setting->attribute}}" value="{{ $setting->value }}" class="form-control">
                    </div>
                </div>
              @elseif($setting->type=='number')
                <div class="form-group row">
                    <label for="name" class="form-label">{{$setting->label}}</label>
                    <div class="w-100">
                      <input type="number" name="{{$setting->attribute}}" value="{{ $setting->value }}" class="form-control">
                    </div>
                </div>
              @elseif($setting->type=='select')
                <div class="form-group row">
                    <label for="name" class="form-label">{{$setting->label}}</label>
                    <div class="w-100">
                      <select name="{{$setting->attribute}}" value="{{ $setting->value }}" class="form-control">
                        @foreach(explode(',', $setting->possible_values) as $value)
                          <option value="{{ $value }}" @if($setting->value == $value) selected @endif>{{ ucwords($value) }}</option>
                        @endforeach
                      </select>
                    </div>
                </div>
              @elseif($setting->type=='textarea')
                <div class="form-group row">
                    <label for="name" class="form-label">{{$setting->label}}</label>
                    <div class="w-100">
                      <textarea name="{{$setting->attribute}}" rows="5" cols="20" class="form-control">
                        {{$setting->value}}
                      </textarea>
                    </div>
                </div>
              @endif
            @endforeach
            <button type="submit" class="btn btn-primary text-white mt-3">Update</button>
          </form> 
        </div>
      </div>
    </div>
  </div>
</div>
@endsection