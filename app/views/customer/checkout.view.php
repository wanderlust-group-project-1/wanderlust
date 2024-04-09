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

<div class="container flex-d flex-md-c justify-content-center  mt-5">
    <div class=" col-lg-8 col-md-12 flex-d-c gap-2 ">

        <div class="card card-normal ">

            <h2 class="justify-content-center flex-d"> Cart </h2>
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
                                <div class="cart-item-image col-lg-4">
                                    <img src="<?= OSURL ?>images/equipment/<?php echo htmlspecialchars($item->e_image); ?>" alt="Image" class="img-fluid">
                                </div>
                                <div class="cart-item-details col-lg-5">
                                    <h5 class="cart-item-name"><?php echo htmlspecialchars($item->e_name); ?></h5>
                                    <div class="cart-item-price">
                                        <h5>Fee: Rs. <?php echo htmlspecialchars($item->total); ?></h5>
                                        <button id="remove-from-cart" class="btn btn-primary">Remove from Cart</button>
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
        <div class="card card-normal col-md-11 flex-d-c">

        <div class="">
            <h2 class="justify-content-center flex-d"> Order Summary </h2>

<!-- 
            <div class="row flex-d-r mt-5 gap-2 justify-content-between">
        <h5> Total </h5> <h5> <?php echo $total; ?>  </h5> <br />
        <h5> Discount </h5> <h5> <? echo $total*0.1 ?> </h5>       
        </div> -->
            <div class=" row flex-d-r mt-5 gap-2 justify-content-between">
            



                        <div class="card-normal2">
                            <fieldset>
                                <!-- <legend>Payment Method</legend> -->

                                <div class="form__radios">
                                    <div class="form__radio">
                                      <label for="pay-full"><i class="fa fa-money" aria-hidden="true"></i>Full Payment</label>
                                      <input checked id="pay-full" name="payment-method" type="radio" />
                                    </div>

                                    <div class="form__radio">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                      <label for="pay-adv">Advance Payment</label>
                                      <input id="pay-adv" name="payment-method" type="radio" />
                                    </div>
                                </div>
                            </fieldset>

                            <div class="row flex-d-r mt-5 gap-2 justify-content-between">               
                                <table>
                                  <tbody>
                                    <tr>
                                      <td>Item Cost</td>
                                      <td align="right">Rs.<?php echo $total?></td>
                                    </tr>
                                    <tr>
                                      <td>Discount 10%</td>
                                      <td align="right">-Rs.<?php echo $total*0.1?></td>
                                    </tr>
                                  </tbody>
                                  <tfoot>
                                    <tr>
                                      <td>Total</td>
                                      <td align="right"><?php echo ($total+$total*0.1)?></td>
                                    </tr>
                                  </tfoot>
                                </table>
                            </div>
                            <!-- <h5 for="pay-full">Full Payment</h5>
                            <input class="" type="checkbox" id="pay-full" name="pay-full" value="pay-full"><br><br>
                            <h6>You can pay the full amount online. Order details will be sent to your email.</h6>
                        </div>
                        <h5 for="pay-adv">pay Advance</h5>
                        <input class="" type="checkbox" id="pay-adv" name="pay-adv" value="pay-adv"><br><br>
                        <h6>You can pay only  amount online. Order details will be sent to your email.</h6> -->
            </div>
        </div>


            <div class="row gap-4">

                        <!-- select pay fully or patial  -->
                        



                <h3>Total: Rs. <span id="total">

                        <?php
 
                        echo $total;
                        ?>

                    </span></h3>
            </div>
            <div class="row gap-2 ">
                <!-- <a href= <?php echo ROOT_DIR . "/cart/checkout" ?> class="btn btn-primary btn-full btn-lg">Pay</a> -->

                <button id="pay" class="btn btn-primary btn-full btn-lg" type="button">Pay</button>
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
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log("Error sending notification: ", textStatus, errorThrown);
                        }
                    });

                    // window.location.href = "<?php echo ROOT_DIR ?>/pay/complete";


                    // Note: validate the payment and show success or failure page to the customer
                };

                // Payment window closed
                payhere.onDismissed = function onDismissed() {
                    // Note: Prompt user to pay again or show an error page
                    console.log("Payment dismissed");
                };

                // Error occurred
                payhere.onError = function onError(error) {
                    // Note: show an error page
                    console.log("Error:" + error);
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