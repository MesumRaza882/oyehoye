@php
$editing = isset($product) ? isset($product) : false;
$newproduct = false;
$randomStopFakeAfterQuantity = 0;
$randomStartFromFakeItems = 0;

@endphp

@if (Session::has('newlyAddedProduct'))
@php
$editing = true;
$newlyAddedProductId = Session::get('newlyAddedProduct');
$product = App\Models\Product::find($newlyAddedProductId);
$newproduct = Session::get('newlyAddedProduct');
$product_fake_range = App\Models\LockedkFolderPassword::where('text_password', 'update_product_fake_sold_range')->first();
// Determine the value for randomStopFakeAfterQuantity
if ($product_fake_range->stop_increasing_after_qty_start != 0 && $product_fake_range->stop_increasing_after_qty_end != 0) {
    $randomStopFakeAfterQuantity = rand($product_fake_range->stop_increasing_after_qty_start, $product_fake_range->stop_increasing_after_qty_end);
}

if ($product_fake_range->start_from_items_start != 0 && $product_fake_range->start_from_items_end != 0) {
    $randomStartFromFakeItems = rand($product_fake_range->start_from_items_start, $product_fake_range->start_from_items_end);
}
@endphp
@endif

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: red;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: green;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>


<!-- Product Price and detail -->
<div class="row">
    <div class="col-md-4">
        <x-x-input label="Name" type="text" name="name" required placeholder="Enter Product Name" value="{!! old('name', ($editing  ? $product->name : '')) !!}" />
    </div>
    <!--Article-->
    <div class="col-md-4">
        <x-x-input label="Add Article" type="text" name="article" required placeholder="Enter Article" value="{!! old('article', ($editing ? $product->article : '')) !!}" />
    </div>
    <!--Price-->
    <div class="col-md-4">
        <x-x-input label="Price" type="number" min="0" name="price" required placeholder="Enter Price" value="{{ old('price', ($editing ? $product->price : '')) }}" />
    </div>
</div>

<!-- Video details and pricing -->
<div class="row">

    <!-- Video -->
    <div class="col-md-4">
        @if ($editing && $product->video &&(!$newproduct))
        <x-x-input for="videoUpload" label="Upload Video" type="file" name="video" id='videoUpload' accept="video/*" />
        <div id="video_container">
            <video width="100%" height="150px" controls>
                <source src="{{$product->video}}" type="video/{{ pathinfo($product->video, PATHINFO_EXTENSION)}}">
                <source src="{{pathinfo($product->video, PATHINFO_FILENAME)}}.ogg" type="video/ogg" />
                Your browser does not support the video tag.
            </video>
        </div>
        @else
        <x-x-input for="videoUpload" label="Upload Video" type="file" name="video" id='videoUpload' accept="video/*" />
        <div id="video_container" style="display:none">
            <video width="100%" height="150px" controls>
                <source src="{{ old('video') }}" type="video/{{ pathinfo(old('video'), PATHINFO_EXTENSION) }}">
                Your browser does not support the video tag.
            </video>
        </div>
        @endif

    </div>
    <!--Thumbail-->
    <div class="col-md-4">
        @if ($editing && $product->thumbnail &&(!$newproduct))
        <x-x-input for="thumbnail" label="Video Thumbnail" type="file" name="thumbnail" id='thumbnail' accept="image/*" />
        <div class="col-6 col-lg-3 img_preview_container">
            <img src="{{$product->thumbnail}}" width="150px" height="150px" id="frame" class="img" />
        </div>
        @else
        <x-x-input for="thumbnail" label="Video Thumbnail" type="file" name="thumbnail" id='thumbnail' accept="image/*" />
        <div class="col-6 col-lg-3 img_preview_container" style="display:none">
            <img class="img" width="150px" height="150px" src="" id="frame">
        </div>
        @endif
    </div>

    <!--Product category-->
    <div class="col-md-4">
        @if ($editing)
        <x-x-select name="category_id" :options="$categories" :selected="$product->category_id" label="Select Category" required />
        @else
        <x-x-select name="category_id" :options="$categories" :selected="old('category_id')" label="Select Category" required />
        @endif
    </div>

    <!-- real price -->
    <div class="col-md-4">
        <x-x-input label="Real Purchase" type="number" min="0" name="purchase" required placeholder="Enter Real Purchase Price" value="{{ old('purchase', ($editing ? $product->purchase : '')) }}" />
    </div>

    <!-- net profit -->
    <div class="col-md-4">
        <x-x-input label="Net Profit" type="number" min="0" name="profit" required placeholder="Enter profit" value="{{ old('profit', ($editing ? $product->profit : '')) }}" />
    </div>

    <!--quantity-->
    <div class="col-md-4">
        <x-x-input label="Quantity" type="number" min="0" name="soldItem" required placeholder="Enter Product Quantity" value="{{ old('soldItem', ($editing ? $product->soldItem : '')) }}" />
    </div>

