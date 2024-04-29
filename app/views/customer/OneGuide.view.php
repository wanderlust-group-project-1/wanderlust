<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');

?>



?>

<div class="dashboard customer-view">
    <div class="customer-bg-image">
    <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
    </div>

    <?php
    // show(UserMiddleware::getUser());
    ?>
    <div class="guide-dash-main customer-view flex-d-c m-6">
    
        <!-- <h1 class="title mb-2">My Guide Profile</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Guide Profile</a></li>
        </ul> -->

        <div class="guide-availability" id="guide-availability">
            <a class="btn-text-black" href="<?= ROOT_DIR ?>/guideavailability"><i class="fas fa-calendar" aria-hidden="true"></i> Availability</i></a>
            <!-- <button class="btn-text-red" id="cancel-complaint"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>   -->

        </div>

        <div class="card-normal-glass">
        <div class="guide-profile customer-view p-6" id="guide-profile">
            <div class="guide-profile-img">
                <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
            </div>
            <div class="guide-profile-content" data-id="<?= htmlspecialchars($guide[0]->id) ?>" id="guide-details">
                <h1>Hi, It's <span><?php echo $guide[0]->guide_name; ?></span></h1>
                <h3 class="typing-text"> I'm a <span>Guide</span></h3>
                <p><?= htmlspecialchars($guide[0]->description) ?></p>
                <!-- <p>Hi, I'm Nirmal, a professional tour guide with 5 years of experience. I have a passion for history and culture and love sharing my knowledge with others. I specialize in tours of ancient ruins, temples, and historical sites. I'm also an expert in local cuisine and can recommend the best places to eat in town. Let me show you the beauty of my country and help you create memories that will last a lifetime.</p> -->

            </div>
        </div>
        </div>

        <div class="Customer-guide-view">
            <div class="info-data mt-5 ml-5 mr-5">
                <div class="guide-card-new customer-view-card">
                    <div class="package-list-selector">
                        <span class="label">Packages For You</span>
                        <?php
                        $loopIndex = -1;
                        foreach ($packages as $package) :
                            $loopIndex++; ?>
                            <div class="mt-4 mb-2" data-id="<?= htmlspecialchars($packages[$loopIndex][0]->id) ?>">
                                <span class="Guide-topics">Package <?= $loopIndex + 1 ?></span>
                                <p class="booking-bar mt-3">Price: <?= htmlspecialchars($packages[$loopIndex][0]->price) ?></p>
                                <p class="booking-bar mt-3">Maximum Group Size: <?= htmlspecialchars($packages[$loopIndex][0]->max_group_size) ?></p>
                                <p class="booking-bar mt-3">Maximum Distance: <?= htmlspecialchars($packages[$loopIndex][0]->max_distance) ?></p>
                                <button class="btn-text-green border mt-5" id="book-guide-btn">Book</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="info-data mt-5 ml-5 mr-5">
                <div class="guide-card-new customer-view-card">
                    <span class="label">Languages</span>

                    <?php
                    $languages = explode(',', $guide[0]->languages); // Assuming languages are comma-separated in the database
                    ?>

                    <?php foreach ($languages as $language) : ?>
                        <div class="booking-bar .flex-d mt-4 mb-2">
                            <p><?= htmlspecialchars(trim($language)) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="guide-card-new customer-view-card">
                    <span class="label">Certifications</span>

                    <?php
                    $certifications = explode(',', $guide[0]->certifications); // Assuming languages are comma-separated in the database
                    ?>

                    <?php foreach ($certifications as $certification) : ?>
                        <div class="booking-bar .flex-d mt-4 mb-2">
                            <p><?= htmlspecialchars(trim($certification)) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="guide-profile-content">
                <div class="booking-list" id="booking-list">

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Box View Booking Guide -->
<!-- 
<div id="book-guide" class="modal book-guide" style="display: none;">
    <div class="modal-content">
        <div id="book-package-details">
        </div>
    </div>
</div> -->


