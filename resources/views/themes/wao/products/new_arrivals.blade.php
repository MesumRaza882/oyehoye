@php
	$load_product_per_page = \Cache::rememberForever('load_product_website', function () {
		return \DB::table('settings')->select('attribute', 'value')->where('attribute', 'load_product_website')->first()->value;
	});
@endphp
@extends('themes.wao.layouts.main')
@section('style')
<style>
body, html{
	height:calc(100% - 10px)!important
}
.scrooling {
  scroll-snap-type: y mandatory;
	height:calc(100% - 10px)!important
}
.h-100{
	height:calc(100% - 10px)!important
}
.track-line {
	flex-grow: 1;
	height: 5px;
	background: #666;
	cursor: pointer;
	position: relative;
}

.progress {
	position: absolute;
	top: 0;
	left: 0;
	height: 100%;
	background: {{ Domain::admin('color_2') }};
	width: 0;
}
</style>
<style>
 /* Hide video controls */
 video::-webkit-media-controls-volume-slider {
            display: none !important;
        }
        /* For Firefox */
        video::-moz-media-controls-volume-slider {
            display: none !important;
        }
</style>
@endsection
@section('content')
	<div class="container-fluid h-100">
		<div class="row justify-content-center h-100">
			<div class="col-12 col-md-5 col-lg-4 scree_bg position-relative h-100">
				{{-- <div class="position-absolute" style="margin-bottom:90px;bottom:0px;left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%);z-index:9">
					<button class=" btn btn-primary shadow-none rounded loadmorebtn" onclick="loadMore()">Load More</button>
				</div> --}}

				<!-- Top menu -->
				@if(count($products) != 0)
				<div id="topMenuFixDiv" style="position:fixed;bottom:120px;right:10px;z-index:9">
					<div class="text-end">
						<!-- profit -->
						@if (auth()->guard('admin')->check() && request()->showUpdate == 1)
							<div class="my-1 icon d-inline-block">
								<a href="{{route('web.profit')}}" class="btn btn-video-icon-secondary text-decoration-none text-center">
									<p class="p-0 m-0 mt-1"><i class="fa-solid fa-money-bill" style="font-size: 20px;"></i></p>
									Profit
								</a>
							</div>
						@endif
						<br />
						<!-- save -->
						<div class="my-1 icon d-inline-block">
							<span href="#" class="btn btn-video-icon-secondary text-decoration-none text-center moreModalShow">
								<p class="p-0 m-0 mt-1"><i class="fa-solid fa-download" style="font-size: 20px;"></i></p>
								Save
							</span>
						</div>
						<br />
						<!-- mute -->
						{{-- @if(Domain::admin('mute_video') == 2)
							<div class="my-1 icon d-inline-block">
								<span href="" class="btn btn-video-icon-secondary text-center mute-unmute">
									<p class="p-0 m-0 mt-1">
										<i class="fa-solid fa-volume-high slash" style="font-size: 20px;"></i>
									</p>
									<span>Muted</span>
								</span>
							</div>
							<br />
						@endif --}}
						<!-- play pause -->
						<div class="my-1 icon d-inline-block" style="position: relative;">
							<span class="btn btn-video-icon-secondary text-center play-button cursor-pointer showModalVideo">
								<p class="p-0 m-0 mt-1"><i class="fa-solid fa-play" style="font-size: 20px;"></i></p>
								Play
							</span>
							<span class="btn btn-video-icon-secondary text-center pause-button cursor-pointer" style="display: none;">
							<p class="p-0 m-0 mt-1"><i class="fa-solid fa-pause" style="font-size: 20px;"></i></p>
							Pause
							</span>
							<div class="position-absolute" style="bottom: 1px;">
							<span class="d-block- d-none play-loader"></span>
							</div>
						</div>
						<br />
						<!-- layout -->
						{{-- <div class="my-1 icon d-inline-block">
							<span href="#" onclick="change_view()" class="btn btn-video-icon-secondary text-decoration-none text-center">
								<p class="p-0 m-0 mt-1"><i class="fa-solid fa-box" style="font-size: 20px;"></i></p>
								Layout
							</span>
						</div>
						<br /> --}}
						<!-- menu -->
						<div class="my-1 icon d-inline-block">
							<a href="{{ url('menu') }}" class="btn btn-video-icon-secondary text-decoration-none text-center">
							<p class="p-0 m-0 mt-1"><i class="fa-solid fa-box" style="font-size: 20px;"></i></p>
							Menu
							</a>
						</div><br>
					</div>
				</div>
				@endif

				<div style="position:fixed;bottom:80px;right:10px;z-index:999">
					<button class="btn button1 price text-color mt-2">Rs. <span id="top_price" class="price0"></span></button>
				</div>
				
				<a href="javascript:void(0);" class="btn-video-icon-secondary card-layout-change-btn d-none" onclick="change_view()"  style="position: fixed;right: 3%; bottom: 125px;border-radius: 50%; height: 53px; width: 53px; padding-left:16px; padding-top: 7px; z-index:9999;">
					<i class="fa-solid fa-box pt-2 product-footer-icons" id="convertButton"></i>
				</a>

				{{-- name --}}
				<div class="row justify-content-center" id="large_product_title">
					<div class="col-12 col-md-5 col-lg-4">
						<div class="top_header_section" style="position: fixed;z-index: 99999;">
							<h4 class="product-name" id="productName"></h4>
							<div class="refresh_play_buttons tetx-center">
								<a href="{{ route('product.NewArrivals') }}">
									<i class="fa-solid fa-arrows-rotate refresh_icon" id="refreshButton"></i>
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="row product-container h-100">
					@if(count($products) != 0)
						@include('themes.wao.products.partials.video-card', ['products' => $products, 'plusIndex' => $plusIndex, 'layout' => $layout])
					@else
						<div class="text-center text-color" style="font-size: 24px; top:50%; left:50%; transform: translate(-50%, -50%); position:fixed">
							<span class="fs-34px">{{Domain::admin('name')}} </span>
							<br />
							<br />
							<br />
							Product Not Found
							<br />
							<br />
							<a href="{{ url('menu') }}" class="btn btn-video-icon-secondary text-decoration-none text-center">
								<p class="p-0 m-0 mt-1"><i class="fa-solid fa-box" style="font-size: 20px;"></i></p>
								Menu
							</a>
						</div>
					@endif
				</div>

				@if(count($products) != 0)
					<div class="text-center my-2">
						<div class="spinner-border text-white"></div>
					</div>
				@endif
				
				<div id="placeOrderFixDiv" style="position:fixed;bottom:0px;margin-top: 10px;left:0px;right:0px;z-index:9999">
					<div class="row justify-content-center">
						<div class="col-12 col-md-5 col-lg-4" style="background-color:#000">
							<div class="product_place_order px-2 py-2 d-flex justify-content-between">
								<a href="{{route('orders')}}" class="me-2">
									<img src="{{asset('themes/wao/images/orders.png')}}" class="orders-icon-secondary p-1">
								</a>
								<button class="btn product-footer-placeorder-button text-color" style="width: 80%;" onclick="add_cart()">
									Place Order Rs. <span id="priceCount">0</span>
									<span id="count2">0</span>
								</button>
								<a href="https://wa.me/{{Domain::admin('whatsapp_number')}}" target="_blank" class="mx-2">
									<img src="{{asset('themes/wao/images/whatsapp.png')}}" alt="WhatsApp" class="whatsapp-icon-secondary">
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

