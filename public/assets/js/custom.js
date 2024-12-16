// delete Record
$(document).on('click', '.deleletRecordIconButton', function (e) {
    e.preventDefault();
    var id = $(this).val();
    if (id) {
        $('#deleteRecordId').val(id);
    }
    $('#DeleteModalRecord').modal('show');
});

$('.delete-button').click(function (e) {
    e.preventDefault();

    const deleteForm = $(this).closest('form');
    const dataName = $(this).data('name');

    Swal.fire({
        title: `Are you sure to delete "${dataName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteForm.submit();
        }
    });
});

$(document).on('submit', '#deleteRecordForm', function (e) {
    e.preventDefault();


    let formdata = new FormData($('#deleteRecordForm')[0]);
    let deleteRoute = $(this).data('delete-route');
    $.ajax({
        type: "POST",
        url: deleteRoute,
        data: formdata,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response);
            if (response.status == 2) {
                toastr.success(response.data);
                window.location.reload(true);
            }
        },
    });
});

// add Record
$(document).on('submit', '#addNewRecordForm', function (e) {
    e.preventDefault();

    let formdata = new FormData($('#addNewRecordForm')[0]);
    let addRoute = $(this).data('add-route');
    $.ajax({
        type: "POST",
        url: addRoute,
        data: formdata,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response);
            if (response.status == 0) {
                toastr.error(response.data);
            } else if (response.status == 2) {
                toastr.success(response.data);
                window.location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // You can handle the error here, such as displaying an error message to the user
            toastr.error('An error occurred while processing your request. Please try again later.');
        }
    });
});

// delete
$('.delete-button').click(function (e) {
    e.preventDefault();

    const dataRoute = $(this).data('route');
    const dataName = $(this).data('name');

    Swal.fire({
        title: `Are you sure to delete "${dataName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: dataRoute,
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        Swal.fire(
                            'Deleted!',
                            `The "${dataName}" has been deleted.`,
                            'success'
                        ).then(() => {

                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the category.',
                            'error'
                        );
                    }
                },
                error: function () {
                    Swal.fire(
                        'Error!',
                        'An error occurred while processing your request.',
                        'error'
                    );
                }
            });
        }
    });
});

// reset search form
$('#reset-button').click(function () {
    $('#search-form')[0].reset();
    $('#search-form :input').val('').prop('selectedIndex', 0);
    $('#search-form').submit();

});

$('#search-form select').change(function () {
    $('#search-form').submit(); // Submit the form
});

var delayTimer;
// Trigger form submission when any input field inside the form is modified
$('#search-form input').on('input', function () {
    clearTimeout(delayTimer); // Clear the previous timer

    // Set a new timer to submit the form after 1 second (1000 milliseconds)
    delayTimer = setTimeout(function () {
        $('#search-form').submit(); // Submit the form
    }, 2000);
});


// get single order track history
$(".track-order-btn").on('click', function (e) {
    e.preventDefault();
    var orderId = $(this).data('order-id');
    var trackRoute = $(this).data('track-route');
    var rowElement = $(this).closest('tr');
    updateOrderStatus(orderId, rowElement, trackRoute);
});

// mnp track order
$(".track-mnp-order-btn").on('click', function (e) {
    e.preventDefault();
    var orderId = $(this).data('order-id');
    var trackRoute = $(this).data('track-route');
    var rowElement = $(this).closest('tr');
    updateOrderStatus(orderId, rowElement, trackRoute);
});

// mnp track order
$(".track-postEx-order-btn").on('click', function (e) {
    e.preventDefault();
    var orderId = $(this).data('order-id');
    var trackRoute = $(this).data('track-route');
    var rowElement = $(this).closest('tr');
    updateOrderStatus(orderId, rowElement, trackRoute);
});

function updateOrderStatus(orderId, rowElement, trackRoute) {
    $.ajax({
        type: "GET",
        url: trackRoute,
        data: {
            _token: '{{ csrf_token() }}',
            orderId: orderId,
        },
        success: function (response) {
            console.log(response);
            // $('.track-order-btn, .track-mnp-order-btn').prop('disabled', false);
            if (response.status === 2) {

                $('#orderHistoryTableBody').empty();

                if (Array.isArray(response.data.history) && response.data.history.length === 0) {
                    var row = '<tr><td>Dispatched</td><td>' + response.data.courier_date + '</td></tr>';
                    $('#orderHistoryTableBody').append(row);
                }
                else {
                    for (var i = 0; i < response.data.history.length; i++) {
                        var historyItem = response.data.history[i];
                        var row = '<tr><td>' + historyItem.history + '</td><td>' + historyItem.time + '</td></tr>';
                        $('#orderHistoryTableBody').append(row);
                    }
                }

                $('#orderHistoryModal').modal('show');

                var statusCell = rowElement.find('.status-cell');
                var courierDate = rowElement.find('.courierDate');
                var cancelButton = rowElement.find('.cancelOrderButton');

                if (response.data.status === 'Team Review your Order' || response.data.status === 'DISPATCHED' || response.data.status === 'Dispatched') {
                    cancelButton.disabled = true;
                } else {
                    cancelButton.disabled = false;
                }

                statusCell.text(response.data.status);
                courierDate.text(response.data.courier_date);
                toastr.success(response.message);
            } else if (response.status === 1) {
                toastr.error(response.message);
            }
        },
    });
}

// cancel order by reseller/admin
$(document).on('click', '.cancelOrderButton', function (e) {
    e.preventDefault();
    var button = $(this);
    var form = $(this).closest('form');
    Swal.fire({
        title: 'Are you sure to Cancel Order?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            button.prop('disabled', true);
            form.submit();
        }
    });
});


// order delete by slected checkbox
$(document).on('click', '.deleteAllbtnOrders', function (e) {
    e.preventDefault();
    var x = confirm("Are you sure you want to delete Orders?");
    if (x) {
        var allids = [];

        $('input[name="cat_checkbox"]:checked').each(function () {
            allids.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: '/admin/order/delSelectedOrders',
            data: {
                ids: allids,
            },

            success: function (response) {
                toastr.success('Deleted Duccessfully');
                window.location.reload();
            },

        });
    }
});


