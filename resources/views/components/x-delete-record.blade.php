@props(['deleteRoute'])
<form id="deleteRecordForm" method="POST" data-delete-route="{{ $deleteRoute }}">
    @csrf
    <input hidden  type="text" id="deleteRecordId" name="RecordId">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i></button>
</form>

