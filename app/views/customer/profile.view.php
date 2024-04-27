<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');
require_once('../app/views/layout/footer.php');
?>


<div class="profile col-lg-12 align-items-center">
    
    <div class="flex-d-c col-lg-12 profile-content align-items-center py-4 mt-6">
    <div class="customer-bg-image">
        <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
    </div>
    <!-- <?php show ($user);?> -->
        <div class="profile-details">
            <div class="col-lg-3 profile-details-colum">
                <div class="card-normal-glass mt-6 py-6 align-items-center justify-content-center mih-100"> 
                    <div class="profile-picture">    
                        <img src="<? echo OSURL?>images/customers/<?php echo $user->image; ?>" alt="Image" class="img">
                    </div>
                    <h1 class="mb-5"> Hello <?php echo $user->name?>!</h1>
                    <div class="row">
                        <h4><i class="fas fa-envelope"></i> <?php echo $user->email ?></h4> 
                    </div>
                    <div class="row">
                        <h4><i class="fas fa-phone"></i> <?php echo $user->number ?></h4>
                    </div>
                    <div class="row">
                        <h4><i class="fa fa-location-arrow"></i> <?php echo $user->address ?></h4>
                    </div>
                    <div class="row">
                        <h4><i class="fas fa-id-card"></i> <?php echo $user->nic ?></h4>
                    </div>
                    <div class="row mt-4">
                        <button type="submit" class="btn mt-4" id="edit-profile">Edit Profile</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 flex-d-c mt-5 mh-50">
                <div class="card-normal-glass "> 
                    <div class="row justify-content-start ml-4">
                        <h1>Recent Booking</h1>
                    </div>
                    <div class="flex-d-r">
                        <div class="img-fluid h-200px rounded-7 ml-4">
                            <img src="<?php echo ROOT_DIR ?>/uploads/images/equipment/<?php echo $rental->equipment_image; ?>" alt="Equipment Image" class="img-fluid h-200px p-3 rounded-8">
                        </div>
                    </div>
                    <div class="flex-d-r  justify-content-start">
                        <div class="flex-d-c mr-5">
                            <div class="row justify-content-end mt-6">
                                <h3 class="mr-3"> <?php echo ucfirst($rental->rental_service_name) ?> </h3>
                            </div>
                            <div class="row justify-content-end mr-6">
                                <p class="mr-3"> <?php echo $rental->rental_service_address ?> </p>
                            </div>
                            <div class="row justify-content-end mr-6">
                                <p class="mr-3"> <?php echo $rental->rental_service_mobile ?> </p>
                            </div>
                            <div class="row justify-content-end mr-4">
                            <a href="<?php echo ROOT_DIR ?>/myOrders">
                                <button type="submit" class="btn-text-blue" id="see-more-bookings">View booking history</button></a>
                            </div>
                        </div>
                    </div>
                                    
                                    
                                    
                                       
                </div>
                <div class="card-normal-glass mt-4"> 
                    <div class="row justify-content-start ml-4 mw-75">
                        <h1>Usage</h1>
                    </div> 
                    <div class="flex-d-r mw-75 gap-0">
                        <div class="card-normal usage-card">
                            <div class="row justify-content-center">
                                <h3 class="equipment-booking">Equipment Booking</h3>
                            </div>
                            <div class="row">
                                <h2>11</h2>
                            </div>
                        </div>
                        <div class="card-normal usage-card">
                            <div class="row">
                                <h3>Guide Booking</h3>
                            </div>
                            <div class="row">
                                <h2>02</h2>
                            </div>
                        </div>
                        <div class="card-normal usage-card">
                            <div class="row">
                                <h3>Complaints</h3>
                            </div>
                            <div class="row">
                                <h2>00</h2>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- <div class="flex-d-r mt-9 mw-100 ml-6">
        <div class="flex-d-c col-lg-2 mw-50 mh-75 ml-6">
            <div class="card-normal-glass flex-d-r ">
                <div class="user-profile px-6 py-6">

                    <div class="container"> -->
                        <!-- <img src="<?php echo ROOT_DIR ?>/uploads/images/customers/<?php echo $user->image; ?>" class="img-fluid h-200px" alt="Profile Image"> -->
                        <!-- <img src="<? echo OSURL?>images/customers/<?php echo $user->image; ?>" alt="Image" class="img-fluid h-200px rounded-7">
                    </div>
                    <div class="user-details py-6">
                        <h1 class="mb-5"> Hello <?php echo $user->name ?>!</h1>
                        <div class="details flex-d">
                            align-items-center
                        </div>
                        <div class="details flex-d">
                            <h4><i class="fa fa-location-arrow"></i> <?php echo $user->address ?></h4>
                        </div>
                        <div class="details flex-d">
                            <h4><i class="fas fa-envelope"></i> <?php echo $user->email ?></h4>
                        </div>
                        <div class="details flex-d">
                            <h4><i class="fas fa-id-card"></i> <?php echo $user->nic ?></h4>
                        </div>
                    </div>
                    <div class="justify-items-center">
                        <button type="submit" class="btn mt-4" id="edit-profile">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div> -->

            <!-- <div class="card-normal flex-d-c col-lg-8 justify-content-center align-items-center">
                
                <div class="row">
                    <h1>Recent Booking</h1>
                </div>

                    <div class=" flex-d-r gap-5">
                        <div class="img-fluid h-200px rounded-7">
                            <img src="<?php echo ROOT_DIR ?>/uploads/images/equipment/<?php echo $rental->equipment_image; ?>" alt="Equipment Image" class="img-fluid h-200px">
                        </div>

                        <div class="flex-d-r">
                            <div class="flex-d">
                                <div class="flex-d-c">
                                    <h3 class="ml-3"> <?php echo ucfirst($rental->rental_service_name) ?> </h3>
                                    <p class="ml-3"> <?php echo $rental->rental_service_address ?> </p>
                                    <p class="ml-3"> <?php echo $rental->rental_service_mobile ?> </p>
                                    <div class="d-flex align-items-end flex-column">
                                    <a href="<?php echo ROOT_DIR ?>/myOrders">
                                        <button type="submit" class="btn-text-blue" id="see-more-bookings">
                                             View booking history
                                        </button>
                                    </a>
                                    </div>
                                </div>
                            </div>
                       </div>
                    </div>
                
                <div class="flex-d-r mh-25">
            <div class="flex-d-c mw-100">
                <div class="flex-d-c">
                    <div class="mw-100">
                        <h2> Usage </h2>
                        <div class="flex-d-R mw-50 gap-0">
                            <div class="usage-card flex-d-c">
                                <div class="flex">
                                    <span class="details justify-content-start">
                                        Guide Bookings
                                    </span>
                                    <a href="<?php echo ROOT_DIR ?>/guide">
                                        <button type=" submit" class="btn d-flex justify-content-end " id="see-more">
                                            See More >>
                                        </button>
                                    </a>
                                </div>
                                <div class="flex-d">
                                    <h1>01</h1>
                                </div>
                            </div>


                            <div class="usage-card flex-d-c">
                                <div class="flex-d-r">
                                    <span class="details justify-content-start">
                                        Equipment Booking
                                    </span>
                                    <a href="<?php echo ROOT_DIR ?>/rent">
                                        <button type=" submit" class="btn d-flex justify-content-end " id="see-more">
                                            See More >>
                                        </button>
                                    </a>
                                </div>
                                <div class="flex-d">
                                    <h1>04</h1>
                                </div>
                            </div>

                            <div class="usage-card flex-d-c">
                                <div class="flex-d-r">
                                    <span class="details justify-content-start">
                                        Complaints
                                    </span>
                                    <a href="<?php echo ROOT_DIR ?>/myBlog">
                                        <button type=" submit" class="btn d-flex justify-content-end " id="see-more">
                                            See More >>
                                        </button>
                                    </a>
                                </div>
                                <div class="flex-d">
                                    <h1>00</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

            </div> -->