</div>

<!--Solditem----------------------------------->
<div class="row align-items-center mb-2">

    <!-- per min added fake products -->
    <div class="col-md-4">
        <x-x-input label="Increase Fake SoldItem Per Minute" type="number" min="0" name="increase_perMin" required placeholder="Enter Minutes" value="{{ old('increase_perMin', ($editing ? $product->increase_perMin : '')) }}" />
    </div>

    <!-- stop per min added fake products -->
    <div class="col-md-4">
        <x-x-input label="Stop Increasing Fake item After Quantity" type="number" min="0" name="stop_fake_after_quantity" required placeholder="Enter Stop Minutes" value="{{ $randomValues['randomStopFakeAfterQuantity'] ? $randomValues['randomStopFakeAfterQuantity'] : $randomStopFakeAfterQuantity }}" />
    </div>

    <!-- fake sold items by admin added -->
    <div class="col-md-4">
        <x-x-input label="Start From Fake Sold Items" type="number" name="soldAdm" required placeholder="Enter Start Fake Sold Items" value="{{ $randomValues['randomStartFromFakeItems'] ? $randomValues['randomStartFromFakeItems'] : $randomStartFromFakeItems }}" />
    </div>

    <!--Normal or Seller User Produuct-->
    @php
    $show_point = (object) [
    (object)['id' => 1, 'name' => 'Normal Product'],
    (object)['id' => 2, 'name' => 'Seller Product'],
    (object)['id' => 3, 'name' => 'Both User Product'],
    ];

    @endphp
    <div class="col-md-4">
        @if ($editing)
        <x-x-select name="show_point" :options="$show_point" :selected="$product->show_point" label="Select Show Point" required />
        @else
        <x-x-select name="show_point" :options="$show_point" :selected="old('show_point')" label="Select Show Point" required />
        @endif
    </div>

    <div class="col-md-4 d-flex align-items-center">
        <label for="product_status">Is Published:</label>
        @if ($editing)
        <input class="ms-2" type="checkbox" id="product_status" name="product_status" {{ old('product_status', $product->product_status === 'published') ? 'checked' : '' }}>
        @else
        <input class="ms-2" type="checkbox" id="product_status" name="product_status" {{ old('product_status') }} checked>
        @endif
    </div>

    <!-- Extra Fields button -->
    <div class="col-md-12">
        <button class=" btn btn-sm btn-primary view_extra_fields_product">Extra Fields</button>
    </div>
</div>


