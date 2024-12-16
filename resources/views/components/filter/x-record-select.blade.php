
<div class="col-auto mb-lg-0 mb-2">
    <label class="pe-1">{{ $label }}</label>
    <select name="{{ $name }}" class="form-control {{ $class }}">
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @if($optionValue == $selected) selected @endif>{{ $optionLabel }}</option>
        @endforeach
    </select>
</div>
