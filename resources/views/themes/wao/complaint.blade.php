@extends('themes.wao.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-4 scree_bg">
        <div class="row">
            <div class="col-12 mx-auto my-4">
                <div class="d-flex">
                    <h4 class="white_text">{{ Domain::admin('name') }}</h4>
                    <p class="px-3 py-1 yellow_text">Collection</p>
                </div>
                <form action="{{route('user.addComplaint')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Name:</label>
                        <input type="text" placeholder="Name" name="user_name" class="form-control">
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Whatsapp Number:</label>
                        <input type="text" placeholder="Whatsapp" name="whatsapp" class="form-control">
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Problem:</label>
                        <textarea name="comment" class="form-control" placeholder="Enter your query" id="" cols="10" rows="3" style="border-radius: 25px;"></textarea>
                    </div>
                    <div class="form-group py-2">
                        <label for="" class="white_text py-1">Upload image for Complaint:</label>
                        <input type="file" placeholder="Whatsapp" class="form-control" name="image" id="imageInput" accept="image/*"> 
                    </div>
                    <div id="imageContainer" class="mb-2"></div>
                    <div class="d-grid">
                        <button type="submit" class="btn button1 btn-block">Send</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>

    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
            const image = new Image();
            image.src = e.target.result;
            image.style.width = '200px'; // Set the width of the image
            
            const removeButton = document.createElement('button');
            removeButton.textContent = '✖';
            removeButton.addEventListener('click', function() {
                document.getElementById('imageContainer').innerHTML = ''; // Clear the image container
                document.getElementById('imageInput').value = ''; // Clear the file input value
            });
            
            image.onload = function() {
                document.getElementById('imageContainer').innerHTML = '';
                document.getElementById('imageContainer').appendChild(image);
                document.getElementById('imageContainer').appendChild(removeButton);
            };
            };
            
            reader.readAsDataURL(file);
        }
        });
    </script>

@endsection