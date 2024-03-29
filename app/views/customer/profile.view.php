<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');
require_once('../app/views/layout/footer.php');
?>


<div class="dashboard">


    <div class="dashboard-content">
        <div class="flex-d-r">

            <div class="flex-d-c">
                <div class="usage flex-d-c">
                    <div class="user-profile">

                        <h1>Hello <?php echo $user->name ?>!</h1>
                        <div class="img">
                            <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="">
                        </div>
                        <div class="user-details">
                            <h2> <?php echo $user->name ?> </h2>
                            <div class="details flex-d">
                                <p><i class="fas fa-phone"></i> <?php echo $user->number ?></p>
                            </div>
                            <div class="details flex-d">
                                <p><i class="fa fa-location-arrow"></i><?php echo $user->address ?></p>
                            </div>
                            <div class="details flex-d">
                                <p><i class="fas fa-envelope"></i> <?php echo $user->email ?></p>
                            </div>
                            <div class="details flex-d">
                                <p><i class="fas fa-id-card"></i> <?php echo $user->nic ?></p>
                            </div>
                        </div>
                        <div class="">
                            <button type="submit" class="btn mt-4" id="edit-profile">
                                Edit Profile
                            </button>
                        </div>

                    </div>

                </div>

            </div>




        </div>
        <div class="flex-d-r">
            <div class="flex-d-c">
                <div class="usage-block flex-d-c">
                    <h2> Usage </h2>

                    <div class="usage-card flex-d-c">
                        <div class="flex">
                            <span class="details justify-content-start">
                                Guide Bookings
                            </span>
                            <button type=" submit" class="btn d-flex justify-content-end " id="see-more">
                                See More >>
                            </button>
                        </div>
                        <div class="flex-d">
                            <h1>01</h1>
                        </div>
                    </div>


                    <div class="usage-card flex-d-c">
                        <div class="flex-d-r">
                            <span class="details">
                                Equipment Booking
                            </span>
                            <button type=" submit" class="btn d-flex justify-content-end " id="see-more">
                                See More >>
                            </button>
                        </div>
                        <div class="flex-d">
                            <h1>04</h1>
                        </div>
                    </div>

                    <div class="usage-card flex-d-c">
                        <div class="flex-d-r">
                            <span class="details">
                                Blogs uploaded
                            </span>
                            <button type=" submit" class="btn d-flex justify-content-end " id="see-more">
                                See More >>
                            </button>
                        </div>
                        <div class="flex-d">
                            <h1>00</h1>
                        </div>
                    </div>

                </div>
            </div>
        </div>



        <div class="flex-d-r">
            <!-- <div class="flex-d-c"> -->
            <!-- <div class="usage flex-d-c"> -->



            <!-- Modal Box -->
            <div class="profile-editor" id="profile-editor">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <div class="profile-info">
                        <div class="profile-image-wrapper">
                            <!-- <div  > -->
                            <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="Profile Image" class="profile-image">
                            <div class="profile-image-container">
                                <input type="file" id="profile-image-upload" accept="image/*" style="display:none">
                                <!-- <button type="submit" class="btn mt-4" id="edit-profile">
                                        Edit Profile
                                    </button> -->
                                <button id="change-profile-pic-button" class="btn-edit-profile-pic">Change Profile Picture</button>
                            </div>
                            <!-- </div> -->

                            <form id="customer" action="<?= ROOT_DIR ?>/customer/update" method="post">
                                <h2>Update Customer Details</h2>
                                <?php if (isset($errors)) : ?>
                                    <div> <?= implode('<br>', $errors) ?> </div>
                                <?php endif; ?>
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" value="<?= $user->name ?>" required>

                                <label for="address">Address</label>
                                <input type="text" name="address" id="address" value="<?= $user->address ?>" required>

                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" value="<?= $user->email ?>" required>

                                <label for="number">Number</label>
                                <input type="text" name="number" id="number" value="<?= $user->number ?>" required>

                                <label for="nic">NIC Number</label>
                                <input type="text" name="nic" id="nic" value="<?= $user->nic ?>" required>
                                <br />
                                <input type="submit" name="submit" value="Update" class="btn-edit-profile-pic">
                            </form>



                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="flex-d-c"> -->
        <!-- <div class="usage flex-d-c"> -->

        <!-- <div class="user-profile"> -->
        <!-- 
                <div class="head">
                    <div>
                        fefefaqew
                    </div>
                </div>
             <span class="progress" data-value="80%"></span>
                <span class="label">80%</span> -->



        <!-- <div class="flex-d-r">
            <div class="flex-d-c">
                <div class="usage-block flex-d-c">
                    <h2> Usage </h2> -->


        <div class="flex-d-r">

            <div class="usage flex-d-c">
                <div class="flex-d-c">
                    <div class="recent-booking-details">
                        <h2> Recent Booking </h2>

                        <div class=" flex-d-r">
                            <div class="img-2">
                                <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="">
                            </div>


                            <div class="flex-d-r">
                                <div class="recent-booking-details flex-d">
                                    <p> Booking Type : Equipment Booking</p>
                                    <p>Name : Glazers Camping</p>
                                    <p>Date : 20/08/2023</p>
                                    <p>Time : 10:00</p>
                                </div>

                            </div>
                            <div class="flex-d-r">
                                <button type=" submit" class="button" id="see-more">
                                    See More >>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>




    <div class="flex-d-r">
        <div class="flex-d-c">
            <div class="usage flex-d-c">
                <h2> Booking History </h2>




                <div class="div-6">
                    <div class="div-wrapper-3">
                        <table>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>

                            <tr>
                                <td>Glazers Camping</td>
                                <td>Upcoming</td>
                                <td>Equipment</td>
                                <td>02/12/2023</td>
                                <td>10.00</td>
                            </tr>

                            <tr>
                                <td>Glazers Camping</td>
                                <td>Upcoming</td>
                                <td>Equipment</td>
                                <td>02/12/2023</td>
                                <td>10.00</td>
                            </tr>

                            <tr>
                                <td>Sarath</td>
                                <td>Done</td>
                                <td>Guide</td>
                                <td>01/09/2023</td>
                                <td>10.00</td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>

<script>
    var modal = document.getElementById("profile-editor");

    var span = document.getElementsByClassName("close")[0];

    // Get all view buttons
    var viewButton = document.getElementById('edit-profile');

    // Function to handle modal display
    function openModal() {
        // document.getElementById("modal-content").innerHTML = content;
        modal.style.display = "block";
    }

    // Add click event listener to view buttons
    viewButton.addEventListener('click', function() {

        openModal();
    });




    // Close the modal when the close button is clicked
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>


<script>
    // Get the button and the file input element by their IDs
    var changeProfilePicButton = document.getElementById('change-profile-pic-button');
    var profileImageUpload = document.getElementById('profile-image-upload');

    // Add a click event listener to the button
    changeProfilePicButton.addEventListener('click', function() {
        // Trigger click event on the file input when the button is clicked
        profileImageUpload.click();
    });

    // Add an event listener to the file input to handle file selection
    profileImageUpload.addEventListener('change', function() {
        // Code to handle the selected file (e.g., upload to server, display preview, etc.)
        // You can add your logic here
    });
</script>