@extends('admin.layouts.app')
@section('content')
@section('title')Inventoty-Seller Record @endsection
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card">
                    <form method="POST" action="{{route('inventory.seller.update',$seller->id)}}" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit {{$seller->role === 1 ? 'Manager' : 'Re-Seller'}}-Account</h4>
                            <a href="javascript:history.back()" class="btn text-white btn-primary">Back</a>
                        </div>
                        <div class="card-body pt-0">
                            <!-- basic detail -->
                            <div class="row g-2">
                                <h6>Basic Detail</h6>
                                <div class="col-md-6"><x-x-input label="Name" value="{{$seller->name}}" name="name" required placeholder="Enter Name" /></div>
                                <div class="col-md-6"><x-x-input label="Email" value="{{$seller->email}}" type="email" name="email" required placeholder="Enter Email" /></div>
                                <div class="col-md-6"><x-x-input label="Password" type="text" name="password" placeholder="Enter Password" /></div>
                                @php
                                // Remove leading +92 and add leading 0
                                $whatsappNumber = str_replace('+92', '0', $seller->whatsapp_number);
                                @endphp
                                <div class="col-md-6"><x-x-input label="Whatsapp Number" type="phone" name="whatsapp_number" placeholder="Enter Whatsapp Number" value="{{ old('whatsapp_number', $whatsappNumber) }}" /></div>

                                <!-- for reseller update amount -->
                                <!-- @if ($seller->role === 3 || $seller->id === 1) -->
                                <!-- @endif -->

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="deductBalance" class="fw-bold text-warning">Restrict Balance</label>
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" {{$seller->isRestrictBalance === 1 ? 'checked' : ''}} name="isRestrictBalance" class="me-2">
                                            <input type="number" min="0" name="restrictBalance" value="{{ $seller->restrictBalance }}" class="form-control" placeholder="Enter restrict balance">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 border border-success p-2">
                                    <div class="form-group mb-2">
                                        <label for="balance">Balance</label>
                                        <input type="number" min="1" name="balance" class="form-control" placeholder="Enter balance">
                                    </div>
                                    <div>
                                        <label for="add_balance_note">Enter Note For Balance Add</label>
                                        <textarea name="add_balance_note" id="add_balance_note" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6 border border-danger p-2">
                                    <div class="form-group mb-2">
                                        <label for="deductBalance" class="text-danger">Deduct Balance</label>
                                        <input type="number" min="1" name="deductBalance" class="form-control" placeholder="Enter balance for deduction">
                                    </div>
                                    <div>
                                        <label for="deduct_balance_note">Enter Note For Deduct Add</label>
                                        <textarea name="deduct_balance_note" id="deduct_balance_note" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6 border border-warning p-2">
                                    <div class="form-group mb-2">
                                        <label for="restrict_inventory" class="text-warning">Restrict Balance Low</label>
                                        <input type="number" min="1" value="{{$seller->restrict_inventory}}" name="restrict_inventory" class="form-control" placeholder="balance for Restriction">
                                    </div>
                                    <div>
                                        <label for="profit_deduction_percentage">Profit Deduction Percentage</label>
                                        <input name="profit_deduction_percentage" value="{{$seller->profit_deduction_percentage}}" id="profit_deduction_percentage"  placeholder="Deduction Profit %" class="form-control" />
                                    </div>

                                    <div class="d-flex align-items-center mt-2">
                                        <label for="is_applied_restrict_inventory" class="fw-bold">Is Applied Restrict Inventory</label>
                                        <input type="checkbox" id="is_applied_restrict_inventory" {{$seller->is_applied_restrict_inventory === 1 ? 'checked' : ''}} name="is_applied_restrict_inventory" class="ms-2">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label for="status" class="fw-bold text-info">Status</label>
                                        <input type="checkbox" id="status" {{$seller->status === 1 ? 'checked' : ''}} name="status" class="ms-2">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="role">Update Role ( {{$seller->is_partner === 1 ? 'Partner' : ($seller->role === 3 ? 'Reseller' : ($seller->role === 4 ? 'WareHouse Team-member' : 'Manager/Admin'))}} ) :</label>
                                    <select class="form-control" name="role">
                                        <option value="">Select Role</option>
                                        <option value="partners">Partner</option>
                                        <option value="3">Reseller</option>
                                        <option value="1">Manager/Admin</option>
                                        <option value="4">Warehouse Team-member</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row mt-3">
                                <!-- product upload status -->
                                <h6>Product Upload Details</h6>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_upload_status" class="group">Product Upload Status <span class="text-danger">*</span></label>
                                        <select class="form-control" name="product_upload_status">
                                            <option value="published" {{$seller->product_upload_status === 'published' ? 'selected' : ''}}>Published</option>
                                            <option value="draft" {{$seller->product_upload_status === 'draft' ? 'selected' : ''}}>Draft</option>
                                        </select>
                                    </div>
                                </div>


                                <!-- permissions for managers or partners -->
                                @if ($seller->role === 1 || $seller->is_partner === 1 )
                                <div class="d-flex align-items-center">
                                    <button class="toggle-permissions btn btn-sm">
                                        <h6 class="fw-bold border border-info p-2 text-info">Permissions <i class="fa fa-eye"></i></h6>
                                    </button>
                                </div>

                                @foreach($permissions as $permission)
                                <div class="col-auto mb-1 permissionDiv" style="display:none;">
                                    <input type="checkbox" id="{{ $permission->name }}" name="permissions[]" value="{{ $permission->name }}" {{ $seller->hasPermissionTo($permission) ? 'checked' : '' }}>
                                    <label for="{{ $permission->name }}" class="ms-1">{{ $permission->name }}</label>
                                </div>
                                @endforeach
                                @endif



                                <!-- track order api setting -->
                                <div class="d-flex align-items-center">
                                    <button class="toggle-tracking-data btn btn-sm">
                                        <h6 class="fw-bold border border-info p-2 text-info">Tracking Api Data <i class="fa fa-eye"></i></h6>
                                    </button>
                                </div>
                                <div class="row trackingDataDiv" @if ($errors->any()) @else style="display: none;" @endif>

                                    <div class="d-flex align-items-center">
                                        <label for="trax_allow" class="fw-bold text-info">Update Trax Details</label>
                                        <input type="checkbox" id="trax_allow" {{$seller->trax_allow === 1 ? 'checked' : ''}} name="trax_allow" class="ms-2">
                                    </div>

                                    <!-- Trax details -->
                                    <div class="col-md-6"><x-x-input name="trax_api_key" value="{{$seller->trax_api_key}}" label="Api-Key" placeholder="Enter Api Key" /></div>
                                    <div class="col-md-6"><x-x-input name="trax_pickup_address_id" value="{{$seller->trax_pickup_address_id}}" label="Pickup-Address-id" placeholder="Enter Address-Id" /></div>

                                    <div class="d-flex align-items-center">
                                        <label for="postEx_allow" class="fw-bold text-info">Update Post-ex Details</label>
                                        <input type="checkbox" id="postEx_allow" {{$seller->postEx_allow === 1 ? 'checked' : ''}} name="postEx_allow" class="ms-2">
                                    </div>
                                    <!-- PostEx details -->
                                    <div class="col-md-6"><x-x-input name="postEx_apiToken" value="{{$seller->postEx_apiToken}}" label="Api-Token" placeholder="Enter Api Token" /></div>
                                    <div class="col-md-6">
                                        <label for="">Pickup-Address-Code</label>
                                        <select class="form-control" name="postEx_pickupAddressCode">
                                            <option {{ $seller->postEx_pickupAddressCode ? '' : 'selected' }} value="">Select City</option>
                                            @foreach($codes as $code)
                                                <option value="{{ $code->postEx_pickupAddressCode }}" {{ $seller->postEx_pickupAddressCode === $code->postEx_pickupAddressCode ? 'selected' : '' }}>
                                                    {{ $code->postEx_pickupAddressCode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- PostEx Nowshera details -->
                                    <div class="col-md-6"><x-x-input name="postEx_apiToken_nowshera" value="{{$seller->postEx_apiToken_nowshera}}" label="Api-Token (Nowshera)" placeholder="Enter Api Token" /></div>
                                    <div class="col-md-6">
                                        <label for="">Pickup-Address-Code (Nowshera)</label>
                                        <select class="form-control" name="postEx_pickupAddressCode_nowshera">
                                            <option {{ $seller->postEx_pickupAddressCode_nowshera ? '' : 'selected' }} value="">Select City</option>
                                            @foreach($codes as $code)
                                                <option value="{{ $code->postEx_pickupAddressCode }}" {{ $seller->postEx_pickupAddressCode_nowshera === $code->postEx_pickupAddressCode ? 'selected' : '' }}>
                                                    {{ $code->postEx_pickupAddressCode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <label for="mnp_alllow" class="fw-bold text-info">Update MNP Details</label>
                                        <input type="checkbox" id="mnp_alllow" {{$seller->mnp_alllow === 1 ? 'checked' : ''}} name="mnp_alllow" class="ms-2">
                                    </div>
                                    <!-- mnp details -->
                                    <div class="col-md-4"><x-x-input name="mnp_username" label="Mnp Username" value="{{$seller->mnp_username}}" placeholder="Enter UserName" /></div>
                                    <div class="col-md-4"><x-x-input name="mnp_password" label="Mnp Password" value="{{$seller->mnp_password}}" placeholder="Enter Password" /></div>
                                    <div class="col-md-4"><x-x-input name="locationID" label="Mnp LocationId" value="{{$seller->locationID}}" placeholder="Enter Location-Id" /></div>
                                </div>

                                <!-- reseller webiste setting -->
                                <div class="d-flex align-items-center">
                                    <button class="toggle-website-data btn btn-sm">
                                        <h6 class="fw-bold border border-info p-2 text-info">Website Setting <i class="fa fa-eye"></i></h6>
                                    </button>
                                </div>

                                <div class="row websiteSettingDataDiv" @if ($errors->any()) @else style="display: none;" @endif>
                                    <div class="mb-2">
                                        <label class="fw-bold text-info">Update Website Setting</label>
                                    </div>

                                    <!-- Colors details -->
                                    <div class="col-md-4">
                                        <label for="website_url">Website</label>
                                        <div class="form-group mb-3">
                                            <input type="text" id="website_url" value="{{$seller->website}}" name="website" class="form-control">
                                            <span class="text-danger">@error('website'){{$message}}@enderror</span>
                                        </div>
                                    </div>

                                    <!-- Colors details -->
                                    <div class="col-md-4">
                                        <label for="color_1">Background Color</label>
                                        <div class="input-group mb-3">
                                            <input type="color" value="{{$seller->color_1}}" id="color-picker" class="color-picker colo_picker_tab">
                                            <input type="text" value="{{$seller->color_1}}" placeholder="Enter Color Manual" id="color-input" name="color_1" class="form-control color-input">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="color_2">Category-Card Color</label>
                                        <div class="input-group mb-3">
                                            <input type="color" value="{{$seller->color_2}}" id="color-picker2" class="color-picker colo_picker_tab">
                                            <input type="text" value="{{$seller->color_2}}" placeholder="Enter Color Manual" id="color-input2" name="color_2" class="form-control color-input">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="color_2">Category-Card Text Color</label>
                                        <div class="input-group mb-3">
                                            <input type="color" value="{{$seller->color_3}}" id="color-picker3" class="color-picker colo_picker_tab">
                                            <input type="text" value="{{$seller->color_3}}" placeholder="Enter Color Manual" id="color-input3" name="color_3" class="form-control color-input">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="color_2">Other Screen Color</label>
                                        <div class="input-group mb-3">
                                            <input type="color" value="{{$seller->color_4}}" id="color-picker4" class="color-picker colo_picker_tab">
                                            <input type="text" value="{{$seller->color_4}}" placeholder="Enter Color Manual" id="color-input4" name="color_4" class="form-control color-input">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="color_2">Special Text Color</label>
                                        <div class="input-group mb-3">
                                            <input type="color" value="{{$seller->color_5}}" id="color-picker5" class="color-picker colo_picker_tab">
                                            <input type="text" value="{{$seller->color_5}}" placeholder="Enter Color Manual" id="color-input5" name="color_5" class="form-control color-input">
                                        </div>
                                    </div>
                                    <div class="row gy-2">
                                        <div class="col-auto">
                                            <div class="d-flex align-items-center">
                                                <label for="mute_video" class="fw-bold text-info">Product Video Mute</label>
                                                <input type="checkbox" id="mute_video" {{$seller->mute_video === 1 ? 'checked' : ''}} name="mute_video" class="ms-2">
                                            </div>
                                        </div>

                                        <div class="col-auto {{$seller->vhost ? '' : 'd-none' }}">
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-primary me-1" onclick="copyVhost()">Copy Vhost</button>
                                            <textarea class="form-control" style="height: auto !important;" id="vhostTextarea" rows="1">
<?php echo 'server {
    listen 80;
    listen [::]:80;
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    ssl_certificate /etc/letsencrypt/live/'.$seller->website.'/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/'.$seller->website.'/privkey.pem;
    server_name www.'.$seller->website.';
    return 301 https://'.$seller->website.'$request_uri;
    }

    server {
    listen 80;
    listen [::]:80;
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    ssl_certificate /etc/letsencrypt/live/'.$seller->website.'/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/'.$seller->website.'/privkey.pem;
    root /home/oyehoyebridalhouses/htdocs/oyehoyebridalhouses.com/public;
    server_name '.$seller->website.' www1.'.$seller->website.';

    {{nginx_access_log}}
    {{nginx_error_log}}

    if ($scheme != "https") {
        rewrite ^ https://$host$uri permanent;
    }

    location ~ /.well-known {
        auth_basic off;
        allow all;
    }

    {{settings}}

    location / {
        {{varnish_proxy_pass}}
        proxy_set_header Host $http_host;
        proxy_set_header X-Forwarded-Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_hide_header X-Varnish;
        proxy_redirect off;
        proxy_max_temp_file_size 0;
        proxy_connect_timeout      720;
        proxy_send_timeout         720;
        proxy_read_timeout         720;
        proxy_buffer_size          128k;
        proxy_buffers              4 256k;
        proxy_busy_buffers_size    256k;
        proxy_temp_file_write_size 256k;
    }

    location ~* ^.+\.(css|js|jpg|jpeg|gif|png|ico|gz|svg|svgz|ttf|otf|woff|woff2|eot|mp4|ogg|ogv|webm|webp|zip|swf|map)$ {
        add_header Access-Control-Allow-Origin "*";
        expires max;
        access_log off;
    }

    if (-f $request_filename) {
        break;
    }
}

server {
    listen 8080;
    listen [::]:8080;
    root /home/oyehoyebridalhouses/htdocs/oyehoyebridalhouses.com/public;
    server_name '.$seller->website.' www1.'.$seller->website.';

    try_files $uri $uri/ /index.php?$args;
    index index.php index.html;

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_intercept_errors on;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        try_files $uri =404;
        fastcgi_read_timeout 3600;
        fastcgi_send_timeout 3600;
        fastcgi_param HTTPS "on";
        fastcgi_param SERVER_PORT 443;
        fastcgi_pass 127.0.0.1:17002;
        fastcgi_param PHP_VALUE "
        error_log=/home/oyehoyebridalhouses/logs/php/error.log;
        memory_limit=5G;
        max_execution_time=180;
        max_input_time=300;
        max_input_vars=50000;
        post_max_size=5G;
        upload_max_filesize=5G;
        date.timezone=UTC;
        display_errors=off;";
    }


    if (-f $request_filename) {
        break;
    }
}'
?>
                                            </textarea>
                                        </div>
                                        </div>

                                    </div>

                                    <!--Thumbail-->
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <label>Logo</label>
                                            <input type="file" class="upd_image form-control" onchange="selected_preview()" id="selected_image" name="logo" accept="image/*">
                                        </div>
                                        <span class="text-danger">@error('image'){{$message}}@enderror</span>
                                    </div>
                                    <!--Thumbnail Image-->
                                    <div class="col-lg-auto {{$seller->logo != null ? '' : 'd-none'}}" id="upd_img_container">
                                        <div class="image-container pe-lg-5">
                                            <img src="{{$seller->logo}}" id="selected_frame" width="200px" height="200px" alt="seller_thumbnail" class="img img-responsive" />
                                        </div>
                                    </div>


                                </div>

                                <div class="mt-2">
                                    <button class="m-auto d-block btn btn-primary" type="submit">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

    </script>


    @endsection

    @section('scripts')
    <script>
        function copyVhost() {
            event.preventDefault();
            // Get the textarea element
            var textarea = document.getElementById("vhostTextarea");

            // Select the text inside the textarea
            textarea.select();

            // Copy the selected text to the clipboard
            document.execCommand("copy");

            // Deselect the text
            textarea.setSelectionRange(0, 0);
            toastr.success("Copied Successfully");

        }

        $(document).ready(function() {
            $('.toggle-permissions').click(function(e) {
                e.preventDefault();
                $('.permissionDiv').fadeToggle();
            });

            $('.toggle-tracking-data').click(function(e) {
                e.preventDefault();
                $('.trackingDataDiv').fadeToggle();
            });

            $('.toggle-website-data').click(function(e) {
                e.preventDefault();
                $('.websiteSettingDataDiv').fadeToggle();
            });

            $('.color-picker').change(function() {
                var color = $(this).val();
                var targetInputId = $(this).attr('id').replace('color-picker', '#color-input');
                $(targetInputId).val(color);
            });


            // $('#color-picker').change(function() {
            //     $('#color-input').val($(this).val());
            // });

            // $('#color-picker2').change(function() {
            //     $('#color-input2').val($(this).val());
            // });
        });
    </script>
    @endsection
