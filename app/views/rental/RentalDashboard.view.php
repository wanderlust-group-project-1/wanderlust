<?php
require_once('../app/views/layout/header.php');

// require_once('../app/views/components/navbar.php');
?>

<link rel="stylesheet" type="text/css" href="<?=ROOT_DIR ?>/assets/css/RentalDashboard.css">



<div class="dashboard">
    <?php require_once('../app/views/layout/sidebar.php');
    ?>

<div class="sidebar-flow"></div>


<div class="dashboard-content"> 

<div class="dashboard-card user"> 
    <div class="details"> 

    <div class="user-image">
        <img src="<?php echo ROOT_DIR?>/assets/images/2.png" alt="">
    </div>

    <!-- <div class="user-details"> -->
            <h2>  <?php echo $user->name; ?></h2>
            <p class="email"> <?php echo $user->email; ?></p>
            <p class="number"> <?php echo $user->mobile; ?></p>

            <!-- Add more details as needed -->
        <!-- </div> -->

        </div>
        <div class="options">

        <div class="edit-prof-button">
            <button type="submit" class="small-button-middle"  id="edit-profile">
                Edit Profile
            </button>
        </div>
        </div>

</div>

<!-- User Details Section -->
<div class="dashboard-card"> 

    <div class="user-details">
            <h2>User Details</h2>
            <p><strong>Name:</strong> <?php echo $user->name; ?></p>
            <p><strong>Email:</strong> <?php echo $user->email; ?></p>
            <p><strong>Role:</strong> <?php echo $user->role; ?></p>
            <!-- Add more details as needed -->
        </div>

</div>

<div class="dashboard-card"> 

<div class="equipment">

    <h2>Equipment</h2>

    <div class="equipment-list">
        
    <?php require_once('../app/views/rental/components/equipmentlist.view.php'); ?>


    </div>

        </div>

</div>


    


    <!-- <div class="rent-dash">
        <div class="frame">
        <div class="edit-prof-button">
            <button type="submit" class="small-button-middle"  id="edit-profile">
                Edit Profile
            </button>
        </div>

        <div class="div-1">
            <div class="div-12">
                <div class="text-wrapper">Hello Glaze Camping!</div>
                <div class="img-1">
                    <img src="<?php echo ROOT_DIR?>/assets/images/1.png" alt="">
                </div>
            </div>

            <form class="div-3">

                <div class="div-4">
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Name : <?php echo $user->name ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Location : Nuwara Eliya</div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Role : Rental Service</div>
                    </div>
                </div>

                <div class="div-4">
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Address : <?php echo $user->address ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Mobile No : <?php echo $user->mobile ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Registration No : <?php echo $user->regNo ?></div>
                    </div>
                </div>

            </form>

        </div>

    </div> -->




    <!-- Modal Box -->
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

                    <input type="submit" name="submit" value="Update">
                </form>



            </div>
        </div>
    </div>
</div>

<!-- Modal Box -->


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