@section('modal')
<div class="modal fade black up" id="moreModal" tabindex="-1" aria-labelledby="moreModal" aria-hidden="true">
  <div class="modal-dialog mb-0 bottom">
    <div class="modal-content">
      <div class="modal-body p-0">
			<div class="mt-3 me-3 clearfix">
				<i class="fa fa-times text-white float-end closeModal" data-bs-dismiss="modal"></i>
			</div>
			<a href="#" class="clearfix p-3 text-white d-block" id="save-thumbnail">
				<div class="float-start">
					Save Picture
				</div>
				<div class="float-end">
					<i class="fa-solid fa-download"></i>
				</div>
			</a>
			<hr class="my-0 bg-white opacity-1" />
			<a href="#" class="clearfix p-3 text-white d-block" id="save-video">
				<div class="float-start">
					Download Video
				</div>
				<div class="float-end">
					<i class="fa-solid fa-download"></i>
				</div>
			</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade black up" id="updateSellerProfitModal" tabindex="-1" aria-labelledby="updateSellerProfitModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog mb-0 bottom">
    <div class="modal-content">
      <div class="modal-body">
        <form id="updateSellerProfitForm" method="post">
          @csrf
          <input type="hidden" name="product_id" id="product_id">
            <div class="row">
                <div class="col-12 clearfix">
                  <h6 class="yellow_text float-start">Update Seller Profit</h6>
                  <i class="fa fa-times text-white float-end closeModal" data-bs-dismiss="modal"></i>
                </div>

                <div class="col-12">
                  <div class="form-group py-2">
                    <label for="" class="white_text py-1">Price:</label>
                    <input type="number" placeholder="Price" name="price" id="product_price" class="form-control" readonly required>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group py-2">
                    <label for="" class="white_text py-1">Seller Profit:</label>
                    <input type="number" placeholder="Seller Profit" name="seller_profit" id="seller_profit" class="form-control" onkeyup="calculate_after_profit()">
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group py-2">
                    <label for="" class="white_text py-1">Price After Profit:</label>
                    <input type="number" placeholder="Price After Profit" name="price_after_profit" id="price_after_profit" class="form-control" readonly>
                  </div>
                </div>
                <div class="col-12">
                  <div class="pt-3">
                    <button type="button" class="btn button1 white_text w-100" id="sellerProfitUpdateBtn">Update</button>
                  </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- play video --}}
