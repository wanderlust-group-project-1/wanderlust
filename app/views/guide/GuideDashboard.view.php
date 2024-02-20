<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/rental-navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?=ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

<div class="sidebar-flow"></div>


<div class="dashboard-content"> 

<!-- <div class="row"> -->
<div class="dashboard-card card  justify-content-sm-center "> 
    <div class="details "> 

    <div class="user-image">
        <img src="<?php echo ROOT_DIR?>/assets/images/2.png" alt="">
    </div>

    <!-- <div class="user-details"> -->
            

            <!-- Add more details as needed -->
        <!-- </div> -->

        </div>
        <div class="options">
        <h2>  <?php echo $user->name; ?></h2>
            <p class="email"> <?php echo $user->email; ?></p>
            <p class="number"> <?php echo $user->mobile; ?></p>

        <div class="">
            <button type="submit" class="btn mt-4"  id="edit-profile">
                Edit Profile
            </button>
        </div>
        </div>

</div>

<!-- User Details Section -->
<div class="dashboard-card card"> 

    <div class="rent-status">
            <h2>Bookings </h2>
            <h1> 15 </h1>
           
        </div>
        <div class="rent-status">
            <h2>Total Earnings</h2>
            <h1> RS 135000 </h1>

           
        </div>
        <div class="rent-status">
            <h2>Years of Experience</h2>
            <h1> 10 </h1>

           
        </div>

</div>

<!-- </div> -->


    <!-- Modal Box Profile Edit -->
    <div class="profile-editor" id="profile-editor">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="profile-info">
                <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="Profile Image" class="profile-image">


                <form id="rentalservice" action="<?= ROOT_DIR ?>/rentalService/update" method="post">
                    <h2>Update Rental Service Details</h2>
                    <?php if (isset($errors)) : ?>
                        <div> <?= implode('<br>', $errors) ?> </div>
                    <?php endif; ?>

                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?= $user->name ?>" required>

                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?= $user->address ?>" required>

                    <!-- <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?= $user->email ?>" required> -->

                    <label for="mobile">Mobile No</label>
                    <input type="text" name="mobile" id="mobile" value="<?= $user->mobile ?>" required>

                    <label for="regNo">Registration Number</label>
                    <input type="text" name="regNo" id="regNo" value="<?= $user->regNo ?>" required>

                    <!-- <label for="password">Password</label>
    <input type="password" name="password" id="password" required> -->

                    <input type="submit" class="btn mt-4" name="submit" value="Update">
                </form>



            </div>
        </div>
    </div>
</div>

<!-- Modal Box Profile Edit End -->



</script>
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

        // var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
        // var email = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
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



<?php
require_once('../app/views/layout/footer.php');

?>