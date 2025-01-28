@props(['label', 'type' => 'text','value' => '', 'name', 'required' => false, 'placeholder' => null, 'min' => null, 'for' => null,'id' => null,'class' => null,'accept' => null ])

<div class="form-group">
    @if($label)<label for="{{$for}}">{{ $label }}
        @if($required)
        <span class="fw-bold text-danger fs-5">*</span>
        @endif
    </label>
    @endif
    <input type="{{ $type }}" id="{{$id}}" value="{{$value}}" @if($min) min="{{ $min }}" @endif class="form-control @error($name) is-invalid @enderror {{ $class }}" name="{{ $name }}" {{ $required ? 'required' : '' }} placeholder="{{ $placeholder }}" accept="{{ $accept }}">
    @error($name)
    <span class="invalid-feedback" role="alert">
        {{ $message }}
    </span>
    @enderror
</div>
