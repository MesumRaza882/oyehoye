<div>

<div class="modal fade rounded"  data-bs-backdrop="static"  id="videoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{$product_name}}</h5>
        
        <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" ></button>
        
      </div>
      <div class="modal-body">
      <iframe id="video-id" style="width: 100%; height: 394px;" src="{{$product_video}}"
         title="Wao-Collection Video" frameborder="0" 
         allow="" 
         allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('.modal-close').click(function() {
        $('#video-id').attr('src','');
    });
});
</script>
@endsection