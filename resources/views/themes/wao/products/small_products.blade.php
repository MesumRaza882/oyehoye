@extends('themes.wao.layouts.main')
<style>
    .full-height-video {
        height: 50vh !important; 
        overflow: hidden; 
        position: relative;
    }
    .main-video-section {
        height: 80% !important;
    }
    .white_text {
        color: white;
    }
    .move-back {
        position: fixed;
        top: 5px;
        left: 5px;
        z-index: 1000;
    }
    .refresh_play_button {
        position: fixed;
        top: 0px;
        right: 10px;
        z-index: 1000;

    }

    @media only screen and (max-width: 600px) {
        .main-video-section {
            height: 24% !important;
        } 
    }
</style>
@section('content')
    <div class="move-back d-flex">
        <img src="{{asset('themes/wao/images/arrow.png')}}" width="70px" height="30px" alt="">
        <h6 class="white_text p-2">Share Pics</h6>
    </div>
    <div class="refresh_play_button">
        <i class="fa-solid fa-arrows-rotate white_text bg_black refresh_play_icon "></i>
        <br>
        <i class="fa-solid fa-play refresh_play_icon play-button cursor-pointer yellow_text"></i>
    </div>
    <div class="container-fluid mt-5">
        <div class="row">
            @foreach($products as $index => $item)
                <div class="col-3 p-1">
                    <div class="full-height-video mb-3">
                        <div class="main-video-section">
                            <video class="video video-section" muted autoplay loop>
                                <source src="{{$item->video}}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <button id="doneBtn{{$index}}" class="btn btn-block counter_btn w-100 rounded-0 py-1" onclick="handleDone({{$index}})">
                            <h5 class="font-weight-bold">Done</h5>
                        </button>
                        <div class="btn btn-block counter_btn w-100 rounded-0 py-2" id="counter{{$index}}" style="display: none;">
                            <span class="mx-md-2" onclick="decrement({{$index}})">
                                <img src="{{asset('themes/wao/images/minus.png')}}" width="20px;" alt="">
                            </span>
                            <span id="count{{$index}}">
                                <h5>0</h5>
                            </span>
                            <span class="mx-md-2" onclick="increment({{$index}})">
                                <img src="{{asset('themes/wao/images/add.png')}}" width="20px;" alt="">
                            </span>
                        </div>
                        <div class="top_header_section">
                            <h6 class="top_price" id="priceCount{{$index}}">Rs. {{$item->price}}</h6>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-3 p-1">
                <div class="full-height-video mb-3">
                    <div class="main-video-section">
                        <video class="video video-section" muted autoplay loop>
                        <source src="{{asset('themes/wao/videos/sample.mp4')}}" type="video/mp4">
                        Your browser does not support the video tag.
                        </video>
                    </div>
                    <button id="doneBtn" class="btn btn-block bg_white w-100 rounded-0 rose_text pb-0">
                        <h5 class="font-weight-bold">Sold Out</h5>
                    </button>
                    <div class="top_header_section">
                        <h6 class="top_price">Rs. 1650</h6>
                        {{-- <div class="refresh_play_buttons">
                            <i class="fa-solid fa-arrows-rotate bg_black refresh_play_icon"></i>
                            <br>
                            <i class="fa-solid fa-play refresh_play_icon play-button cursor-pointer yellow_text"></i>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="product_place_order py-2 d-flex justify-content-between">
                <i class="fa-solid fa-chevron-left pt-2 product-footer-icons"></i>
                <button class="btn product-footer-placeorder-button">
                    Place Order Rs. <span id="price">0</span>
                    <span id="count2">0</span>
                </button>
                <a href="{{route('product.MediumProducts')}}"  class="white_text">
                    <i class="fa-solid fa-box pt-2 product-footer-icons"></i>
                </a>
            </div>
        </div>

        
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  
function change_view(){
  
  console.log('dsa');
    $.ajax({
        url: "{{route('product.MediumProducts')}}",
        type: 'GET',
        success: function(response){
	       document.documentElement.innerHTML =response;
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}
    document.addEventListener("DOMContentLoaded", function() {
        var video = document.querySelector('.video');
        var video2 = document.querySelector('.video2');
        var playButton = document.querySelector('.play-button');
        var playButton2 = document.querySelector('.play-button2');
        var playButtonCenter1 = document.querySelector('.playButtonCenter1');
        var playButtonCenter2 = document.querySelector('.playButtonCenter2');
    
        video.pause();
        video2.pause();
    
        playButton.addEventListener('click', function() {
            if (video.paused) {
                video.play();
                video.muted = false;
            } else {
                video.pause();
            }
        });

        playButton2.addEventListener('click', function() {
            if (video2.paused) {
                video2.play();
                video2.muted = false;
            } else {
                video2.pause();
            }
        });

        playButtonCenter1.addEventListener('click', function() {
            if (video.paused) {
                video.play();
                playButtonCenter1.style.display = 'none';
                video.muted = false;
            } else {
                video.pause();
            }
        });

        playButtonCenter2.addEventListener('click', function() {
            if (video2.paused) {
                video2.play();
                playButtonCenter2.style.display = 'none';
                video2.muted = false;
            } else {
                video2.pause();
            }
        });
    });

    const counts = Array.from({ length: {{$products->count()}}, }).map(() => 0);
    const toggleDone = (index) => {
        const doneBtn = document.getElementById('doneBtn' + index);
        const counter = document.getElementById('counter' + index);
        if (counts[index] === 0) {
            console.log('Count is 0, show Done button');
            doneBtn.style.display = 'inline-block';
            counter.style.display = 'none';
        } else {
            console.log('Count is greater than 0, hide Done button');
            doneBtn.style.display = 'none';
            counter.style.display = 'inline-block';
        }
    };

    const increment = (index) => {
        counts[index]++;
        updateCount(index);
        toggleDone(index);
    };

    const decrement = (index) => {
        if (counts[index] > 0) {
            counts[index]--;
            updateCount(index);
            toggleDone(index);
        }
    };

    const handleDone = (index) => {
        counts[index] = 1;
        console.log('Done button clicked, count:', counts[index]);
        updateCount(index);
        toggleDone(index);
    };

    const updateCount = (index) => {
        const countElement = document.getElementById('count' + index);
        const priceElement = document.getElementById('priceCount' + index);
        const priceText = priceElement.innerText;
        const price = parseFloat(priceText.replace('Rs. ', '').replace(/[^0-9.]/g, ''));
        countElement.innerText = counts[index];
        document.getElementById('price' + index).innerText = counts[index] * price;
    };

    toggleDone();
</script>
@endsection