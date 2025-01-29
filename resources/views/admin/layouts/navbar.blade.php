@auth
    <nav class="navbar navbar-expand-lg main-navbar sticky">
        <div class="form-inline mr-auto">
            <ul class="navbar-nav mr-3">
                <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
            collapse-btn"> <i
                            data-feather="align-justify"></i></a></li>
                <!-- <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                  <i data-feather="maximize"></i>
                </a></li> -->
                <li>

                    @php
                        $balance = auth()->user()->balance;
                        $restrictInventory = auth()->user()->restrict_inventory;

                        $badgeClass = 'bg-success'; // Default green for sufficient balance

                        if ($balance < $restrictInventory) {
                            $badgeClass = 'bg-warning'; // Warning if less than restriction
                        }

                        if ($balance <= 0) {
                            $badgeClass = 'bg-danger'; // Danger if balance is 0 or negative
                        }
                    @endphp

                    <p class="fw-bold {{ $badgeClass }} p-2 badge mt-2">
                        {{ number_format($balance) }}
                    </p>
                </li>
                @if (!Route::is('waoseller.order.create') && !Route::is('waoseller.order.index'))
                    <li>
                        <a href="{{ route('waoseller.order.create') }}" class="ms-1 btn btn-sm bg-warning text-dark"><i
                                class="fa fa-plus"></i></a>
                    </li>
                @endif
                <li>
                    <!-- <form class="form-inline mr-auto" method="GET" action="{{ route('search') }}">
                  @csrf
                  <div class="search-element">
                    <input class="form-control" type="search" placeholder="Search" name="search" aria-label="Search" data-width="200" required value="">
                    <button class="btn" type="submit">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </form> -->
                </li>
            </ul>
        </div>
        <ul class="navbar-nav navbar-right">
            <!--Logout-->
            <li class="dropdown">
                <a href="{{ route('admin.logout') }}" class="btn btn-primary"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
                <form action="{{ route('admin.logout') }}" method="POST" class="d-none" id="logout-form">@csrf</form>
            </li>
            <!--Profile Change-->
            <li class="dropdown">
                <a class="btn p-2 border mx-2" id="admin_profile_modal"><i class="mx-1 fa-solid fa-user"></i></a>
            </li>
        </ul>
    </nav>

    <!-- Modal For Update profile  -->
    <div class="modal fade" id="exampleModalAddprojectadm" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span>My Profile</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateAdminform" enctype="multipart/form-data" method="POST" autocomplete="off">
                        @csrf

                        <!--modal body start-->
                        <section style="background-color: #eee;">
                            <div class="container pt-4">
                                <div class="row">
                                    <div class="col-lg-12 dataAdmin">
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <p class="mb-0">Full Name</p>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ auth()->user()->name }}">
                                                    </div>
                                                    <span class=" mb-2 text-danger  errr-span"></span>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <p class="mb-0">Email</p>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="email" readonly name="email" class="form-control"
                                                            value="{{ auth()->user()->email }}">
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <p class="mb-0">Password</p>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="password" class="form-control"
                                                            value="" placeholder="Change password">
                                                        <span class="text-muted py-3">If you want to change password?, use
                                                            this field</span>
                                                    </div>
                                                    <span class=" mb-1 text-danger  errr_span_pass"></span>
                                                </div>
                                                <hr>

                                                <!-- for admin and managers only -->

                                                @if (auth()->user()->role === 1)
                                                    @if (auth()->user()->trax_allow === 1)
                                                        <p class="fw-bold text-info">Update Trax Details</p>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <p class="mb-0">Api-Key</p>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="trax_api_key"
                                                                    class="form-control"
                                                                    value="{{ auth()->user()->trax_api_key }}">
                                                            </div>
                                                            <span class=" mb-2 text-danger  errr-span"></span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <p class="mb-0">Pickup-Address-id</p>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="trax_pickup_address_id"
                                                                    class="form-control"
                                                                    value="{{ auth()->user()->trax_pickup_address_id }}">
                                                            </div>
                                                            <span class=" mb-2 text-danger  errr-span"></span>
                                                        </div>
                                                        <hr>
                                                    @endif

                                                    @if (auth()->user()->postEx_allow === 1)
                                                        <p class="fw-bold text-info">Update Post-Ex Details</p>

                                                        <div class="py-2 card-body">
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p class="mb-0">Api-Token</p>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="postEx_apiToken"
                                                                        class="form-control"
                                                                        value="{{ auth()->user()->postEx_apiToken }}">
                                                                </div>
                                                                <span class=" mb-2 text-danger  errr-span"></span>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p class="mb-0">Pickup-Address-Code</p>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control"
                                                                        name="postEx_pickupAddressCode">
                                                                        <option
                                                                            {{ auth()->user()->postEx_pickupAddressCode ? '' : 'selected' }}
                                                                            value="">Select City</option>
                                                                        @for ($i = 1; $i <= 100; $i++)
                                                                            <option value="{{ sprintf('%03d', $i) }}"
                                                                                {{ auth()->user()->postEx_pickupAddressCode === sprintf('%03d', $i) ? 'selected' : '' }}>
                                                                                {{ sprintf('%03d', $i) }}
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <span class=" mb-2 text-danger  errr-span"></span>
                                                            </div>
                                                        </div>

                                                        <div class="pt-3 pb-1 text-white card-body bg-dark">
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p class="mb-0 text-white">Api-Token (Nowshera)</p>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input type="text" name="postEx_apiToken_nowshera"
                                                                        class="form-control"
                                                                        value="{{ auth()->user()->postEx_apiToken_nowshera }}">
                                                                </div>
                                                                <span class=" mb-2 text-danger  errr-span"></span>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p class="mb-0 text-white">Pickup-Address-Code
                                                                        (Nowshera)</p>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control"
                                                                        name="postEx_pickupAddressCode_nowshera">
                                                                        <option
                                                                            {{ auth()->user()->postEx_pickupAddressCode_nowshera ? '' : 'selected' }}
                                                                            value="">Select City</option>
                                                                        @for ($i = 1; $i <= 100; $i++)
                                                                            <option value="{{ sprintf('%03d', $i) }}"
                                                                                {{ auth()->user()->postEx_pickupAddressCode_nowshera === sprintf('%03d', $i) ? 'selected' : '' }}>
                                                                                {{ sprintf('%03d', $i) }}
                                                                            </option>
                                                                        @endfor
                                                                    </select>

                                                                </div>
                                                                <span class=" mb-2 text-danger  errr-span"></span>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    @endif

                                                    @if (auth()->user()->mnp_alllow === 1)
                                                        <p class="fw-bold text-info">Update MNP Details</p>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <p class="mb-0">MNP Username</p>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="mnp_username"
                                                                    class="form-control"
                                                                    value="{{ auth()->user()->mnp_username }}">
                                                            </div>
                                                            <span class=" mb-2 text-danger  errr-span"></span>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <p class="mb-0">MNP Password</p>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="mnp_password"
                                                                    class="form-control"
                                                                    value="{{ auth()->user()->mnp_password }}">
                                                            </div>
                                                            <span class=" mb-2 text-danger  errr-span"></span>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <p class="mb-0">MNP LocationId</p>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="locationID"
                                                                    class="form-control"
                                                                    value="{{ auth()->user()->locationID }}">
                                                            </div>
                                                            <span class=" mb-2 text-danger  errr-span"></span>
                                                        </div>
                                                        <hr>
                                                    @endif
                                                @endif

                                                <button type="submit"
                                                    class=" mt-4 btn btn-success ms-auto d-block">Update Profile</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end-->
                        </section>
                </div>

                </form>
            </div>
        </div>
    </div>
    <!--end Profile Add  Modal -->
@endauth


<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        / /
        add Project through Modal
        $(document).on('click', '#admin_profile_modal', function(e) {
            e.preventDefault();
            $('#exampleModalAddprojectadm').modal('show');
        });

        //form submit to add Post
        $(document).on('submit', '#updateAdminform', function(e) {
            e.preventDefault();

            let formdata = new FormData($('#updateAdminform')[0]);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.updateAdminProfile') }}",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {

                    //added
                    if (response.check_num == 100) {
                        toastr.success(response.status);
                        setTimeout(function() {
                            $('#exampleModalAddprojectadm').modal('hide');
                        }, 200);
                        window.location.reload(true);
                    }


                },
            });
        });
    });
</script>
