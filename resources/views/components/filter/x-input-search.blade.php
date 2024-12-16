@props(['label' => null])
<div class="col-lg-3 mb-lg-0 mb-2">
    <label class="pe-1 {{ $label ? '' : 'd-none' }}">{{ $label }}</label>
    <input type="search" value="{{ $value }}" class="{{ $class }} form-control" name="{{ $name }}" placeholder="{{ $placeholder }}">
</div>