<!--extra fields-->
<div class="row  extra_fields_product" style="display:none;">
    <h6 class="mt-2">For Website</h6>
    <!-- product upload for specific user superadmin/manager/reseller/all -->
    <div class="col-md-4 ps-0">
        @if ($editing)
        <x-filter.x-record-select label="Product UploaresellerAppd For" name="product_upload_for" :options="[1 => 'For All', 2 => 'Only For Super Admin', 3 => 'Only For Resellers', 6 => 'Only For Partners', 4 => 'Only For Managers', 5 => 'For Super Admin + Partners']" :selected="$product->product_upload_for" />
        @else
        <x-filter.x-record-select label="Product Upload For" name="product_upload_for" :options="[1 => 'For All', 2 => 'Only For Super Admin', 3 => 'Only For Resellers', 6 => 'Only For Partners', 4 => 'Only For Managers', 5 => 'For Super Admin + Partners']" :selected="0" />
        @endif
    </div>

    <!-- specific reseller product price -->
    <div class="col-md-4">
        <x-x-input label="Specific Reseller Price" type="number" min="0" name="reseller_price" placeholder="Enter Specific Price" value="{{ old('reseller_price', ($editing ? $product->reseller_price : '')) }}" />
    </div>

    <!--select partners for specific profit-->
    <div class="col-md-4 mb-2">
        <label for="partners">Select Partners for Specific Profit:</label>
        <select name="resellers[]" id="partners" class="form-control" style="height: auto !important;" multiple>
            @if ($editing)
            @foreach ($partners as $admin)
            <option value="{{ $admin->id }}" {{ in_array($admin->id, $product->resellerSpecificProduct->pluck('admin_id')->toArray()) ? 'selected' : '' }}>
                {{ $admin->name }}
            </option>
            @endforeach
            @else
            @foreach ($partners as $admin)
            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
            @endforeach
            @endif
        </select>
    </div>

    <!-- specific net profit -->
    <div class="col-md-4">
        <x-x-input label="Specific Net Profit for Partners" type="number" min="0" name="specific_reseller_profit" placeholder="Enter specific profit" value="{{ old('specific_reseller_profit', ($editing ? $product->specific_reseller_profit : '')) }}" />
    </div>

    <div class="col-md-4 mb-2">
        <label for="app_resellers">Select Apps:</label>
        @foreach ($app_resellers as $admin)
        <div class="clearfix">
            <div class="float-left">{{ $admin->name }}</div>
            <label class="switch float-end">
                <input type="checkbox" name="app_resellers[]" value="{{ $admin->id }}" checked>
                <span class="slider round"></span>
            </label>
        </div>
        @endforeach
        {{-- <select name="app_resellers[]" id="app_resellers" class="form-control" style="height: auto !important;" multiple>
            @foreach ($app_resellers as $admin)
                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
        @endforeach
        </select> --}}
    </div>


    <h6 class="mt-2">Extra Setting For Product</h6>
    <!--exceed_limit-->
    <div class="col-md-4">
        <x-x-input label="Customer Buying Item Limit Quantity" type="number" name="exceed_limit" placeholder="Enter Exceed Limit" value="{{ old('exceed_limit', ($editing ? $product->exceed_limit : '')) }}" />
    </div>

    <!--discount-->
    <div class="col-md-3">
        <x-x-input label="Discount" type="number" name="discount" placeholder="Enter Discount" value="{{ old('discount', ($editing ? $product->discount : '')) }}" />
    </div>
    <!--is_dc_free-->
    <div class="col-md-3">
        @if ($editing)
        <x-filter.x-record-select label="Is Dc Free" name="is_dc_free" :options="[0 => 'No', 1 => 'DC Free']" :selected="$product->is_dc_free" />
        @else
        <x-filter.x-record-select label="Is Dc Free" name="is_dc_free" :options="[0 => 'No', 1 => 'DC Free']" :selected="0" />
        @endif
    </div>

    <!--is_hide_to_new_arrival-->
    <div class="col-md-3">
        @if ($editing)
        <x-filter.x-record-select label="Hide to New-Arrival" name="hide_to_new_arrival" :options="[0 => 'No', 1 => 'Hide']" :selected="$product->hide_to_new_arrival" />
        @else
        <x-filter.x-record-select label="Hide to New-Arrival" name="hide_to_new_arrival" :options="[0 => 'No', 1 => 'Hide']" :selected="0" />
        @endif
    </div>
    <!--is_added_to_lock_folder-->
    <div class="col-md-3">
        @if ($editing)
        <x-filter.x-record-select label="Lock Folder" name="is_locked" :options="[0 => 'No', 1 => 'Added']" :selected="$product->is_locked" />
        @else
        <x-filter.x-record-select label="Lock Folder" name="is_locked" :options="[0 => 'No', 1 => 'Added']" :selected="0" />
        @endif
    </div>
</div>