<script>
    function bookingList(packageId) {
        console.log('booking list');
        $.ajax({
            headers: {
                Authorization: "Bearer " + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/guideBookings/getGuideAllBookings/'+ packageId,
            type: 'GET',
            success: function(data) {
                console.log(data);
                bookingHTML(data.data);
            }

        });
    }

    function bookingHTML(bookingDetails) {
        const modalContent = document.getElementById("booking-list");
        modalContent.innerHTML = ""; // Clear previous content

        const currentDate = new Date();

        const recentBookings = [];
        const upcomingBookings = [];

        // Divide bookings into recent and upcoming based on date
        bookingDetails.forEach(booking => {
            const bookingDate = new Date(booking.date);
            if (bookingDate > currentDate) {
                upcomingBookings.push(booking);
            } else {
                recentBookings.push(booking);
            }
        });

        // Function to generate HTML for booking details
        function generateBookingHTML(bookings) {
            return bookings.map(booking => `
            <div class="booking-bar .flex-d mt-4 mb-2">
                <p>${booking.date} : ${booking.location}</p>
            </div>
        `).join('');
        }

        const tableHTML = `
        <div class="info-data mt-5 ml-5 mr-5 guide-profile-content">
            <div class="guide-card-new customer-view-card booking-history">
                <span class="label">Recent Bookings</span>
                ${generateBookingHTML(recentBookings)}
            </div>
            <div class="guide-card-new customer-view-card customer-view booking-history">
                <span class="label">Upcoming Bookings</span>
                ${generateBookingHTML(upcomingBookings)}
            </div>  
        </div>`;
        modalContent.innerHTML = tableHTML;
    }
    console.log(<?php echo $package[0]->id ?>);
    bookingList(<?= $package[0]->id ?>);
</script>

<script>
    function viewBookingPayment() {

    }
</script>

<script>
    function viewBookGuide() {
        console.log('book guide');

        var modal = document.getElementById("payment-modal");

        // closeBtn = document.querySelectorAll("close")[0];
        // closeBtn.addEventListener("click", function(){
        $(document).on('click', '#payment-modal .close', function() {
            modal.style.display = "none";
        });

        // var viewButtons = document.querySelectorAll('#book-guide-btn');
        // console.log(viewButtons);
        // viewButtons.forEach(function(button) {
        //     button.addEventListener('click', function() {
        //         var packageId = button.getAttribute('data-id');
        //         modal.style.display = "block";
        //     });
        // });

        $(document).on('click', '#book-guide-btn', function() {

            var packageId = $(this).parent().attr('data-id');
            console.log(packageId);
            fetchGuidePackages(packageId);
            modal.style.display = "block";
        });
    }

    function fetchGuidePackages(packageId) {
        console.log('fetch packages');
        $.ajax({
            headers: {
                Authorization: "Bearer " + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/FindGuide/viewGuidePackage/' + packageId,
            method: 'GET',
            success: function(data) {
                var modalContent = document.querySelector('#payment-modal .modal-content');
                modalContent.innerHTML = data;
                console.log(packageId);

                // var closeBtn = document.querySelector('#book-guide .close');
                // closeBtn.addEventListener('click', function(){
                $(document).on('click', '#payment-modal .close', function() {
                    var modal = document.getElementById("book-guide");
                    modal.style.display = "none";
                    console.log("Modal closed");
                });
            },
            error: function(err) {
                console.log(err);
            }
        })
    }


    document.addEventListener("DOMContentLoaded", function() {
        viewBookGuide();
    });
</script>

<!-- Modal box for payment -->

<div id="payment-modal" class="modal payment-modal" style="display: none;">
    <div class="modal-content">
        <div id="payment-details">
        </div>
    </div>
</div>

<!-- Modal box for confirm payment -->

<div id="confirm-payment-modal" class="modal confirm-payment-modal" style="display: none;">
    <div class="modal-content">
        <div class="confirm-payment-details" id="confirm-payment-details-<?php echo htmlspecialchars($package[0]->id); ?>">
            <span class="close">&times;</span>
            <div class="container flex-d-c gap-4 p-md-0 ">
                <h2>Payment Details</h2>

                <div class="row">

                    <div class="col-lg-6 col-md-12">

                        <table class="table-details">
                            <tr>
                                <!-- <td><strong>Price:</strong></td> -->
                                <td><strong><?php echo htmlspecialchars($package[0]->price); ?></strong></td>
                            </tr>
                        </table>

                    </div>

                    <p>Are you sure you want to proceed with the payment?</p>
                    <div class="flex-d gap-2 mt-5">
                        <button id="book-pay" class="btn btn-danger" data-id="<?php echo htmlspecialchars($package[0]->id); ?>">Pay</button>
                        <button id="cancel-pay" class="btn modal-close">Cancel</button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var modal = document.getElementById("confirm-payment-modal");

    $(document).on('click', '#confirm-payment-modal .close', function() {
        modal.style.display = "none";
    });

    $(document).on('click', '#book-pre-pay', function() {

        var packageId = $(this).attr('data-id');
        console.log(packageId);
        fetchGuidePackages(packageId);
        modal.style.display = "block";
    });
</script>

<!--Booking Guide AJAX request -->
<script>
    $(document).on('click', '#book-pay', function() {
        var packageId = $(this).attr('data-id');
        console.log(packageId);

        // Retrieve data from local storage
        var searchData = localStorage.getItem('searchData');
        if (searchData) {
            var jsonData = JSON.parse(searchData); // Parse JSON string to object
            jsonData.package_id = packageId; // Add package_id to the JSON object
            console.log(jsonData);

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/GuideBookings/bookRequest',
                method: 'POST',
                data: JSON.stringify(jsonData),
                contentType: 'application/json',
                processData: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        alertmsg("Guide booking request is successful");
                        paymentGateWay(data.data); // Call payment gateway
                        // window.location.href = '<?= ROOT_DIR ?>/myBookings';
                        // Update UI with new profile details
                        // For example, update name, description, languages, certifications
                        // closeModal(); // Close the modal after successful update
                    } else {
                        // Display errors
                        var errorDiv = document.getElementById("profile-errors");
                        errorDiv.innerHTML = data.errors.join('<br>');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            console.log("No search data found in local storage.");
        }
    });

    // Payment gateway
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
                                window.location.href = "<?php echo ROOT_DIR ?>/myBookings";

                                hideLoader();

                            }, 1000);


                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log("Error sending notification: ", textStatus, errorThrown);
                            alertmsg("Error occured", "error");
                            setTimeout(() => {
                                window.location.href = "<?php echo ROOT_DIR ?>/myBookings";
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
    require_once('../app/views/layout/footer.php');
?>