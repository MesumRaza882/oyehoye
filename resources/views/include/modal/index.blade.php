
<div class="modal fade" id="Add" tabindex="-1" aria-labelledby="AddLabel" 
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                @yield('modal_header')
                <button type="button" class="btn-close add_modal_close" data-bs-dismiss="modal" aria-label="Close"></button>
                <button type="button" class="btn-close modal_close d-none"  onclick='window.location.reload()' data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @yield('modal_body')
            </div>
            <div class="modal-footer">
                @yield('modal_footer')
            </div>
        </div>
    </div>
</div>