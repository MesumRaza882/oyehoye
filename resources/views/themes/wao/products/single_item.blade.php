@extends('themes.wao.layouts.main')
<style>
html {
  scroll-snap-type: y mandatory;
}
</style>
@section('content')
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12 col-md-5 col-lg-4">
        <div class="row product-container">
          @include('themes.wao.products.partials.video-card', ['products' => $products, 'plusIndex' => $plusIndex, 'layout' => $layout])
        </div>
        <div class="text-center my-2">
            <div class="spinner-border text-white"></div>
        </div>
        
        <div style="position:fixed;bottom:0px;margin-top: 10px;left:0px;right:0px;z-index:9999">
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 col-lg-4" style="background-color:#000">
                    <div class="product_place_order px-2 py-2 d-flex justify-content-between">
                        <a href="{{ url('menu') }}">
                            <i class="fa-solid fa-chevron-left pt-2 product-footer-icons"></i>
                        </a>
                        <button class="btn product-footer-placeorder-button text-color" style="width: 80%;" onclick="add_cart()">
                            Place Order Rs. <span id="priceCount">0</span>
                            <span id="count2">0</span>
                        </button>
                        
                        <a href="javascript:void(0);" class="white_text" onclick="change_view()">
                            <i class="fa-solid fa-box pt-2 product-footer-icons" id="convertButton"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>


      </div>
    </div>
  </div>
  
	<div>
		<form action="{{route('product.addToCart')}}" method="post" id="cartForm">
			@csrf
			<div id="cartFormDiv"></div>
		</form>
	</div>
