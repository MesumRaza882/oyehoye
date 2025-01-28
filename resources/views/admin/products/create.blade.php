@extends('admin.layouts.app')
@section('content')
@section('title') Add Product @endsection
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <section class="section">
                    <div class="section-body">
                        <div class="card">
                            <form method="POST" id="myForm" enctype="multipart/form-data" action="{{route('save')}}" autocomplete="off">
                                @csrf
                                <div class="card-header text-center d-flex justify-content-between">
                                    <h4 class="fw-bold">Add New Item</h4>
                                    <a class="btn btn-primary btn-sm" href="{{route('all')}}">View All Items</a>
                                </div>
                                <div class="card-body">
                                    @include('admin.products.form-inputs')
                                    <!--foooter-->
                                    <div class="card-footer text-right">
                                        <button class="btn btn-outline-info" id="resetButton" type="reset">Reset</button>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('resetButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default reset action

        // Reset the form
        document.getElementById('myForm').reset();

        // Stop any ongoing file uploads (if applicable)
        // Assuming you are using XMLHttpRequest or Fetch API for file uploads
        if (window.currentUploadRequest) {
            window.currentUploadRequest.abort();
        }

        // Refresh the page
        window.location.reload();
    });
</script>


@endsection

