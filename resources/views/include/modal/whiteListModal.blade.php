<div class="modal fade" id="whiteList" tabindex="-1" aria-labelledby="AddLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                Select White List Items
                <button type="button" class="btn-close add_modal_close whiteModalCloseButton"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="search-form-modal" autocomplete="off">
                    <div class="row gy-2 justify-content-lg-end">
                        <div class="col-auto d-flex align-items-center">
                            <input type="search" id="search-input" class="form-control border border-dark"
                                placeholder="Serach Article">
                            <button type="submit"
                                class="ms-1 btn btn-outline-primary search-whitelist-btn">Search</button>
                        </div>
                    </div>
                </form>
                <div class="row mt-2" id="article-container">
                    {{-- @foreach ($whiteListProducts as $item)
                        <div class="col-auto mb-2 ">
                            <div class="badge bg-dark" style="font-size:15px;">
                                <span class="product-article" data-article-id="{{ $item->id }}">
                                    {{ $item->article }}</span>
                                <button class="btn btn-success increment-button"
                                    data-article-id="{{ $item->id }}">+</button>
                                <span class="px-1 product-count{{ $item->id }}" style="display: none">0</span>
                                <button class="btn btn-danger decrement-button" data-article-id="{{ $item->id }}"
                                    style="display: none">-</button>
                            </div>
                        </div>
                    @endforeach --}}
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Define an object to store the counts for each article.
        var articleCounts = {};
        let dataValue = null;

        // Use event delegation on the article-container
        $('#article-container').on('click', '.increment-button', function() {
            $('.decrement-button').prop('disabled', true);
            $('.increment-button').prop('disabled', true);
            var articleId = $(this).data('article-id');
            var $productCount = $('.product-count' + articleId);
            articleCounts[articleId] = articleCounts[articleId] || {
                count: 0,
                name: ''
            };
            articleCounts[articleId].count += 1;
            $productCount.text(articleCounts[articleId].count);
            $productCount.show();
            $(this).siblings('.decrement-button').show();
            articleCounts[articleId].name = $(`.product-article[data-article-id='${articleId}']`)
                .text();
            updateDescription();
        });

        // Click event for decrement button
        $('#article-container').on('click', '.decrement-button', function() {
            $('.decrement-button').prop('disabled', true);
            $('.increment-button').prop('disabled', true);
            var articleId = $(this).data('article-id');
            var $productCount = $('.product-count' + articleId);
            articleCounts[articleId] = articleCounts[articleId] || {
                count: 0,
                name: ''
            };
            articleCounts[articleId].count = Math.max((articleCounts[articleId].count || 0) - 1, 0);
            $productCount.text(articleCounts[articleId].count);

            if (articleCounts[articleId].count === 0) {
                $productCount.hide();
                $(this).hide();
            }

            articleCounts[articleId].name = $(`.product-article[data-article-id='${articleId}']`)
                .text();
            updateDescription();
        });
        // Event listener for the charges input field
        document.getElementById('charges').addEventListener('input', function() {
            var charges = parseFloat(this.value) || 0;
            var total = parseFloat($('#total').val()) || 0;
            var grandTotal = total + charges;

            $('#grandTotal').val(grandTotal);

            updateChargesInDescription(charges);
        });

        // Function to update the charges in the description
        function updateChargesInDescription(charges) {
            var description = 'Code # ';
            var totalCount = 0;
            var dataToSend = [];

            for (var article in articleCounts) {
                if (articleCounts[article].count > 0) {
                    var articleName = articleCounts[article].name;
                    description += articleName + ' (' + articleCounts[article].count + 'x), ';
                    totalCount += articleCounts[article].count;
                    dataToSend.push({
                        article_id: article,
                        article_name: articleName,
                        count: articleCounts[article].count
                    });
                }
            }

            // Remove trailing comma and update the description and total count fields
            description = description.replace(/,\s*$/, '');

            var whatsappNumber = $('#consignee_whatsaapp').val();
            description += ' Cell # ' + whatsappNumber;

            // Append the charges to the description after "DC -"
            var existingDescription = $('#generalDescription').val();
            var descriptionParts = existingDescription.split(' DC - ');
            if (descriptionParts.length === 2) {
                descriptionParts[1] = charges.toString();
                description = descriptionParts.join(' DC - ');
            }

            $('#generalDescription').val(description);
            $('#item_quantity').val(totalCount);
        }

        // Function to update the description based on counts
        function updateDescription() {
            var description = 'Code # ,';
            var totalCount = 0;
            var dataToSend = [];

            for (var article in articleCounts) {
                if (articleCounts[article].count > 0) {
                    var articleName = articleCounts[article].name;
                    description += articleName + ' (' + articleCounts[article].count + 'x), ';
                    totalCount += articleCounts[article].count;
                    dataToSend.push({
                        article_id: article,
                        article_name: articleName,
                        count: articleCounts[article].count
                    });
                }
            }

            // Remove trailing comma and update the description and total count fields
            description = description.replace(/,\s*$/, '');

            var completewhatsappNumber = $('#consignee_whatsaapp').val();
            var whatsappNumber = $('#consignee_whatsaapp').val();
            // if mnp then attech last 5 digits of whatsapp in order id
            var selectedCourier = $('input[name="courier_option"]:checked').val();
            var lastFiveDigits = "";
            if (selectedCourier === 'mnp') {
                lastFiveDigits = '-' + whatsappNumber.slice(-5);
            } else if (selectedCourier === 'postEx') {
                //lastFiveDigits = '/APP-' + whatsappNumber.slice(-5);
                whatsappNumber = whatsappNumber.slice(0, 4) + '--' + whatsappNumber.slice(6);
            } else {
                lastFiveDigits = "";
            }
            description += ' Cell # ' + whatsappNumber;

            $('#generalDescription').val(description);
            $('#item_quantity').val(totalCount);
            $('#transactionNotes').val(' Other Contact # ' + completewhatsappNumber);
            $.ajax({
                url: '{{ route('waoseller.order.calculateTotals') }}',
                method: 'GET',
                data: {
                    articles: dataToSend,
                    total_count: totalCount
                },
                dataType: 'json',
                contentType: 'application/json',
                success: function(response) {
                    console.log(response);

                    // Update per product charges details
                    $('#perProductCount').text(totalCount + ' x ' + response.data.perProductCharge);
                    var perProductAmount = totalCount * response.data.perProductCharge;
                    $('#perProductAmount').text(perProductAmount);

                    // Update order charges details
                    $('#orderCharges').text(response.data.perOrderCharge);

                    // Deduct order charges from purchase total and update display
                    var basicProductAmount = response.data.purchaseTotal - response.data
                        .perOrderCharge - perProductAmount;

                    // here set reseller deduct amount modal details
                    $('#basicProductCount').text('(' + totalCount + 'x)');
                    $('#basicProductAmount').text(basicProductAmount);

                    $('#totalAmountPurchse').text(response.data.purchaseTotal);
                    $('#purchaseTotal').val(response.data.purchaseTotal);
                    $('#purchaseTotalDisplay').val(response.data.purchaseTotal);

                    $('#total').val(response.data.total);
                    $('#charges').val(response.data.charges);
                    $('#grandTotal').val(response.data.grandTotal);
                    $('#grandProfit').val(response.data.grandProfit);
                    $('#order_id').val(
                        `${response.data.nextOrderId}/${response.data.grandProfit}/${totalCount}${lastFiveDigits}`
                    );

                    // Update the description with TotalBill and TotalDC
                    description += ' Bill - ' + response.data.total + ' DC - ' + response.data
                        .charges;
                    $('#generalDescription').val(description);
                    // enable inc/dec buttons
                    $('.decrement-button').prop('disabled', false);
                    $('.increment-button').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });


        }

        document.getElementById('consignee_whatsaapp').addEventListener('input', function() {
            let whatsappNumber = cleanNumber(this.value);
            this.value = whatsappNumber;
            handleOrderHistory(whatsappNumber, 'whatsapp');
        });

        // get user order history based on contactnumber
        document.getElementById('consignee_phone_number_1').addEventListener('input', function() {
            let phoneNumber = cleanNumber(this.value);
            this.value = phoneNumber;
            handleOrderHistory(phoneNumber, 'consignee_phone_number_1');
        });

        function cleanNumber(inputValue) {
            // Remove spaces, dashes, and country code
            return inputValue.replace(/^\+92/, 0).replace(/[\s+\-\(\)]/g, '');
        }

        var userCurrentDaysOrder = '';

        function handleOrderHistory(inputValue, $action) {
            var $viewOrdersButton = document.querySelector('.view_user_orders');
            var $noRecordText = document.querySelector('.noRecordText');

            if (!/^03\d{9}$/.test(inputValue)) {
                return;
            } else {
                // if action call for update on whatsapp
                if ($action === 'whatsapp') {
                    updateDescription(inputValue)
                }
                $.ajax({
                    url: "{{ route('waoseller.order.history') }}",
                    method: 'GET',
                    data: $action === 'whatsapp' ? {
                        whatsappNumber: inputValue
                    } : {
                        phoneNumber: inputValue
                    },
                    success: function(response) {
                        console.log(response);
                        var $consigneeAddressTextarea = document.querySelector(
                            'textarea[name="consignee_address"]');
                        var $consignee_name = document.querySelector(
                            'input[name="consignee_name"]');
                        var $phoneNumber = document.querySelector(
                            'input[name="consignee_phone_number_1"]');
                        var $whatsappNumber = document.querySelector(
                            'input[name="consignee_whatsaapp"]');
                        var $consigneeCityInput = document.querySelector(
                            'input[name="consignee_city_trax"]');
                        var $consigneeCityMnp = document.querySelector(
                            'input[name="consignee_city_mnp"]');
                        var $consigneeCityPostEx = document.querySelector(
                            'input[name="consignee_city_postEx"]');
                        var $answersDatalist = document.getElementById('answers');
                        var $ordersTable = document.getElementById('history-order-table');

                        // update variable of userCurrentday orders

                        if (response.status == 1) {
                            // if action is whatsapp then empty data
                            if ($action === 'whatsapp') {
                                $consigneeAddressTextarea.value = '';
                                $consignee_name.value = '';
                                $consigneeCityInput.value = '';
                                $consigneeCityMnp.value = '';
                                $consigneeCityPostEx.value = '';
                                $viewOrdersButton.classList.add('d-none');
                                $noRecordText.classList.remove('d-none');
                                userCurrentDaysOrder = '';
                            }
                        } else {

                            // console.log(response.data);
                            userCurrentDaysOrder = response.data.currentDayOrder;
                            $ordersTable.innerHTML = '';
                            response.data.latestOrders.forEach(function(order, index) {
                                let row = document.createElement('tr');
                                row.innerHTML = `
                            <td>${order.courier_tracking_id}</td>
                            <td class="fw-bold">${order.status}</td>
                            <td>${order.amount}</td>
                            <td>${order.charges}</td>
                            <td>${order.date}</td>
                        `;
                                $ordersTable.appendChild(row);

                                // Fill the consignee address textarea with the address from the first order
                                if (index === 0) {

                                    // Check if the status of the first order is "Returned" or "RETURNED"
                                    // if (order.status.toLowerCase() === 'returned' || order.status.toLowerCase() === 'returned') {
                                    //     userCurrentDaysOrders = 100;
                                    // }

                                    $consigneeAddressTextarea.value = order.userdetail
                                        .address;
                                    $consignee_name.value = order.userdetail.name;

                                    if ((!$phoneNumber.value) && $action === 'whatsapp') {
                                        $phoneNumber.value = order.userdetail.phone;
                                    } else if ((!$whatsappNumber.value) && $action ===
                                        'consignee_phone_number_1') {
                                        $whatsappNumber.value = order.userdetail.whatsapp;
                                    }
                                    updateDescription(order.userdetail.phone);

                                    $consigneeCityInput.value = order.citydetail ? order
                                        .citydetail.name : '';
                                    $consigneeCityMnp.value = order.citydetail ? order
                                        .citydetail.name : '';
                                    $consigneeCityPostEx.value = order.citydetail ? order
                                        .citydetail.name : '';

                                    $answersDatalist.querySelectorAll('option').forEach(
                                        option => {
                                            if (option.getAttribute('data-value') === (
                                                    order.citydetail ? order.citydetail
                                                    .trax : '')) {
                                                option.setAttribute('selected',
                                                    'selected');
                                            } else {
                                                option.removeAttribute('selected');
                                            }
                                        });
                                }
                            });
                            $noRecordText.classList.add('d-none');
                            $viewOrdersButton.classList.remove('d-none');
                        }
                    }
                });
            }


        }

        // Function to reset counts and update the UI
        function resetArticleCounts() {
            // Iterate over each article in the counts object
            for (var article in articleCounts) {
                if (articleCounts.hasOwnProperty(article)) {
                    // Reset count to 0
                    articleCounts[article].count = 0;

                    // Update UI
                    var $productCount = $('.product-count' + article);
                    $productCount.text(articleCounts[article].count);
                    $productCount.hide();

                    var $decrementButton = $('.decrement-button[data-article-id=' + article + ']');
                    $decrementButton.hide();
                }
            }

            // Set the entire object to null
            articleCounts = {};

            const viewOrdersButton = document.querySelector('.view_user_orders');
            viewOrdersButton.classList.add('d-none');
        }

        function makeSlip(orderId, whatsappNumber) {

            domtoimage.toJpeg(document.getElementById("box")).then((dataUrl) => {
                var slipImage = document.getElementById('slipImage');
                if (window.innerWidth >= 768) {
                    var formData = new FormData();
                    formData.append('image', dataUrl);
                    formData.append('order_id', orderId);

                    // Add CSRF token to the FormData
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('waoseller.slip.store') }}', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            console.log(response);
                            if (!response.ok) {
                                throw new Error('Network response was not okk');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data.imageUrl);
                            // change the image src
                            slipImage.src = data.imageUrl;

                            var slipAnchor = document.getElementById('slipAnchor');
                            slipAnchor.href = data.imageUrl;

                            $('#copyUrlButton').removeClass('d-none');
                            $('#copyUrlButton').val(
                                `https://alzulfiqar110.com/index.php?input=${whatsappNumber}`);
                        })
                        .catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                            alert(error);
                        });

                } else {
                    $('#slipAnchor').addClass('d-none');
                    $('#copyUrlButton').removeClass('d-none');
                    $('#copyUrlButton').val(
                        `https://alzulfiqar110.com/index.php?input=${whatsappNumber}`);
                }
                $('#box').addClass('d-none');
            });
        }

        //  order create form
        $(document).on('submit', '#orderdone', function(e) {
            e.preventDefault();

            var dispatcRoute = "";
            // Find the clicked button within the form
            var clickedButton = $(this).find(':submit:focus');
            var pickupAddressCode = clickedButton.data('pickup-address');

            var clickedButton = $(this).find('button:focus');
            if (clickedButton.hasClass('order_trax_btn')) {
                dispatcRoute = "{{ route('waoseller.order.store') }}";
            } else if (clickedButton.hasClass('order_mnp_btn')) {
                dispatcRoute = "{{ route('waoseller.order.storeMnp') }}";
            } else if (clickedButton.hasClass('order_warehouose_btn')) {
                dispatcRoute = "{{ route('waoseller.order.storeWareHouse') }}";
            } else if (clickedButton.hasClass('order_postEx_btn')) {

                if (pickupAddressCode === 'nowshera') {
                    // Set the value of the select element to 'nowshera'
                    $('#pickupAddressCode').val('nowshera');
                } else {
                    var postexApi = "{{ auth()->user()->postEx_pickupAddressCode }}";
                    $('#pickupAddressCode').val(postexApi);
                }
                dispatcRoute = "{{ route('waoseller.order.storePostEx') }}";
            }

            $('.order_trax_btn').prop('disabled', true);
            $('.order_mnp_btn').prop('disabled', true);
            $('.order_postEx_btn').prop('disabled', true);
            $('.order_warehouose_btn').prop('disabled', true);


            var formdata = new FormData($('#orderdone')[0]);
            var dataToSend = [];

            for (var article in articleCounts) {
                if (articleCounts[article].count > 0) {
                    var articleName = articleCounts[article].name;
                    dataToSend.push({
                        article_id: article,
                        article_name: articleName,
                        count: articleCounts[article].count
                    });
                }
            }

            // Include the selected articles data in the FormData object
            formdata.append('selected_articles', JSON.stringify(dataToSend));

            if (userCurrentDaysOrder) {

                var currentDayOrderStatus = userCurrentDaysOrder.status.toLowerCase();

                var swalTitle = '';
                var swalText = '';

                // Constructing order details HTML markup
                var orderDetails = `<div style="border: 1px solid #ccc; padding: 10px;">
                        <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Order ID:</strong> ${userCurrentDaysOrder.id}</p>
                            <p><strong>Total:</strong> ${userCurrentDaysOrder.grandTotal}</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Whatsapp:</strong> ${userCurrentDaysOrder.user_detail.whatsapp}</p>
                            <p><strong>Name:</strong> ${userCurrentDaysOrder.name}</p>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <p><strong>Status:</strong> ${userCurrentDaysOrder.status}</p>
                        </div>`;

                // Conditionally include additional information
                orderDetails += userCurrentDaysOrder.wao_seller_detail ? `
                                <h5 class="text-info">By Reseller From Admin</h5>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p><strong>Admin Name:</strong> ${userCurrentDaysOrder.wao_seller_detail.name}</p>
                                    <p><strong>Email:</strong> ${userCurrentDaysOrder.wao_seller_detail.email}</p>
                                </div>` :
                    userCurrentDaysOrder.wao_admin_detail ? `
                                <h5>By Web/App Admin</h5>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p><strong>Admin Name:</strong> ${userCurrentDaysOrder.wao_admin_detail.name}</p>
                                    <p><strong>Email:</strong> ${userCurrentDaysOrder.wao_admin_detail.email}</p>
                                </div>` :
                    ''; // Empty string if neither wao_seller_detail nor wao_admin_detail exists

                orderDetails += `</div>`;


                if (currentDayOrderStatus === 'returned') {
                    swalTitle = 'Last Returned Order Confirmation';
                    swalText =
                        'User last Order is returned. Are you sure you want to dispatch a new order?<br><br>' +
                        orderDetails;
                } else {
                    swalTitle = 'Same Day Order Confirmation';
                    swalText =
                        'You have existing orders for today. Do you still want to dispatch a new order?<br><br>' +
                        orderDetails;
                }

                // Ask for confirmation using SweetAlert
                Swal.fire({
                    title: swalTitle,
                    html: swalText,
                    icon: currentDayOrderStatus === 'returned' ? 'error' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, dispatch!',
                    cancelButtonText: 'No, cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        dispatchOrder(dispatcRoute, formdata);

                    } else {

                        $('.order_trax_btn').prop('disabled', false);
                        $('.order_mnp_btn').prop('disabled', false);
                        $('.order_postEx_btn').prop('disabled', false);

                        var pickupAddressCodeValue = $('#pickupAddressCode').val();
                        $('#orderdone :input:not([name="courier_option"],[name="estimated_weight"],[name="mnp_username"],[name="mnp_password"],[name="locationID"],[name="pickup_address_id"])')
                            .val('').prop('selectedIndex', 0);
                        // Restore the original value of pickupAddressCode
                        $('#pickupAddressCode').val(pickupAddressCodeValue);
                        resetArticleCounts();
                    }
                });
            } else {
                // user has not userCurrentDaysOrder, here proceed with the AJAX request directly
                dispatchOrder(dispatcRoute, formdata)
            }

        });

        function dispatchOrder(dispatcRoute, formdata) {
            formdata.append('dataValue', dataValue);
            $.ajax({
                type: "POST",
                url: dispatcRoute,
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $('.order_trax_btn').prop('disabled', false);
                    $('.order_mnp_btn').prop('disabled', false);
                    $('.order_postEx_btn').prop('disabled', false);
                    $('.order_warehouose_btn').prop('disabled', false);

                    if (response.status == 0) {
                        toastr.error(response.data);
                        // for seller
                    } else if (response.status == 2) {

                        var currentDate = new Date();
                        var formattedDate = (currentDate.getMonth() + 1) + '/' + currentDate
                            .getDate() + '/' + currentDate.getFullYear();
                        // make slip and set content in html
                        $('#box').removeClass('d-none');
                        var grandTotalValue = parseFloat($('#grandTotal').val());
                        $('#slip-order-type').text($('#postExOrderType').val());
                        $('#slip-order-id').text($('#order_id').val());
                        $('#slip-order-pieces').text($('#item_quantity').val());
                        $('#slip-order-charges').text($('#charges').val());
                        $('#slip-order-productDetail').text($('#generalDescription').val());
                        $('#slip-cod-amount').text(grandTotalValue.toFixed(2) + '/-');
                        $('#slip-order-cn').text(response.data.courier_tracking_id);
                        $('#slip-order-cons-city').text($('#consignee_city_postEx').val());
                        $('#slip-order-consignee_whatsaapp').text($('#consignee_whatsaapp').val());
                        $('#slip-order-cons-name').html($('#consignee_name').val() +
                            '<br>Contact #: ' + $('#consignee_phone_number_1').val());
                        $('#slip-order-consignee_address').text($('#consignee_address').val());
                        $('#slip-order-date').text(formattedDate);
                        $('#slip-order-remarks').text($('#transactionNotes').val());

                        if (response.status == 2) {
                            toastr.success(response.message);
                        } else {
                            Swal.fire({
                                title: "Order Dispatched",
                                text: response.message,
                                icon: "warning",
                                confirmButtonText: "OK",
                            })
                        }

                        // // generate slip
                        makeSlip(response.data.orderId, $('#consignee_whatsaapp').val());
                        // reset all form fileds expect these
                        // Save the current value of pickupAddressCode
                        var pickupAddressCodeValue = $('#pickupAddressCode').val();
                        $('#orderdone :input:not([name="courier_option"],[name="estimated_weight"],[name="mnp_username"],[name="mnp_password"],[name="locationID"],[name="pickup_address_id"])')
                            .val('').prop('selectedIndex', 0);
                        // Restore the original value of pickupAddressCode
                        $('#pickupAddressCode').val(pickupAddressCodeValue);

                        resetArticleCounts();
                    } else if (response.status == 3) {
                        toastr.error(response.data);

                    }
                    // for wareteam member
                    else if (response.status == 4) {
                        toastr.success(response.message);
                        $('#orderdone :input:not([name="courier_option"],[name="estimated_weight"],[name="mnp_username"],[name="mnp_password"],[name="locationID"],[name="pickup_address_id"])')
                            .val('').prop('selectedIndex', 0);
                        resetArticleCounts();
                    }
                },
            });
        }

        // Add a listener for the search form submission
        document.getElementById('search-form-modal').addEventListener('submit', function(e) {
            e.preventDefault();
            var searchQuery = document.getElementById('search-input').value;
            // Send an AJAX request to the server
            searchProductForWhiteList(searchQuery, dataValue);

        });

        $('.whiteModalCloseButton').on('click', function() {
            // Send an AJAX request to the server
            $('#search-input').val('');
            searchProductForWhiteList(null, dataValue);
            $('#whiteList').modal('hide');
        });

        function searchProductForWhiteList(searchQuery, forItem) {
            var articleContainer = document.getElementById('article-container');
            articleContainer.innerHTML = '';

            $.ajax({
                url: "{{ route('waoseller.search_whiteList') }}",
                method: 'GET',
                data: {
                    query: searchQuery,
                    value: forItem,
                },

                success: function(response) {
                    response.forEach(function(article) {
                        var articleId = article.id;
                        var countObj = articleCounts[articleId] || {
                            count: 0,
                            name: ''
                        };
                        var displayDecrement = countObj.count > 0 ? 'inline-block' : 'none';
                        var imageHtml = article.thumbnail ?
                            `<span><img src="${article.thumbnail}" alt="" width="45px" height="45px" class="rounded-circle me-1"></span>` :
                            '';

                        var articleDiv = document.createElement('div');
                        articleDiv.classList.add('col-auto', 'mb-2');
                        articleDiv.innerHTML = `
                            <div class="badge bg-dark" style="font-size:15px;">
                                <span class="product-article" data-article-id="${articleId}">${imageHtml}${article.article}</span>
                                <button class="btn btn-success increment-button" data-article-id="${articleId}">+</button>
                                <span class="px-1 product-count${articleId}" style="display: ${displayDecrement}">${countObj.count}</span>
                                <button class="btn btn-danger decrement-button" data-article-id="${articleId}" style="display: ${displayDecrement}">-</button>
                                    ${
                                        article.is_multan_list === 1 && forItem === 'multanItems'
                                        ? `<button class="btn btn-warning delete-button" data-article-id="${articleId}" title="Remove from Multan List"><i class="fa fa-trash"></i></button>`
                                        : ''
                                    }
                            </div>
                        `;

                        articleContainer.appendChild(articleDiv);
                    });

                    // Attach delete button functionality
                    document.querySelectorAll('.delete-button').forEach(function(button) {
                        button.addEventListener('click', function() {
                            var articleId = this.getAttribute('data-article-id');
                            if (confirm(
                                    'Are you sure you want to remove this item from the Multan list?'
                                    )) {
                                $.ajax({
                                    url: "{{ route('waoseller.update_multan_list') }}", // Your backend route
                                    method: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}", // Include CSRF token for security
                                        article_id: articleId,
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            toastr.success('Item removed successfully!');
                                            location.reload();
                                        } else {
                                            alert(
                                                'Failed to remove the item. Please try again.');
                                        }
                                    },
                                    error: function() {
                                        alert(
                                            'An error occurred while processing your request.');
                                    }
                                });
                            }
                        });
                    });
                }
            });
        }

        $('button[data-bs-toggle="modal"]').click(function() {
            const olddataValue = dataValue;
            dataValue = $(this).data('value');
            if(dataValue != olddataValue && olddataValue != null){
                resetArticleCounts();
                $('#orderdone')[0].reset();
            }
            searchProductForWhiteList(null, dataValue);
        });


    });
</script>