@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

		document.addEventListener('DOMContentLoaded', function() {
        $('#loader').hide();
    });

  	var product_current_status='large-cards';
		layout = 1;
		
		function pauseV(){
			//stop all videos
			$cvideo = $('video');
			$cvideo.trigger("pause");
			// $cvideo.prop("muted", true);
			// show paly btn
			$('.play-button').css("display", "block");
			// hide pause btn
			$('.pause-button').css("display", "none");
		}

    document.addEventListener("DOMContentLoaded", function() {
			// var playButtons = document.querySelectorAll('.play-button');
			// var pauseButtons = document.querySelectorAll('.pause-button');
			// var videos = document.querySelectorAll('.video');
			// var overlays = document.querySelectorAll('.video-overlay');
			// var currentVideo = null;

			// function playVideo(video, playButton, pauseButton, overlay) {
			// 	console.log(currentVideo);
			// 	if (currentVideo && currentVideo !== video) {
			// 		currentVideo.pause();
			// 		var index = Array.from(videos).indexOf(currentVideo);
			// 		pauseButtons[index].style.display = 'none'; // Hide pause button of the previous video
			// 		playButtons[index].style.display = 'inline-block'; // Show play button of the previous video
			// 		overlays[index].style.display = 'none'; // Hide overlay of the previous video
			// 	}
			// 	video.play();
			// 	video.muted = false;
			// 	playButton.style.display = 'none';
			// 	pauseButton.style.display = 'inline-block';
			// 	overlay.style.display = 'flex'; // Show overlay with loader
			// 	currentVideo = video;
			// }

			// function pauseVideo(video, playButton, pauseButton, overlay) {
			// 	video.pause();
			// 	playButton.style.display = 'inline-block';
			// 	pauseButton.style.display = 'none';
			// 	overlay.style.display = 'none'; // Hide overlay when paused
			// }

			// Hide all pause buttons and overlays initially
			// pauseButtons.forEach(function(button) {
			// 	button.style.display = 'none';
			// });
			// overlays.forEach(function(overlay) {
			// 	overlay.style.display = 'none';
			// });

			// playButtons.forEach(function(button, index) {
			// 	button.addEventListener('click', function() {
			// 		var video = videos[index];
			// 		var pauseButton = pauseButtons[index];
			// 		var overlay = overlays[index];
			// 		playVideo(video, button, pauseButton, overlay);
			// 	});
			// });
			

			// play video
			$(document).on("click", ".play-button", function() {
				$t = $(this).closest('.video-card');

				pauseV();

				// play current video
				$video = $t.find('video');
				$soruce = $video.find('source');
				$soruce.attr('src', $soruce.attr('data-src'));
				$video[0].load();
				$video.currentTime = 0;
				$video.trigger("play");

				if(domain_setting.mute_video == "0"){
					$video.prop("muted", false);
				}
				
				$video.get(0).play();
				// hide paly btn
				$('.play-button').css("display", "none");
				// show pause btn
				$t.find('.pause-button').css("display", "block");
    	});

		$(document).on("click", ".pause-button", function() {
			pauseV();
    	});


		});

		document.addEventListener("DOMContentLoaded", function() {

			$(document).on("click", ".done-button", function() {
				$t = $(this).closest('.video-card');
				// const doneButton = $(event.target);
				// const counter = doneButton.parent().find('.done-button-counter');

				// Increment count
				const countElement = $t.find('#qtyInCart');
				let count = parseInt(countElement.val()) + 1;
				countElement.val(count);

				$t.find('.done-button-counter .count').text(count);

				// $t.find('.done-button').hide();
				// $t.find('.done-button-counter').css('display', 'inline-block');

				$t.find('.done-button-counter').addClass('d-flex');
				$t.find('.done-button').addClass('d-none');
				
				updatePriceCount($t);
			});

			$(document).on("click", ".plusBtn", function() {
				$t = $(this).closest('.video-card');

				// Increment count
				// const countElement = $t.find('.count');
				const countElement = $t.find('#qtyInCart');
				const qtyInStock = $t.find('#qtyInStock');

				if(parseInt(qtyInStock.val()) > parseInt(countElement.val())){
					
					let count = parseInt(countElement.val()) + 1;
					countElement.val(count);

					$t.find('.done-button-counter .count').text(count);

					updatePriceCount($t);
				}
			});

			$(document).on("click", ".minusBtn", function() {
				$t = $(this).closest('.video-card');

				// Decrement count
				// const countElement = $t.find('.count');
				const countElement = $t.find('#qtyInCart');
				let count = parseInt(countElement.val()) - 1;
				if(count <= 0){
					count = 0;
					$t.find('.done-button-counter').removeClass('d-flex');
					$t.find('.done-button').removeClass('d-none');
				}
				countElement.val(count);

				$t.find('.done-button-counter .count').text(count);

				updatePriceCount($t);
			});



			// Function to update price count
			function updatePriceCount($t) {
				totalPrice = 0;
				totalQty = 0;
				$('.video-card').each(function() {
					const $t = $(this);
					const price = parseFloat($t.find('#price').val());
					const qty = parseInt($t.find('#qtyInCart').val());
					console.log(price);
					console.log(qty);

					if(qty > 0){
						totalPrice += price * qty;
						totalQty += qty;
					}
				});
			
				$('#priceCount').text(totalPrice);
				$('#count2').text(totalQty);

			}
		});

		// Wait for the DOM to be fully loaded
		document.addEventListener("DOMContentLoaded", function() {
			// Get the button element
			var refreshButton = document.getElementById("refreshButton");
			// Attach click event handler
			refreshButton.addEventListener("click", function() {
				// Reload the current page
				location.reload();
			});
		});

	let offset = 9;
	var canLoad = true;
	$(document).ready(function(){
		$(window).scroll(function(){
			// on scroll stop curren video
			pauseV();

			// console.log($(window).scrollTop());
			if(layout == 1){
				videCardHeight = 2 * $(".video-card").first().height();
			}else{
				videCardHeight = 200;
			}

			if($(window).scrollTop() + videCardHeight >= $(document).height() - $(window).height()){
				console.log(canLoad);
				if (canLoad == true) {
					console.log("Reached bottom of page");
					canLoad = false;
					loadMore();
				}
			}
		});
	});

	function loadMore(){
		console.log("Reached bottom of " + offset);
		offset += 9;

		console.log("Reached bottom of page 2");
		$.ajax({
			url: "{{ route('product.loadMoreProducts') }}?offset="+offset+"&layout="+layout,
			type: 'GET',
			dataType: 'json',
			success: function(response){
				$(".product-container").append(response.html);
				canLoad = true;
		
			},
			error: function(xhr, status, error) {
				console.error(xhr.responseText);
			}
		});
	}

	function add_cart(){
		const allCountBtn = $('.large-cards');
		var formRow = '';
		var isEnableFormStatus = false;
		allCountBtn.each(function(index, element) {
			var $t = $(this);
			countBtn = $t.find('.count');
			var qty = parseInt(countBtn.text());
			console.log(qty);
			if(qty > 0) {
				isEnableFormStatus = true;
				var product_id = countBtn.attr('data-productid');
				formRow += `<input type="hidden" name="product_id[]" value="${product_id}">
				<input type="hidden" name="qty[]" value="${qty}">`;
			}
		});

		var total = parseFloat($('#priceCount').html() || 0);
		formRow += `<input type="hidden" name="total_amount" value="${total}">`;
		$('#cartFormDiv').empty().append(formRow);

		if (isEnableFormStatus) {
			$('#cartForm').submit();
			$('#loader').show();
		}
	}
  

	function change_view(){
		if(layout == 1){
			layout = 2;
			$('.video-card').addClass('col-4');
			$('.video-card').removeClass('col-12');
		}else{
			layout = 1;
			$('.video-card').addClass('col-12');
			$('.video-card').removeClass('col-4');
		}
		
		$('.large-cards').toggleClass('d-none');
		$('.medium-cards').toggleClass('d-none');
	}

</script>
@endsection