<!-- Product Modal -->
<div class="modal fade" id="productModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Search Form -->
                <form id="search-form-modal" autocomplete="off">
                    <div class="row mb-3">
                        <div class="col-auto d-flex align-items-center">
                            <input type="search" id="search-input" class="form-control border border-dark"
                                placeholder="Search By Article">
                            <button type="submit"
                                class="ms-1 btn btn-sm btn-outline-primary search-whitelist-btn">Search</button>
                        </div>
                    </div>
                </form>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Product List -->
                <div id="productList" class="row g-2">
                    <!-- Dynamic product items will be loaded here via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submitOrder" class="btn btn-primary">Add Products to Order</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        let dataValue = null;
        // Event listeners for search form submission and button click
        $('#search-form-modal').submit(function(e) {
            e.preventDefault();
            triggerSearch(dataValue);
        });

        $('.search-whitelist-btn').click(function(e) {
            e.preventDefault();
            triggerSearch(dataValue);
        });

        // Function to handle product search and data display
        function triggerSearch(value) {
            let searchQuery = $('#search-input').val();
            searchProductForWhiteList(searchQuery,value);
        }

        function searchProductForWhiteList(searchQuery,value) {

            $('#productList').empty(); // Clear current list

            $.ajax({
                url: "{{ route('waoseller.search_whiteList') }}",
                method: 'GET',
                data: {
                    query: searchQuery,
                    value:value,
                },
                success: function(response) {

                    // Loop through response data and append products
                    response.forEach(function(product) {
                        $('#productList').append(`
                        <div class="product-item col-lg-4" data-product-id="${product.id}">
                            <div class="border border-light p-2 ">
                                <h6> <span><img src="${product.thumbnail}" alt="" width="45px" height="45px" class="rounded-circle me-1"></span> ${product.article}</h6>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary decrement-btn" type="button">-</button>
                                    <input type="number" class="mx-1 form-control qty-input" value="0" min="1">
                                    <button class="btn btn-outline-secondary increment-btn" type="button">+</button>
                                </div>
                            </div>
                        </div>
                    `);
                    });

                    // Re-bind increment and decrement functionality for new elements
                    bindQuantityButtons();
                },
                error: function() {
                    alert('Error fetching products.');
                }
            });
        }

        // Increment and Decrement functionality
        function bindQuantityButtons() {
            $('.increment-btn').off('click').on('click', function() {
                let qtyInput = $(this).siblings('.qty-input');
                qtyInput.val(parseInt(qtyInput.val()) + 1);
            });

            $('.decrement-btn').off('click').on('click', function() {
                let qtyInput = $(this).siblings('.qty-input');
                let currentQty = parseInt(qtyInput.val());
                if (currentQty > 1) {
                    qtyInput.val(currentQty - 1);
                }
            });
        }

        // Submit Order
        $('#submitOrder').click(function() {
            let orderData = [];
            $('#productList .product-item').each(function() {
                let productId = $(this).data('product-id');
                let qty = parseInt($(this).find('.qty-input').val());

                // Only add products with a quantity greater than zero
                if (qty > 0) {
                    orderData.push({
                        product_id: productId,
                        qty: qty
                    });
                }
            });

            // Check if orderData is empty
            if (orderData.length === 0) {
                // Show a toaster message if no product quantity is greater than zero
                toastr.error('Please enter a quantity greater than zero for at least one product.');
                return; // Exit the function, skipping the AJAX call
            }

            $(this).prop('disabled', true);

            $.ajax({
                url: "{{ route('updateOrderProducts') }}",
                type: "POST",
                data: {
                    order_id: {{ $order->id }},
                    products: orderData,
                    dataValue: dataValue,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        toastr.success("Update Successfully");
                        location.reload(); // Reload the page on success
                    } else {
                        alert('Failed to update order.');
                    }
                },
                error: function() {
                    alert('Error submitting order.');
                },
                complete: function() {
                    // Re-enable the button after the AJAX call is complete
                    $('#submitOrder').prop('disabled', false);
                }
            });
        });



        // Open modal and fetch items when button is clicked
        $('button[data-bs-toggle="modal"]').click(function() {
            // Get the data-value of the clicked button
            dataValue = $(this).data('value');

            triggerSearch(dataValue);
            // Initialize bindings for increment and decrement buttons
            bindQuantityButtons();
        });

    });
</script>
