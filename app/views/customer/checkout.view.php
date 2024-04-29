<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/navbar/customer-navbar.php');

?>

<!-- <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script> -->

<?php
// $total = 0;
// foreach ($items as $item) {

//     $total += $item->e_fee;
// }
?>

<div class="container flex-d flex-md-c justify-content-center  mt-9 ml-8 p-6">
    <div class="customer-bg-image">
        <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
    </div>
    <div class=" col-lg-8 col-md-12 flex-d-c gap-2 ">

        <div class="card card-normal-glass justify-content-center">

            <h2 class="justify-content-center flex-d "> Cart </h2>
            <div class="row gap-2 ">
                <!-- scrollable cart items -->
                <!-- <div class="col-lg-12    " id="cart-items"> -->
                <div class="col-lg-12 checkout-items overflow-scroll " id="cart-items">




                    <?php
                    if (empty($items)) {
                        echo "<h3>Cart is empty</h3>";
                    }
                    foreach ($items as $item) : ?>
                        <div class="card" id='cart-item' data-id="<?= htmlspecialchars($item->id) ?>">
                            <div class="row gap-2">
                                <div class="cart-item-image col-lg-4 mb-6">
                                    <img src="<?= OSURL ?>images/equipment/<?php echo htmlspecialchars($item->e_image); ?>" alt="Image" class="img-fluid rounded-8">
                                </div>
                                <div class="cart-item-details col-lg-5">
                                    <h3 class="cart-item-name"><?php echo htmlspecialchars($item->e_name); ?></h3>
                                    <div class="cart-item-price">
                                        <h4>Fee: Rs. <?php echo htmlspecialchars($item->total); ?></h4>
                                        <!-- <button id="remove-from-cart" class="btn btn-primary">Remove from Cart</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>


                </div>
            </div>

        </div>






    </div>

    <div class=" col-lg-3 col-md-12 flex-d gap-2 ">
        <div class="card card-normal-glass col-md-11 flex-d-c">

            <div class="flex-d-c gap-2 justify-content-between h-100">
                <h2 class="justify-content-center flex-d"> Checkout </h2>




                <div class="flex-d-c align-items-end">
                    <div class="row flex-d-c gap-4 ">

                        <!-- select pay fully or patial  -->


<table class="payment-table">
    

                        <!-- <h4> Total: Rs. <?php echo $total; ?> </h4>
                        <h4> Booking Fee: Rs. <?php echo $total * 0.2 ?> </h4> -->
                     
                        <tr>
                            <td> Total: </td>
                            <td> Rs. <?php echo $total; ?> </td>
                        </tr>
                        <tr>
                            <td> Booking Fee: </td>
                            <td> Rs. <?php echo $total * 0.2 ?> </td>
                        </tr>
                      

</table>

                    </div>
                    <div class="row gap-2 ">
                        <!-- <a href= <?php echo ROOT_DIR . "/cart/checkout" ?> class="btn btn-primary btn-full btn-lg">Pay</a> -->

                        <button id="pay" class="btn-text-green border mt-4" type="button">Pay</button>
                    </div>

                </div>
            </div>



        </div>


    </div>

    <script>
        $(document).ready(function() {
            // Event listener for the button click

            $('#pay').on('click', function() {
                pay();
            });

        });

        // set timeout 

        setTimeout(() => {
            console.log(payhere);

        }, 1000);

        function pay() {

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),

                },
                type: "GET",

                url: "<?php echo ROOT_DIR ?>/api/pay/cart",

                success: function(response) {
                    console.log(response);
                    paymentGateWay(response.data)
                }

            });

        }



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








        // check payhere is loaded, and alert if not loaded(undefined)
        setTimeout(() => {
            console.log(payhere);
            if (payhere == undefined) {
                alertmsg("Payhere not loaded", "error");
            }
        }, 1000);
    </script>

    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

    <?php
    require_once('../app/views/layout/footer-main.php');
    ?>

    <?php
    require_once('../app/views/layout/footer.php');
    ?>