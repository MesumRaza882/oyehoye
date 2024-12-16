
<div class="modal fade" id="dispatched-mnp" tabindex="-1" aria-labelledby="UpdateLabel" 
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                @yield('dispatched_mnp_modal_header')
                <button type="button" class="btn-close update_modal_close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @yield('dispatched_mnp_modal_body')
            </div>
            <div class="modal-footer">
                @yield('dispatched_mnp_modal_footer')
            </div>
        </div>
    </div>
</div>