<div class="modal fade black up" id="videoModal" tabindex="-1" aria-labelledby="videoModal" aria-hidden="true" style="z-index:99999">
  <div class="modal-dialog bg-white modal-dialog-centered m-0 vp-height" style="height:calc(100% - 280px)">
    <div class="modal-content border-0 vp-height" style="height:calc(100% - 280px)">
      <div class="modal-body p-0 position-relative">
				<div class="position-absolute" style="z-index:9999999; margin-top:70px; margin-left:10px;">
					<div class="bg-dark rounded p-2">
						<i class="fa text-white fa-arrow-left closeVideoModal"></i>
					</div>
				</div>
        <video id="myVideo" class="vp-height" muted autoplay style="width:100%; height:calc(100% - 280px); object-fit: cover;">
          <source src="" type="video/mp4">
          Your browser does not support the video tag.
        </video>
        <div class="custom-controls">
            <div class="track-line">
                <div class="progress"></div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
<script>

			document.addEventListener('DOMContentLoaded', function() {
				var video = document.getElementById('myVideo');
				var progress = document.querySelector('.progress');
				var trackLine = document.querySelector('.track-line');

				video.addEventListener('timeupdate', function() {
						var value = (video.currentTime / video.duration) * 100;
						progress.style.width = value + '%';
				});

				trackLine.addEventListener('click', function(event) {
						var rect = trackLine.getBoundingClientRect();
						var offsetX = event.clientX - rect.left;
						var percentage = offsetX / trackLine.offsetWidth;
						video.currentTime = percentage * video.duration;
				});
		});

		document.addEventListener('DOMContentLoaded', function() {
			$('#loader').hide();
		});

		var videoData = null;
  	var product_current_status='large-cards';
		layout = 1;
		var height = $(window).height();
		let offset = parseInt("{{ $load_product_per_page }}");
		var canLoad = true;
		

		function checkVisibility() {
			$('.video-card').each(function() {
				var $video = $(this);
				var rect = $video[0].getBoundingClientRect();
				// var isVisible = rect.top >= 0 && rect.bottom <= $(window).height();
				var isVisible = rect.bottom <= $(window).height();

				if (isVisible) {
					
					videoData = $video.data('video');

					var $modal = $('#videoModal');
					var $source = $modal.find('source');
					var $video = $modal.find('video');
					
					if ($source.length && $video.length) {
						$source.attr('src', videoData.video);

						$video[0].load();
					} else {
						console.error('No <source> or <video> element found in the modal.');
					}

					$('#productName').text(videoData.name);

					var price = parseFloat(videoData.price);
					var resellerProductProfit = parseFloat(videoData.reseller_product_profit);
					if (!isNaN(price) && !isNaN(resellerProductProfit)) {
						var total = price + resellerProductProfit;
						$("#top_price").text(total.toFixed(0));
					} else {
						$("#top_price").text(price);							
					}
				}
			});
		}

		function muteUnmute(){
			if($('.mute-unmute').find('.fa-volume-high').hasClass('slash')){
				$('.mute-unmute').find('.fa-volume-high').removeClass('slash');
				$('.mute-unmute').find('span').text('Mute');
				$('video').prop("muted", false);
			}else{
				$('.mute-unmute').find('.fa-volume-high').addClass('slash');
				$('.mute-unmute').find('span').text('Muted');
				$('video').prop("muted", true);
			}
		}

		// Function to play the next video when it comes into view
		function playNextVideoWhenVisible(entries) {
			entries.forEach(entry => {

				entry.target.intersecting = entry.isIntersecting;
				if (entry.isIntersecting) {
					// Play the video that is currently in view
					var video = $(entry.target)[0];
					// console.log(video.id);
					$('#productName').html($('#'+video.id).data('productname'));
					$('#top_price').html($('#'+video.id).data('productprice'));
					// video.play();
					// console.log('s');
					// $(entry.target).closest('.video-card:visible').find(".play-button").click();
					$(".play-button").click();
				} else {
					// Pause the video that is not in view
					var video = $(entry.target)[0];
					video.pause();
				}
			});
		}
		
		function loadMoreIntersecting(entries) {
			entries.forEach(entry => {
				entry.target.intersecting = entry.isIntersecting;
			});
		}

		// on every ajax bind video config
		function bindVideoEventListeners() {
			$('.video').each(function(index) {
				var video = $(this)[0];
				var $t = $(this).closest('.video-card');

				video.addEventListener('waiting', function() {
					console.log('waiting');
					$('.play-loader').removeClass('d-none');
					$t.find('.play-loader').removeClass('d-none');
					// $t.find('.product_footer_section').addClass('d-none');
					$('.play-button').show();
					$('.pause-button').hide();
				});
				
				video.addEventListener('canplaythrough', function() {
					console.log('canplaythrough');
					
					$('.play-loader').addClass('d-none');
					$t.find('.play-loader').addClass('d-none');
					$t.find('.product_footer_section').removeClass('d-none');
					$('.play-button').hide();
					$('.pause-button').show();
				});
			});

			// Create an Intersection Observer
			var observer = new IntersectionObserver(playNextVideoWhenVisible, {threshold: 0.5});

			// // Observe each video element
			$('video').each(function() {
				observer.observe(this);
			});
		}

		function pauseV(){
			//stop all videos
			$cvideo = $('video');
			$cvideo.trigger("pause");
			// $cvideo.prop("muted", true);
			// show paly btn
			// $('.play-button').css("display", "block");
			// hide pause btn
			// $('.pause-button').css("display", "none");
		}


		$(document).ready(function(){
			checkVisibility();
		});

    document.addEventListener("DOMContentLoaded", function() {

			// bindVideoEventListeners();

			// mute unmute video
			$(document).on("click", ".mute-unmute", function() {
				muteUnmute();
			});

			// play video
			$(document).on("click", ".play-button", function() {
				
				// pauseV();

				// $video = null;
				// var videos = document.querySelectorAll('video');

				// // Find the video currently in the viewport
				// videos.forEach(function(singlevideo) {
				// 	// if (isElementInViewport(singlevideo)) {
				// 	if (singlevideo.intersecting > 0) {
				// 		$video = $('#'+singlevideo.id);
				// 	}
				// });
				// // console.log($video);
				// $soruce = $video.find('source');
				// $soruce.attr('src', $soruce.attr('data-src'));
				// $video[0].load();
				// $video.currentTime = 0;
				// // $video.trigger("play");
				

				// if(domain_setting.mute_video == "2"){
				// 	// $video.prop("muted", false);
				// 	// muteUnmute();
				// }
				
				// $video.get(0).play();
				
				// // hide paly btn
				// // $('.play-button').css("display", "none");
				// // show pause btn
				// // $t.find('.pause-button').css("display", "block");
				// $('.play-button').css("display", "none");
				// $('.pause-button').css("display", "block");
    	});

			// pause video
			$(document).on("click", ".pause-button", function() {
				
				$('.play-button').css("display", "block");
				// hide pause btn
				$('.pause-button').css("display", "none");
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

		$(document).ready(function(){
			$('.scrooling').scroll(function(){
				checkVisibility();

				// console.log($(window).scrollTop());
				if(layout == 1){
					videCardHeight = 2 * $(".video-card").first().height();
					canLoad = true;
				}else{
					videCardHeight = 200;
				}

				var scrollTop = $(window).scrollTop();
				var windowHeight = $(window).height();
				var documentHeight = $(document).height();
				
				// Select the second-to-last item
				var $secondLastItem = $('.video-card').eq(-2);
				
				if ($secondLastItem.length) {
					var secondLastItemOffsetTop = $secondLastItem.offset().top;
					var secondLastItemHeight = $secondLastItem.outerHeight();

					if (scrollTop + windowHeight >= secondLastItemOffsetTop + secondLastItemHeight) {
						if (canLoad == true) {
							console.log("Reached bottom of page 1");
							canLoad = false;
							loadMore();
						}else{
							console.log("else Reached bottom of page");
						}
					}
				}
			});
		});

		function loadMore(){
			offset += 9;
			showUpdate = "{{ request()->showUpdate }}";
			$.ajax({
				url: "{{ route('product.loadMoreProducts') }}?offset="+offset+"&layout="+layout+"&showUpdate="+showUpdate,
				type: 'GET',
				dataType: 'json',
				success: function(response){
					$(".product-container").append(response.html);
				},
				error: function(xhr, status, error) {
					console.error(xhr.responseText);
				},
				complete: function(xhr, status) {
					canLoad = true;
				}
			});
		}

		// function loadMore(){
		// 	// console.log("Reached bottom of " + offset);
		// 	offset += 9;
		// 	$('#loader').show();
		// 	// console.log("Reached bottom of page 2");
		// 	showUpdate = "{{ request()->showUpdate }}";
		// 	$.ajax({
		// 		url: "{{ route('product.loadMoreProducts') }}?offset="+offset+"&layout="+layout+"&showUpdate="+showUpdate,
		// 		type: 'GET',
		// 		dataType: 'json',
		// 		success: function(response){
		// 			// console.log(response);
		// 			$(".product-container").append(response.html);
		// 			// $(".product-container").append(response);
		// 			canLoad = true;
		// 			bindVideoEventListeners();
		// 			// // Observe each video element
		// 			$(response.productids).each(function(key,value) {
		// 				var newVideo=document.getElementById('video'+value);
		// 				new IntersectionObserver(loadMoreIntersecting, {threshold: 0.5}).observe(newVideo);
		// 			});
		// 		},
		// 		error: function(xhr, status, error) {
		// 			console.error(xhr.responseText);
		// 		},
		// 		complete: function(xhr, status) {
		// 			// This function will be called regardless of success or failure
		// 			// console.log('Request completed');
		// 			// console.log('Status: ' + status);
		// 			$('#loader').hide();
		// 		}
		// 	});
		// }

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
				$('.card-layout-change-btn').removeClass('d-none');
			}else{
				layout = 1;
				$('.video-card').addClass('col-12');
				$('.video-card').removeClass('col-4');
				$('.card-layout-change-btn').addClass('d-none');
			}
			
			$('#topMenuFixDiv').toggleClass('d-none');
			$('#large_product_title').toggleClass('d-none');
			$('.large-cards').toggleClass('d-none');
			$('.medium-cards').toggleClass('d-none');
		}
</script>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
	$(document).ready(function () {
		$('.scrooling').animate({scrollTop: 0}, 'slow');
	});

	$("html, body, .full-height-video, .body-fix, .video-container, .play-video-thumbnail, .vp-height, .video-section").css({
			height: $(window).height()
	});
	
	function calculate_after_profit() {
		var price = parseFloat($('#product_price').val()||0);
		var profit = parseFloat($('#seller_profit').val()||0);
		$('#price_after_profit').val(price+profit);
	}

	$(document).ready(function () {
		placeOrderFixDivHeight = $("#placeOrderFixDiv").height();
		$(".modal-dialog").css('bottom', placeOrderFixDivHeight - 2);
		$(".product-bottom-right-icons").css('bottom', placeOrderFixDivHeight + 10);
		
		$(document).on('click','.moreModalShow',function () {
			url = "{{ route('supportive.download-video') }}?url=" + videoData.video;
			$('#save-video').attr('href', url);

			url = "{{ route('supportive.download-file') }}?url=" + videoData.thumbnail;
			$('#save-thumbnail').attr('href', url);
			$('#moreModal').modal('show');
		});

		$(document).on('click','.showModalVideo',function () {
			showModal('#videoModal');
		});

		$(document).on('click','.sellerProfitModalShow',function () {
			$t = $(this).closest('.video-card');
			id = $t.find('#id').val();
			original_price = $t.find('#original_price').val();
			price = $t.find('#price').val();
			reseller_profit = $t.find('#reseller_profit').val();
			
			$('#product_id').val(id);
			$('#product_price').val(original_price);
			$('#seller_profit').val(parseFloat(reseller_profit || 0));
			$('#price_after_profit').val(parseFloat(original_price || 0) + parseFloat(reseller_profit || 0));

			$('#updateSellerProfitModal').addClass('show');
			$('body').addClass('modal-open');
			$('.modal-backdrop').addClass('show');
			$('.modal-backdrop').fadeIn();

			$('#updateSellerProfitModal').attr('aria-modal', 'true');
			$('#updateSellerProfitModal').css('display', 'block');
		});

		$(document).on('click','#sellerProfitUpdateBtn',function (e) {
			e.preventDefault();
			var formData = $('#updateSellerProfitForm').serialize();
			var product_id = $('#product_id').val();
			var product_price = $('#product_price').val();
			var price_after_profit = $('#price_after_profit').val();
			var seller_profit = $('#seller_profit').val()||0;
			$.ajax({
				url: "{{route('product.update-seller-profit')}}",
				type: 'POST',
				data: formData,
				success: function(response) {
					
					toastr["info"](response.message);
					
					if (response.status==1) {
						$('.price'+product_id).text(price_after_profit);

						$t = $('#video-card'+product_id);
						$t.find('#price').val(price_after_profit);
						$t.find('#reseller_profit').val(seller_profit);

						$('.closeModal').click();
					}
				},
				error: function(xhr, status, error) {
					console.error(xhr.responseText);
				}
			});
		});

		$('.closeVideoModal').click(function(event) {

			event.preventDefault();

			// stop autdio

			// close modal
			$('.closeModal').click();
		});
	});
	
</script>
@endsection