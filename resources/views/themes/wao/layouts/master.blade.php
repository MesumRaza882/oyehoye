<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Navbar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	
	<!-- <link rel="stylesheet" href="https://respectmart.com/plugins/aos/aos.min.css" /> -->
	<!-- <script src="https://respectmart.com/plugins/aos/aos.min.js"></script> -->
	
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

	<style>
    * {
        margin: 0px;
        padding: 0px;
        box-sizing: border-box;
        /* font-family: "Poppins", sans-serif; */
				font-family: "Public Sans";
    }
    :root {
        --white: #ffffff;
        --primary: rgba(40, 167, 69, 1);
        --lightGreen: #daf2da;
        --yellow: #ffe617;
    }
		/* overwritter */
		.bg-primary{
			background: var(--primary) !important;
		}
		.btn-primary{
			background: var(--primary) !important;
			border:1px solid var(--primary) !important;
		}
		.text-primary{color: var(--primary) !important;}
		.border-primary{border:1px solid var(--primary) !important;}

		.rounded-lg{border-radius: 30px;}
		body{
			font-family: "Public Sans" !important;
		}
    .navbar-custom {
      background-color: var(--white);
    }
    .navbar .nav-item {
        padding: 0px 10px;
    }
    .navbar .dropdown-toggle::after {
        display: none;
    }
    .green-button {
        background-color: var(--green);
        color: white;
    }
    .green-button:hover {
        background-color: var(--green);
        color: var(--white);
    }
    .green-text {
        color: var(--green);
    }
    .dropdown .dropdown-menu{
        border-radius: unset;
    }
    .quote_section {
			box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
			border-radius: 30px;
			padding: 30px 50px !important;
			background-color: #FFFFFF;
    }

    .fs-13px{font-size: 13px;}
    .fs-14px{font-size: 14px;}
    .fs-16px{font-size: 16px;}
    .fs-31px{font-size: 31px;}
    .fs-35px{font-size: 35px;}
    .fs-38px{font-size: 38px;}
    .fs-40px{font-size: 40px;}
    .fs-50px{font-size: 50px;}

		.text-green{color:rgba(52, 168, 83, 1);}

		.py-70px,.pt-70px{padding-top:70px;}
		.py-70px,.pb-70px{padding-bottom:70px;}
    .form-control:focus {
        box-shadow: unset;
        border-color: var(--green);
    }
    .offers_section {
			background-color: var(--lightGreen);
    }
    .text_green {
        color: var(--green);
    }
    .custom-card {
			background-color: var(--white);
			text-align: center;
			padding: 20px;
			border-radius: 10px;
			height: 180px;
    }
    .networks {
			box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
			margin: 5px;
			padding:  15px 7px;
			border-radius: 10px;
			width: 100px;
			height: 80px;
    }
    .bg_yellow {
			background-color: var(--yellow);
    }


    @media (max-width: 768px) {
			.hide-md-up {
				display: none !important;
			}
    }


		/* general */
		.btn-outline-white{border: 2px solid rgba(255, 255, 255, 1);color:white}
		a{text-decoration: none;}

		/* home page */
    .gradient-bg {background: linear-gradient(96.7deg, #051709 0.64%, #135021 30.73%, #28A745 53.72%, #D1FA30 96.32%);}
		.s-heading{letter-spacing: 0.5em;border-left:3px solid rgba(176, 48, 48, 1); color:rgba(176, 48, 48, 1);background: rgba(255, 238, 238, 0.75);}

		/* hero/top section */
		.bg-map{background-image: url('images/map.png');background-repeat: no-repeat;background-position: right center;background-size: 80% 100%;}
		.hero-heading{font-size:40px;font-weight:700;color:white}
		.hero-para{font-size:18px;color:white;font-weight:400}
		.start-now{background: linear-gradient(74.9deg, #FBBC05 -33.23%, #A7B426 5.12%, #34A853 63.86%, #1D572D 107.92%, #FFFFFF 123.43%);font-family: Public Sans;font-size: 16px;font-weight: 700;}

		/* blog section */
		.blog-image-overlay{position: absolute;top: 0; height: 100%; width: 100%;left: 0;background: linear-gradient(0deg, #000000 14.34%, rgba(60, 60, 60, 0) 43.86%);background-blend-mode: multiply;}
		.blog-text-over-image{position: absolute; bottom:10px;left:10px;z-index:1}

		/* testinomials */
		.t-arrow .prev, .t-arrow .next{cursor:pointer;height:40px;width:40px;border-radius:50%;padding-top:8px;}
		.t-arrow .prev{background:rgba(52, 168, 83, 1);color:white}
		.t-arrow .next{color:rgba(52, 168, 83, 1);background:white}
		.t-card{padding: 50px 40px 50px 40px;border-radius: 48px 48px 48px 0px;gap: 10px;}
		.t-card:nth-child(even) {background: rgba(52, 168, 83, 1);color: #ffffff;}

		@media (min-width: 992px){
			.bg-map{background-size: 65% 100%;}

			.hero-heading{font-size:66px;font-weight:700;color:white}
			.hero-para{font-size:31px;color:white;font-weight:400}

			.contact-us-robot{margin-bottom: -110px; margin-left: -90px;}
			.contact-us-robot img{height: 160px; width: 160px;}
			.contact-us-left{margin-top: 100px;}
		}

footer{position:relative;background: rgba(38, 38, 38, 1); color:#ffffff !important}
.zi-1{z-index:1}

.footer-widgets{margin-bottom:40px;}
@media (min-width: 992px){
.footer-widgets{margin-bottom:0;}
}
.footer-widgets__title{color: rgba(163, 163, 163, 1);font-size:16px;font-weight:400;letter-spacing:normal;line-height:28px;margin-bottom:20px;}
.footer-widgets__text{margin-top:32px;font-size:16px;font-weight:400;letter-spacing:normal;line-height:30px;color:rgba(255, 255, 255, 0.7);}
.footer-widgets--address{margin-top:35px!important;}
.footer-widgets--address li{margin-bottom:20px;}
.footer-widgets__list{padding:0;margin:0;}
.footer-widgets__list li{list-style:none;color:var(--color-headings);display:flex;}
.footer-widgets__list li a{color:var(--color-headings);font-size:16px;font-weight:400;letter-spacing:normal;line-height:2.75;transition:0.4s;}
.footer-widgets__list li a:hover{text-decoration:underline;}
.footer-widgets__list li i{font-size:14px; margin-right:10px;}
.footer-widgets__list li span{display:block;font-size:16px;font-weight:400;letter-spacing:normal;line-height:1.75;}
.copyright{padding-top:10px;padding-bottom:10px;}
.footer-social-share{padding-top:11px;padding-bottom:30px;}
.footer-social-share ul{margin-bottom:0;}
.footer-social-share ul li a{position:relative;margin:0 8px;color:var(--color-headings);transition:0.4s;}
.footer-social-share ul li a:hover{color:#ff5722;}
.footer-social-share ul li a::before{content:".";position:absolute;left:0;bottom:0;transform:translateX(-20px);}
.footer-social-share ul li:first-child a:before{display:none;}
.footer-social-share.dot-right ul li a::before{transform:translateX(-12px);}
.footer-social-share--1{padding-top:20px;padding-bottom:20px;}
.footer-social-share--1 ul li a{margin-left:20px;}
.footer-social-share--1.dot-right ul li a::before{transform:translateX(-20px);}

  </style>
</head>
<body>
    @yield('content')

</body>
</html>