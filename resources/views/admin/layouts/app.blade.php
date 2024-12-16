<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title> @yield('title','Wao-App Collection')</title>

    <!-- **jquery cdn ** -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous">
    </script>

    <!--** Toaster Library **-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0- 
        alpha/css/bootstrap.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!--** End Toaster **-->

    <meta property="og:title" content="Oyehoye">
<meta property="og:description" content="Order Slip">
<meta property="og:image" content="https://oyehoyebridalhouses.com/slip/1704098841.jpeg">
<meta property="og:image:width" content="200">
<meta property="og:image:height" content="200">
<meta property="og:url" content="https://oyehoyebridalhouses.com/slip/1704098841.jpeg">

    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{asset('assets/css/app.min.css')}}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css?ver=1.1')}}">
    <link rel="stylesheet" href="{{asset('assets/css/components.css')}}">
    <!-- CSRF Token -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.6/css/ionicons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/custom.css?v=1.2.2')}}">
    <!-- dataTable css file -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">
    <!--tagsinput-->
    <!-- <link rel='stylesheet'
         href='https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'> -->
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    @yield('style')

    <style>
        .btn{border-radius:20px !important;}
        .rounded-c1{border-radius:10px !important;}
        .rounded-c{border-radius:20px !important;}
        .card{box-shadow:unset !important; border-radius:20px !important; padding: 0.5rem 0.5rem !important;}
        .no-shadow{box-shadow:unset !important;}
    </style>

</head>

