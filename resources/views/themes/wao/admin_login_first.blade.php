@extends('themes.wao.layouts.main')
@section('style')
<style>
html {
  scroll-snap-type: y mandatory;
}
.text-color{
	color: white !important;
}
</style>
@endsection
@section('content')
	<div class="container-fluid scroll-me">
		<div class="row justify-content-center">
			<div class="col-12 col-md-5 col-lg-4 scree_bg position-relative">
				<div class="row product-container">
					<div class="text-center text-color" style="font-size: 24px; top:50%; left:50%; transform: translate(-50%, -50%); position:fixed;color:white;">
						<span class="fs-34px">{{Domain::admin('name')}} </span>
						<br />
						<br />
						<br />
						Congratulations 🎉
						<br />
						<br />
						👋 <a class="text-color" href="{{ route('product.NewArrivals', ['showUpdate' => 1]) }}">Click here to setup</a>
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection

@section('modal')
@endsection

@section('script')
@endsection