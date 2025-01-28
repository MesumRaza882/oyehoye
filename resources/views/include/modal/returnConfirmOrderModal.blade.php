<div class="modal fade" id="ConfirmOrderModalRecord" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
     tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalToggleLabel">Confirm Order Return</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

          <div class="modal-body bg-primary text-white">
            <h6 class="">Are You Sure ! this Order Returned
              <i class="fa-solid fa-circle-question"></i>
          </h6>
          </div>
            
          <!--delet form cat data-->
            <div class="modal-footer">
                @yield('returnConfirmOrder_modal_footer')
            </div>
    </div>
  </div>
</div>
