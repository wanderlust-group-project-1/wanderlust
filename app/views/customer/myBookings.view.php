<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');

?>

<div class="dashboard customer-view justify-content-center">
<div class="customer-bg-image">
    <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
</div>


    <?php
    // show(UserMiddleware::getUser());
    ?>
    <div class="guide-dash-main customer-view col-lg-8 flex-d-c m-6">

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="guide-card-new customer-view-card">
                <div class="booking-list" id="booking-list">
                    <h2>Booking Details</h2>

                    <!-- Booking list will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="report-modal" style="display: none;">
    <div class="modal-content">
        <span class="close">×</span>
        <h2 id="report-order-id">Report for Order ID: 67</h2>
        <form id="report-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="report-title" name="title" class="form-control-lg" required="">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="report-description" name="description" class="form-control-lg" required=""></textarea>
            </div>
            <button class="btn-text-green border" id="report-submit" data-id="html">Submit</button>
        </form>
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

            //     const userHTML = `
            //     <div class="user-details">
            //         <h2>Guide Details</h2>
            //         <p>Name: ${userDetails.name}</p>
            //         <p>Mobile No: ${userDetails.mobile}</p>
            //         <!-- Add more user details as needed -->
            //     </div>
            // `;

            const bookingHTML = `
            <div class="booking-details my-3 col-lg-11 ">
                    ${bookingDetails.map(booking => `
                        <div class="guide-card-new my-4 booking-history">
                        <div class=".flex-d mt-4 mb-2">
                            <p> Date: ${booking.date}</p>
                            <p> Place: ${booking.location}</p>
                            <p> Group Size: ${booking.no_of_people}</p>
                            <p> Status: ${booking.status}</p>
                        </div>
                        <button class="btn-text-green order-view-button" id="view-button"><i class="fa fa-list" aria-hidden="true"></i> View</button>
                        <button class="btn-text-orange order-report-button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Complain</button>

                        </div>
                    `
                ).join('')}
            </div>


        `;

            modalContent.innerHTML = bookingHTML;
        }
        bookingList();
    });
</script>


<script>
    $(document).ready(function() {
        // Get the modal
        var modal = document.getElementById("report-modal");
        var viewModal = document.getElementById("view-booking-modal")

        // When the user clicks the "Complain" button, open the modal
        $(document).on('click', '.order-report-button', function() {
            modal.style.display = "block";
        });

        // When the user clicks the "View" button, open the modal
        $(document).on('click', '.order-view-button', function() {
            viewModal.style.display = "block";
        });

        // When the user clicks the close button, close the modal
        $(document).on('click', '.close', function() {
            modal.style.display = "none";
            viewModal.style.display = "none";
        });

        // When the user clicks anywhere outside of the modal, close it
        $(window).click(function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                viewModal.style.display = "none";
            }
        });
    });


    $(document).on('click', '#report-submit', function(e) {
        e.preventDefault();
        var title = $('#report-title').val();
        var description = $('#report-description').val();
        var orderId = $(this).attr('data-id');
        var data = {
            title: title,
            description: description,
            orderId: orderId
        };

        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/complaints/addComplaint',
            method: 'POST',
            data: JSON.stringify(data),
            success: function(response) {
                alert('Complaint submitted successfully');
                $('#report-modal').css('display', 'none');
            }
        });
    });
</script>



<!-- Modal box to view booking details -->
<div class="modal modal view-booking-modal" id="view-booking-modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="booking-list" id="booking-list">
            <h2>Booking Details</h2>

            <!-- Booking list will be displayed here -->
        </div>
        <div class="flex-d gap-2 mt-5">
            <button class="btn btn-danger delete-package-button">Cancel Booking</button>
        </div>
    </div>

    <div id="delete-booking-modal" class="delete-booking-modal modal">
        <div class="modal-content ">
            <span class="close ">&times;</span>
            <h2 class="guide-h2-title">Delete Booking</h2>
            <p>Are you sure you want to cancel this booking?</p>

        </div>
    </div>
</div>

<?php
    require_once('../app/views/layout/footer.php');
?>