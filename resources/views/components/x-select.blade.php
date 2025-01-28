<div class="form-group">
    @if ($label)
        <label for="{{ $name }}">
            {{ $label }}
            @if ($required)
                <span class="fw-bold text-danger fs-5">*</span>
            @endif
        </label>
    @endif
    <select class="form-control @error($name) is-invalid @enderror" name="{{ $name }}" {{ $required ? 'required' : '' }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as  $option)
            <option value="{{ $option->id }}" {{ $option->id == $selected ? 'selected' : '' }}>
                {{ $option->name }}
            </option>
        @endforeach
    </select>
    @error($name)
        <span class="invalid-feedback" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
