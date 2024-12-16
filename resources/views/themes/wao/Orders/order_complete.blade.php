@extends('themes.wao.layouts.main')
@section('style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400..700&display=swap" rel="stylesheet">
<style>
	.urdu-font{
		font-family: "Noto Nastaliq Urdu", serif;
		line-height:2;
	}
</style>
@endsection
@section('content')
{{-- <style>
	body {
		background-color: black;
	}
</style> --}}
<div class="container-fluid ">
	{{-- 080002345 --}}
	<div class="row justify-content-center">
		<div class="col-12 col-md-5 col-lg-4">
			<div class="row justify-content-center">
				<div class="col-12  text-center scree_bg mt-2">
					<img src="{{asset('themes/wao/images/order_complete.jpg')}}" class="text-center rounded" width="300px" alt="">
					{{-- <h6 class="text-white mt-5 urdu-font" dir="rtl">
						<span>معزز کسٹمر آرڈر کرنے کا بہت شکریه۔آپ اسی آڈر کے</span>
						<span>ID</span>
						<span>اندر کوؑی بھی آرٹیکل کو</span>
						<span>add</span>
						<span>یا</span>
						<span>remove</span>
						<span>کر سکتے</span> 
						<span>جب تک آپ کے آرڈر کا</span>
						<span>detail status (order)</span>
						<span>تبدیل ھو کر</span>
						<span>dispatch</span>
						<span>نہ ہو جائے</span> 
					</h6> --}}
					<h6 dir="rtl" class="text-white mt-5 urdu-font">معزز کسٹمر آرڈر کرنے کا بہت شکریه۔آپ اسی آڈر کے ID کے اندر کوؑی بھی آرٹیکل کو add یا remove کر سکتے ہین جب تک آپ کے آرڈر کا detail status (order) تبدیل ھو کر dispatch نہ ہو جائے</h6>
					<a href="{{route('orders')}}" class="btn button1 my-5">Go to My Orders</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection