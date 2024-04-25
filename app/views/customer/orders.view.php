<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/navbar/customer-navbar.php');

?>

<div class="container flex-d flex-md-c justify-content-center mt-5 bg-color-primary">
    <div class=" col-lg-10 flex-d-c gap-2 mt-5 ">

        <div class="card card-normal ">

        <h2 class="justify-content-center flex-d"> Orders </h2>

            <div class="section-switch flex-d  gap-3 flex-wrap" >

                            <button class="btn-selected" id="all">All</button>
                            <button class="btn-selected" id="unpaid">Unpaid</button>

                            <button class="btn-selected" id="pending">Pending</button>
                            <button class="btn-selected" id="upcoming">Upcoming</button>
                            <button class="btn-selected" id="rented">Rented</button>
                            <button class="btn-selected" id="completed">Completed</button>
                            <button class="btn-selected" id="cancelled">Cancelled</button>


                            <!-- not rented yet -->

                        </div>

            <div class="row gap-2 ">
                <!-- scrollable cart items -->
                <!-- <div class="col-lg-12    " id="cart-items"> -->
                <div class="col-lg-12 checkout-items overflow-scroll " >

                    <div id="orders">
    
    
    
    
    
    
    
                    </div>

                </div>


            </div>

           
        </div>
    </div>
</div>






<script>

    // function orderLoadScript(){

    //   var viewButtons = document.querySelectorAll('#view-button');

    //     var orderItemModal = document.getElementById("order-item-modal");
    //     var orderData = document.getElementById("order-data");

    //     // When the user clicks the button, open the modal

    //     viewButtons.forEach(function(button) {
    //         button.addEventListener('click', function() {
    //             orderItemModal.style.display = "block";
    //             var orderId = button.closest('.card').getAttribute('data-id');
    //             $.ajax({
    //                 url: '<?= ROOT_DIR ?>/myOrders/viewOrder/' + orderId,
    //                 headers: {
    //                     'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
    //                 },
    //                 type: 'GET',
    //                 success: function(data) {
    //                     orderData.innerHTML = data;
    //                 }
    //             });
    //         });
    //     });
    // }


</script>



<script>
    $(document).ready(function() {
        // loadOrders();
        loadOrders('all');
        $('.section-switch button').click(function() {
            $('.section-switch button').removeClass('active');
            $(this).addClass('active');
            var status = $(this).attr('id');
            loadOrders(status);
        });




    });

    function loadOrders(status = 'all') {
        window.currentStatus = status;
        showLoader();
        
        $.ajax({
            url: '<?= ROOT_DIR ?>/myOrders/list/' + status,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'GET',
            success: function(data) {
                $('#orders').html(data);
                hideLoader();
            },
            error: function(data) {
                console.log(data);
                alertmsg('Error loading orders', 'error');
                hideLoader();
            }
        });

    }



    $(document).on('click','.order-view-button', function() {
        showLoader();
        var orderId = $(this).closest('.card').attr('data-id');
        console.log(orderId);
        $.ajax({
            url: '<?= ROOT_DIR ?>/myOrders/viewOrder/' + orderId,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'GET',
            success: function(data) {
                $('#order-data').html(data);
                $('#order-item-modal').show();
                hideLoader();
            },
            error: function(data) {
                console.log(data);
                alertmsg('Error loading order details', 'error');
                hideLoader();
            }

        });
    });


    // Cancel 
    $(document).on('click','.order-cancel-button', function() {
    //    Open modal
        var orderId = $(this).closest('.card').attr('data-id');
        console.log(orderId);
        
        $('#confirm-cancel-modal').show();
        $('#confirm-cancel').attr('data-id', orderId);


    });

    // Confirm Cancel
    $(document).on('click','#confirm-cancel', function() {
        showLoader();
        var orderId = $(this).attr('data-id');
        console.log(orderId);
        $.ajax({
            url: '<?= ROOT_DIR ?>/myOrders/cancelOrder/' + orderId,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'GET',
            success: function(data) {
                $('#confirm-cancel-modal').hide();
                loadOrders();
                hideLoader();
            },
            error: function(data) {
                console.log(data);
                alertmsg('Error cancelling order', 'error');
                hideLoader();
            }

        });
    });


    // Mark as Rented Modal 
    $(document).on('click','.order-rent-button', function() {
        var orderId = $(this).closest('.card').attr('data-id');
        console.log(orderId);
        $('#mark-as-rented-modal').show();
        $('#mark-as-rented-confirm').attr('data-id', orderId);
    });

    // Mark as Rented Confirm
    $(document).on('click','#mark-as-rented-confirm', function() {
        showLoader();
        var orderId = $(this).attr('data-id');
        console.log(orderId);
        $.ajax({
            url: '<?= ROOT_DIR ?>/api/myOrders/markAsRentedByCustomer/' + orderId,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'GET',
            success: function(data) {
                $('#mark-as-rented-modal').hide();
                loadOrders(window.currentStatus);
                hideLoader();
            },
            error: function(data) {
                console.log(data);
                alertmsg('Error marking as rented', 'error');
                hideLoader();
            }
            
        });
    });

    // Report Modal
    $(document).on('click','.order-report-button', function() {
        var orderId = $(this).closest('.card').attr('data-id');

        console.log(orderId);
        $('#report-modal').show();
        $('#report-submit').attr('data-id', orderId);
        //  <span id="report-order-id"></span>
        $('#report-order-id').html("Report for Order ID: " + orderId);


    });

    // Report Submit
    $(document).on('click','#report-submit', function() {
        // prevent the submit button
        event.preventDefault();
        showLoader();
        var orderId = $(this).attr('data-id');
        // var report = $('#report-text').val();
        var title = $('#report-title').val();
        var description = $('#report-description').val();
        console.log(orderId);
        console.log(title);
        console.log(description);
        $.ajax({
            url: '<?= ROOT_DIR ?>/api/myOrders/reportOrder/' + orderId,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify (
                {
                    rent_id: orderId,
                    title: title,
                    description: description
                }
            ),
            success: function(data) {
                $('#report-modal').hide();
                alertmsg('Report submitted successfully', 'success');
                loadOrders(window.currentStatus);
                hideLoader();
            },
            error: function(data) {
                alertmsg('Error submitting report', 'error');
                console.log(data);
                hideLoader();
            }
            
        });
    });

    // full pay button
    $(document).on('click','.order-fullpay-button', function() {
        var orderId = $(this).closest('.card').attr('data-id');
        console.log(orderId);
        showLoader();
        // open pay modal
        $.ajax({
            url: '<?= ROOT_DIR ?>/myOrders/fullpay/' + orderId,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },

            type: 'GET',
            success: function(data) {
                $('#pay-data').html(data);
                $('#pay-modal').show();
                hideLoader();
            },
            error: function(data) {
                console.log(data);
                alertmsg('Error loading payment details', 'error');
                hideLoader();
            }

        });

    });

    // full pay confirm
    $(document).on('click','#full-pay-confirm', function() {
        showLoader();
        var orderId = $(this).attr('data-id');
        console.log(orderId);
        $.ajax({
            url: '<?= ROOT_DIR ?>/api/pay/fullPay/' + orderId,
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'GET',
            success: function(data) {
                console.log(data);
                paymentGateWay(data.data);
                hideLoader();
                
            },
            error: function(data) {
                console.log(data);
                alertmsg('Error making payment', 'error');
                hideLoader();
            }

        });
    });



    function paymentGateWay(data) {
            console.log("Payment gateway");
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = () => {
                console.log(xhttp.readyState);
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    // alert(xhttp.responseText);

                    // Payment completed. It can be a successful failure.
                    payhere.onCompleted = function onCompleted(orderId) {
                        console.log("Payment completed. OrderID:" + orderId);
                        showLoader();

                        // notify
                        $.ajax({
                            url: "<?php echo ROOT_DIR ?>/api/pay/notify", // URL to your PHP script that will handle the notification
                            type: "POST", // Use POST method
                            headers: {
                                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                            },
                            data: {
                                merchant_id: data.merchant_id,
                                order_id: data.orderId,
                                payhere_amount: data.amount,
                                payhere_currency: "LKR",
                                status_code: 2,
                                md5sig: data.hash
                            }, // Send the data as part of the request
                            success: function(response) {
                                console.log("Notification sent. Server responded with: ", response);
                                alertmsg("Payment successful", "success");

                                setTimeout(() => {
                                    window.location.href = "<?php echo ROOT_DIR ?>/myOrders";

                                    hideLoader();

                                }, 1000);


                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log("Error sending notification: ", textStatus, errorThrown);
                                alertmsg("Error occured", "error");
                                setTimeout(() => {
                                    window.location.href = "<?php echo ROOT_DIR ?>/myOrders";
                                    hideLoader();
                                }, 1000);
                            }
                        });

                        // window.location.href = "<?php echo ROOT_DIR ?>/pay/complete";


                        // Note: validate the payment and show success or failure page to the customer
                    };

                    // Payment window closed
                    payhere.onDismissed = function onDismissed() {
                        // Note: Prompt user to pay again or show an error page
                        console.log("Payment dismissed");
                        alertmsg("Payment dismissed", "error");

                    };

                    // Error occurred
                    payhere.onError = function onError(error) {
                        // Note: show an error page
                        console.log("Error:" + error);
                        alertmsg("Error occured", "error");
                    };

                    // Put the payment variables here
                    var payment = {
                        "sandbox": true,
                        "merchant_id": data.merchant_id,
                        "return_url": "http://localhost:8080/pay/complete",
                        "cancel_url": "http://localhost:8080/pay/cancel",
                        "notify_url": "http://localhost:8080/pay/notify",
                        "order_id": data.orderId,
                        "items": "Door bell wireles",
                        "amount": data.amount,
                        "currency": "LKR",
                        "hash": data.hash,
                        "first_name": "Saman",
                        "last_name": "Perera",
                        "email": "samanp@gmail.com",
                        "phone": "0771234567",
                        "address": "No.1, Galle Road",
                        "city": "Colombo",
                        "country": "Sri Lanka",
                        "delivery_address": "No. 46, Galle road, Kalutara South",
                        "delivery_city": "Kalutara",
                        "delivery_country": "Sri Lanka",
                        "custom_1": "",
                        "custom_2": ""
                    };

                    console.log(payment);
                    payhere.startPayment(payment);
                }
            }
            xhttp.open("GET", "<?php echo ROOT_DIR ?>/pay/payhereprocess", true);
            xhttp.send();
        }









</script>


<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>



<?php require_once('../app/views/layout/footer.php'); ?>