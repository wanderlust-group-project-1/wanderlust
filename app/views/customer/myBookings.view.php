<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');

?>

<div class="dashboard">


    <?php
    // show(UserMiddleware::getUser());
    ?>
    <div class="guide-dash-main flex-d-c m-6">

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="guide-card-new">
                <div class="booking-list" id="booking-list">
                    <h2>Booking Details</h2>

                    <!-- Booking list will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        function bookingList() {
            $.ajax({
                headers: {
                    Authorization: "Bearer " + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/guideBookings/getAllMyBookings/',
                type: 'GET',
                success: function(data) {
                    console.log("Booking List")
                    console.log(data.data.bookingDetails)
                    console.log(data.data.guideDetails)
                    bookingHTML(data.data.guideDetails, data.data.bookingDetails);
                }

            });
        }

        function bookingHTML(userDetails, bookingDetails) {
            const modalContent = document.getElementById("booking-list");
            modalContent.innerHTML = "";

            const userHTML = `
            <div class="user-details">
                <h2>User Details</h2>
                <p>Name: ${userDetails.name}</p>
                <p>Mobile No: ${userDetails.mobile}</p>
                <!-- Add more user details as needed -->
            </div>
        `;

            const bookingHTML = `
            <div class="booking-details">
                    ${bookingDetails.map(booking => `
                        <div class="guide-card-new booking-history">
                    <span class="label">Recent Bookings</span>
                        <div class=".flex-d mt-4 mb-2">
                            <p> Date: ${booking.date}</p>
                            <p> Place: ${booking.location}</p>
                            <p> Group Size: ${booking.no_of_people}</p>
                            <p> Status: ${booking.status}</p>

                        </div>
                        </div>

                    `).join('')}
            </div>
        `;

            modalContent.innerHTML = userHTML + bookingHTML;
        }
        bookingList();
    });
</script>


<?php require_once('../app/views/layout/footer.php'); ?>