<body>
    <!-- End Sessions of message when item update then generate session and after 5 seconds end -->
    @if(Session::has('end_time'))
    @if(Carbon\Carbon::now() >= Session::get('end_time'))
    {{ session()->forget('message')}}
    {{ session()->forget('end_time')}}
    @endif
    @endif
    <!-- <div class="loader_new"></div> -->
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <!-- *** Navbar *** -->
            @include('admin.layouts.navbar')
            <!-- *** End Navbar *** -->

            <!-- *** Modal For Adding/Update/Delete *** -->
            @include('include.modal.index')
            @include('include.modal.updateModal')
            @include('include.modal.deleteModal')
            @include('include.modal.dispatchModal')
            @include('include.modal.dispatchMnpModal')
            @include('include.modal.dispatchPostExModal')
            @include('include.modal.orderHistoryModal')
            @include('include.modal.returnConfirmOrderModal')
            <!-- *** End Modal *** -->

            <!-- *** Sidebar *** -->
            @include('admin.layouts.sidebar')
            <!-- *** End Sidebar *** -->

            @yield('content')

            <!-- *** Footer *** -->
            <!-- @include('admin.layouts.footer') -->
            <!-- *** End Footer *** -->
        </div>
    </div>

    <!--  ************Script Libraries******************** -->
    <!--bootstrap js-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- General JS Scripts -->
    <script src="{{asset('assets/js/app.min.js')}}"></script>
    <!-- JS Libraies -->
    <!-- <script src="{{asset('assets/bundles/apexcharts/apexcharts.min.js')}}"></script> -->
    <!-- Page Specific JS File -->
    <script src="{{asset('assets/js/page/index.js')}}"></script>
    <!-- Template JS File -->
    <script src="{{asset('assets/js/scripts.js')}}"></script>
    <!-- Custom JS File -->
    <script src="{{asset('assets/js/custom.js?v=1.4')}}"></script>
    <!-- dom to image for order slip -->
    <script src="{{asset('assets/js/dom-to-image.js')}}"></script>
    <!--Input Tags js file-->
    <!-- <script src='https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js'></script> -->
    <!--DataTable js file-->
    <!--<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>-->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <!--Input tags script-->
    <!-- ***Ck editoe -->
    <!-- <script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script> -->
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
    <!-- ************ End Script Libraries *************** -->

    <!-- Input Tags Script Work-->
    <script>
        $(function() {
            $('input').on('change', function(event) {

                var $element = $(event.target);
                var $container = $element.closest('.example');

                if (!$element.data('tagsinput'))
                    return;

                var val = $element.val();
                if (val === null)
                    val = "null";
                var items = $element.tagsinput('items');

                $('code', $('pre.val', $container)).html(($.isArray(val) ? JSON.stringify(val) : "\"" + val.replace('"', '\\"') + "\""));
                $('code', $('pre.items', $container)).html(JSON.stringify($element.tagsinput('items')));


            }).trigger('change');
        });
    </script>
    <!-- End Input Tags Script -->

    @if(Session::has('status'))
    <script>
        toastr.success("{{ Session::get('status') }}");
    </script>
    @endif
    <!-- Custom Script work -->
    <script>
        // preloader hide
        window.onload = function() {
            $(".loader_new").fadeOut();
        }

        $(document).ready(function() {

            // when pageload empty order filtered ids 
            $('.filterOrderIds').val('');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // dataTables
            $(document).ready(function() {
                $('.myTable').DataTable();

                //dataTable export excel csv buttons stc
                $('#examples').DataTable({
                    paging: false,
                    "order": [],
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ]
                });

            });

            // active tr of Table when click
            $('.active_table tbody').on('click', 'tr', function(e) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
            });

            // add more customer deatils
            $(document).on('click', '.add_row', function(e) {
                e.preventDefault();
                var row = '<div class="row d-flex">' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    '<label>Customer Name</label>' +
                    '<input type="text" class="form-control" name="name[]"  placeholder="Enter Customer Name" value="{{old('
                name ')}}">' +
                    '</div>' +
                    '<span class="text-danger">@error('
                name '){{$message}}@enderror</span>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    ' <label>Address</label>' +
                    '<textarea class="form-control" name="address[]" placehlder="Enter Customer Address etc" value="{{old('
                address ')}}"></textarea>' +
                    '</div>' +
                    ' <span class="text-danger">@error('
                address '){{$message}}@enderror</span>' +
                    '</div>' +
                    '</div>';
                $('.main_div').append(row);
            });

            // password toggle view
            $(document).on('click', '.toggle-password', function() {
                $('.toggle-icon').toggleClass("fa-eye fa-eye-slash");
                var input = $("#password_adm");
                input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
            });

            // add more customer reviews
            $(document).on('click', '.add_row_review', function(e) {
                e.preventDefault();
                var row = '<div class="row d-flex">' +
                    '<div class="col-md-4">' +
                    '<div class="form-group">' +
                    '<label>Customer Name</label>' +
                    '<input type="text" class="form-control" name="name[]"  placeholder="Enter Customer Name" value="{{old('
                name ')}}">' +
                    '</div>' +
                    '<span class="text-danger">@error('
                name '){{$message}}@enderror</span>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label>Customer Review</label>' +
                    '<input type="text" class="form-control" name="review[]"  placeholder="Enter Customer review" value="{{old('
                review ')}}">' +
                    '</div>' +
                    '<span class="text-danger">@error('
                review '){{$message}}@enderror</span>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    ' <label>Customer Rreview Description</label>' +
                    '<textarea class="form-control" name="description[]" placehlder="Enter Customer description " value="{{old('
                description ')}}"></textarea>' +
                    '</div>' +
                    ' <span class="text-danger">@error('
                description '){{$message}}@enderror</span>' +
                    '</div>' +
                    '</div>';
                $('.main_div_review').append(row);
            });

            //select all checkbox and delete
            $(document).on('click', 'input[name="main_checkbox"]', function() {
                if (this.checked) {
                    $('input[name="cat_checkbox"]').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('input[name="cat_checkbox"]').each(function() {
                        this.checked = false;
                    });
                }
                toggleDeleteButton()
            });

            //if select all checkboxes  then checked main checkbox  
            $(document).on('change', 'input[name="cat_checkbox"]', function() {
                var allids = [];
                if ($('input[name="cat_checkbox"]').length == $('input[name="cat_checkbox"]:checked').length) {
                    $('input[name="main_checkbox"]').prop('checked', true);
                } else {
                    $('input[name="main_checkbox"]').prop('checked', false);
                }
                toggleDeleteButton()
            });

            //toggle function delete button show/hide
            function toggleDeleteButton() {

                if ($('input[name="cat_checkbox"]:checked').length > 0) {
                    $('#deleteAllbtn').text('Delete (' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#filterSpecificIds').text('(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#pinItems').text('Add to Pin(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#whiteItems').text('Add to White-List(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#multanItems').text('Multan-List(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#profitMarkasPaid').text('Mark as Paid-Profit(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#draftToPublishItems').text('Published(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#freezItems').text('Freezed(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    $('#unfreezItems').text('Un-Freezed(' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    // this section belongs to order specific filter
                    $('#deleteAllbtnOrders').text('Delete (' + $('input[name="cat_checkbox"]:checked').length + ')').removeClass('d-none');
                    // order deletion
                    var allids = [];
                    $('input[name="cat_checkbox"]:checked').each(function() {
                        allids.push($(this).val());
                        $('.filterOrderIds').val(allids);
                    });
                } else {
                    $('.filterOrderIds').val('');
                    $('button#deleteAllbtn').addClass('d-none');
                    $('#filterSpecificIds').addClass('d-none');
                }
            }

            // delete slected Problems
            $(document).on('click', '.deleteAllbtnproblem', function(e) {
                e.preventDefault();
                var x = confirm("Are you sure you want to delete Problems?");
                if (x) {
                    var allids = [];

                    $('input[name="cat_checkbox"]:checked').each(function() {
                        allids.push($(this).val());
                        $.ajax({
                            type: "POST",
                            url: "{{route('delCheckProblems')}}",
                            data: {
                                ids: allids,
                            },

                            success: function(response) {
                                window.location.reload(true);
                            },
                        });

                    });
                }

            });

            // selected Luckydraw delete
            $(document).on('click', '.deleteAllbtnLucky', function(e) {
                e.preventDefault();
                var x = confirm("Are you sure you want to delete LuckyDraws?");
                if (x) {
                    var allids = [];

                    $('input[name="cat_checkbox"]:checked').each(function() {
                        allids.push($(this).val());
                        $.ajax({
                            type: "POST",
                            url: "{{route('delCheckLucky')}}",
                            data: {
                                ids: allids,
                            },

                            success: function(response) {
                                window.location.reload(true);
                            },
                        });

                    });
                }

            });

            // selected CategoriesReviews delete
            $(document).on('click', '.deleteAllbtnReviews', function(e) {
                e.preventDefault();
                var x = confirm("Are you sure you want to delete Reviews?");
                if (x) {
                    var allids = [];

                    $('input[name="cat_checkbox"]:checked').each(function() {
                        allids.push($(this).val());
                        $.ajax({
                            type: "POST",
                            url: "{{route('delCheckCatReview')}}",
                            data: {
                                ids: allids,
                            },

                            success: function(response) {
                                window.location.reload(true);
                            },
                        });

                    });
                }

            });

            // delete slected Addresses
            $(document).on('click', '.deleteAllbtnAddresses', function(e) {
                e.preventDefault();
                var x = confirm("Are you sure you want to delete Addresses?");
                if (x) {
                    var allids = [];

                    $('input[name="cat_checkbox"]:checked').each(function() {
                        allids.push($(this).val());
                        $.ajax({
                            type: "POST",
                            url: "{{route('delAddress')}}",
                            data: {
                                ids: allids,
                            },

                            success: function(response) {
                                window.location.reload(true);
                            },
                        });

                    });
                }

            });

            // View Notes
            $(document).on('click', '.view_notes', function(e) {
                e.preventDefault();
                $('.notes_div').toggle(1000);
            });



            // View Extra Product Fields
            $(document).on('click', '.view_extra_fields_product', function(e) {
                e.preventDefault();
                $('.extra_fields_product').toggle(1000);
            });

            // Add All category Reviews(Updated)
            $(document).on('submit', '#addReviewform', function(e) {
                e.preventDefault();

                let formdata = new FormData($('#addReviewform')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{route('addCatReview')}}",
                    data: formdata,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        //addReviews
                        if (response.check_num == 100) {
                            $('.successRev').removeClass('d-none');
                            $('.failRev').addClass('d-none');
                            setTimeout(function() {
                                location.reload(true);
                            }, 1000);
                        }
                        if (response.check_num == 200) {
                            $('.failRev').removeClass('d-none');
                        }

                    },
                });
            });

            // end
        });

        // image preview before uploading
        document.getElementById("thumbnail").onchange = function(event) {
            frame.src = URL.createObjectURL(event.target.files[0]);
            document.querySelector(".img_preview_container").style.display = 'block';
        }

        // image preview Update Modal uploading
        function selected_preview() {
            selected_frame.src = URL.createObjectURL(event.target.files[0]);
            $('#upd_img_container').removeClass('d-none');
            // document.querySelector(".upd_img_container").style.display = 'block';
        }

        // Video Preview
        document.getElementById("videoUpload").onchange = function(event) {
            let file = event.target.files[0];
            let blobURL = URL.createObjectURL(file);
            document.querySelector("video").src = blobURL;
            document.querySelector("#video_container").style.display = 'block';
        }
    </script>
    <!-- End Custom Script work -->

    <!-- Toaster Messages -->
    <script>
        @if(Session::has('message'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.success("{{ session('message') }}");
        @endif

        @if(Session::has('error'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            // "closeButton": false,
            // "debug": false,
            // "progressBar": true,
            // "positionClass": "toast-bottom-right",
            // "preventDuplicates": true,
            // "onclick": null,
            "showDuration": "5000",
            // "hideDuration": "1000",
            // "timeOut": "5000",
            // "extendedTimeOut": "1000",
            // "showEasing": "swing",
            // "hideEasing": "linear",
            // "showMethod": "fadeIn",
            // "hideMethod": "fadeOut",
        }
        toastr.error("{{ session('error') }}");
        @endif

        @if(Session::has('info'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.info("{{ session('info') }}");
        @endif

        @if(Session::has('warning'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.warning("{{ session('warning') }}");
        @endif
    </script>
    <!-- End Toaster Message -->

    <script>
        /**
         * https://github.com/hassan005004/jquery-ajax-easy
         * On Click Data Attribute to Serve via Ajax
         * 
         * credit goes to hassan - zahidaz.com
         **/
        $.fn.callDataAttributeAjax = async function(_this) {
          var data = {};
          $.each(_this.attributes, function(index, attr) {
            if(attr.name.substr(0,4) == 'data'){
              newName = attr.name.replace('data-','').replace(/-/g,'_');
            //   console.log(newName);
              data[newName] = this.value;
            }
          });

          // $.ajaxSetup({
          //     headers: {
          //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          //     }
          // });

          isConfirmed = true;
          if(data.ask_confirmation == 'true'){
            result = await Swal.fire({
              title: "Are you sure?",
              text: "You won't be able to revert this!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Confirm"
            });
            
            if (result.isConfirmed) {
              isConfirmed = true;
            }else{
              isConfirmed = false;
            }
          }

          if(isConfirmed == true){
            const ot = await $.ajax({
              url: data.url,
              type: 'post',
              dataType: 'json',
              data: data,
              success: function(data) {
                if(data.st == 1){
                  toastr['success'](data.msg);
                  _this.remove();
                }else{
                  toastr['error'](data.msg);
                }
              },
              error: function(xhr) {
                console.log(xhr);
                if (xhr.status == 422) {
                  // handle valdaition error
                }
              }
            });

            return ot;

          }else{
            return null;
          }
          // const json = JSON.stringify(ot);
        };

        $('.trigger-js').click(function(){
          //Some code
          data_url = $(this).attr('data-url');
          output = $.fn.callDataAttributeAjax(this);
          output.then(function(data) {
            // handle success here
            // $("#loader").addClass("d-none");
          }).catch(e => {
            toastr['error']('Something went wrong');
            // handle catch
            // $("#loader").addClass("d-none");
          });
        });

    </script>
    @yield('scripts')
</body>

</html>