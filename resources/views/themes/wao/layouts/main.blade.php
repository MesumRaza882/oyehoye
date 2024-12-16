<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="minimum-scale=1, width=device-width, initial-scale=1.0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <title>{{ Domain::admin('name') }} | {{ Domain::admin('website') }}</title>
    <meta name="theme-color" content="{{ Domain::admin('color_2') }}">
    <style>
        :root {
            --green: #086423;
            --lightblue: #0EA8E6;
            --grey: #0C4B6A;
            --purple: #640064;
            --yellowGreen: #B2FF59;
            --yellow: {{ Domain::admin('color_2') }};
            --rose: #D62329;
            --white: #fff;
            --black: {{ Domain::admin('color_1') }};
            --c4: {{ Domain::admin('color_4') }};
            --background: {{ Domain::admin('color_1') }};
            --category_card_text: {{ Domain::admin('color_3') }};
            --yellow_text_color: {{ Domain::admin('color_2') }};
        }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/wao-theme.css') }}">

@yield('style')
@stack('style')
</head>
<body>
    <div class="scrooling">
        <div id="loader" class="loader-container" style="display: none;">
            <img src="{{asset('assets/img/spinner-white.svg')}}" alt="" width="70px" height="70px">
        </div>
        @yield('content')
        @yield('modal')
    
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

        <script>
            domain_setting = {
            'mute_video': "{{ Domain::admin('mute_video') }}"
            }
        </script>
	    <script>

			// document.addEventListener('DOMContentLoaded', function() {
			// 	$('#loader').hide();
			// });
			$(window).on('pageshow', function(event) {
				$('#loader').hide();
			});
	
			$(document).ready(function() {
				// Function to close the modal
				$(".modal").on("shown.bs.modal", function() {
					var urlReplace = "#" + $(this).attr('id');
					history.pushState(null, null, urlReplace);
				});
	
				$(window).on('popstate', function() { 
					$('.modal').removeClass('show');
					$('body').removeClass('modal-open');
					$('.modal-backdrop').removeClass('show');
					$('.modal-backdrop').fadeOut();
	
					$('.modal').attr('aria-modal', 'false');
					$('.modal').css('display', 'none');
				});
			});
	

            // show modal
            function showModal(selector){
                $(selector).addClass('show');
                $('body').addClass('modal-open');
                $('.modal-backdrop').addClass('show');
                $('.modal-backdrop').fadeIn();

                $(selector).attr('aria-modal', 'true');
                $(selector).css('display', 'block');

                var urlReplace = "#" + $(selector).attr('id');
                history.pushState(null, null, urlReplace);
            }
            

			// cloase modal by click
			$('.closeModal').click(function(event) {
				$('.modal').removeClass('show');
				$('body').removeClass('modal-open');
				$('.modal-backdrop').removeClass('show');
				$('.modal-backdrop').fadeOut();
	
				$('.modal').attr('aria-modal', 'false');
				$('.modal').css('display', 'none');
			});

		</script>
				@if( Session::has( 'success' ))
					<script>
						toastr["success"]("{{ Session::get('success') }}")
					</script>
        @elseif( Session::has('error'))
					<script>
						toastr["error"]("{{ Session::get('error') }}")
					</script>
        @endif

        @yield('script')
        @stack('script')
    </div>
</body>
</html>