<!-- 
    </div>
</div> -->





<!-- image Upload modal -->

<div class="modal" id="image-upload">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Upload Image</h2>
        <!-- <form action="<?= ROOT_DIR ?>/rentalService/uploadImage" method="post" enctype="multipart/form-data">
                <input type="file" name="image" id="image" required>
                <input type="submit" class="btn mt-4" name="submit" value="Upload">
            </form> -->
        <!-- With image preview -->
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="image" id="profile-image-input" class="form-control-lg" accept="image/png, image/jpg, image/gif, image/jpeg" required>
            <div class="image-preview-container flex-d-c align-items-center">


                <img src="<?php echo ROOT_DIR ?>/uploads/images/customers/<?php echo $user->image; ?>" alt="" id="image-preview" class="image-preview">
            </div>
            <input type="submit" class="btn mt-4" name="submit" value="Upload">
        </form>


    </div>
</div>



<!--  profile editor modal -->


<div class="profile-editor modal" id="profile-editor">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="profile-info">
 



            <div class="profile-image mh-400px">

                <div class="profile-image-overlay">
                    <img src="<?php echo ROOT_DIR ?>/uploads/images/customers/<?php echo $user->image; ?>" alt="Profile Image" class="profile-image mh-200px" id="profile-image">
                    <div class="camera-icon">
                        <i class="fa fa-camera" aria-hidden="true"></i>
                    </div>
                </div>



            </div>





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
            <!-- </div> -->
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
    $(document).ready(function() {
        $('.profile-image').click(function() {
            $('#image-upload').css('display', 'block');
        });
    });

    // image preview jquery
    $(document).ready(function() {
        $('#profile-image-input').change(function() {
            var reader = new FileReader();

            // file type validation
            if (!/image\/\w+/.test(this.files[0].type)) {
                alertmsg('File type not supported', 'error');
                // clear file input
                console.log('File type not supported');
                $('#profile-image-input').val('');

                return;
            }




            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);

            }
            reader.readAsDataURL(this.files[0]);

        });
    });

    // upload image  using ajax
    $(document).ready(function() {
        $('#image-upload form').submit(function(e) {
            e.preventDefault();
            showLoader();
            var formData = new FormData(this);
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: "<?= ROOT_DIR ?>/api/customer/uploadImage",
                type: 'POST',
                data: formData,
                success: function(data) {
                    alertmsg('Image uploaded successfully', 'success');
                    $('#image-upload').css('display', 'none');
                    console.log(data.data);

                    $('.profile-image').attr('src', '<?= ROOT_DIR ?>/uploads/images/customers/' + data.data.image);
                    // $('#profile-image-input').val('');
                    // $('#image-preview').attr('src', '');
                    // location.reload();
                    hideLoader();
                },
                error: function(data) {
                    alertmsg('Image upload failed', 'error');
                    console.log(data);
                    hideLoader();
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    });
</script>