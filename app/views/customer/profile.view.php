<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');
require_once('../app/views/layout/footer.php');
?>


<div class="profile">
    <div class="dashboard-content">
        <div class="flex-d-c">
            <div class="usage flex-d-r">
                <div class="user-profile">

                    <div class="container">
                        <img src="<?php echo ROOT_DIR ?>/uploads/images/customers/<?php echo $user->image; ?>" class="img">
                        <div class="overlay">
                            <div class="text">Hello <?php echo $user->name ?> ! </div>
                        </div>

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

        <div class="flex-d-r">
            <div class="flex-d-c">
                <div class="usage-block flex-d-c">
                    <h2> Recent Booking </h2>

                    <div class=" flex-d-r">
                        <div class="img-2">
                            <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="">
                        </div>

                        <div class="flex-d-r">
                            <div class="recent-booking-details flex-d">
                                <p>Booking Type : Equipment Booking</p>
                                <p>Name : Glazers Camping</p>
                                <p>Date : 20/08/2023</p>
                                <p>Time : 05:25 PM</p>
                            </div>

                        </div>
                        <!-- <div class="flex-d-r"> -->
                        <div class="d-flex align-items-end flex-column">
                            <a href="<?php echo ROOT_DIR ?>/myOrders">
                                <button type=" submit" class="btn mt-4  " id="see-more-bookings">
                                    See More >>
                                </button>
                            </a>
                        </div>
                        <!-- </div> -->

                    </div>
                </div>
            </div>




            <div class="flex-d-c">
                <div class="profile-editor" id="profile-editor">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <div class="profile-info">
                            <!-- <div class="profile-image-wrapper"> -->
                            <!-- 
                                <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="Profile Image" class="profile-image">
                                <div class="profile-image-container">
                                    <input type="file" id="profile-image-upload" accept="image/*" style="display:none">

                                    <button id="change-profile-pic-button" class="btn-edit-profile-pic">Change Profile Picture</button>
                                </div> -->



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
            </div>
        </div>


        <div class="flex-d-r">
            <div class="usage flex-d-c">
                <div class="flex-d-c">
                    <div class="usage-details">
                        <h2> Usage </h2>
                        <div class="flex-d-R">
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
    </div>
</div>





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
                <input type="file" name="image" id="profile-image-input" class="form-control-lg"  accept="image/png, image/jpg, image/gif, image/jpeg" required>
                <div class="image-preview-container flex-d-c align-items-center">
                    
                    
                <img src="<?php echo ROOT_DIR ?>/uploads/images/customers/<?php echo $user->image; ?>" alt="" id="image-preview" class="image-preview">
                </div>
                <input type="submit" class="btn mt-4" name="submit" value="Upload">
            </form>


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
                    alertmsg('File type not supported','error');
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
                var formData = new FormData(this);
                $.ajax({
                    headers: {
                      'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
                    },
                    url:"<?= ROOT_DIR ?>/api/customer/uploadImage",
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        alertmsg('Image uploaded successfully','success');
                        $('#image-upload').css('display', 'none');
                        console.log(data.data);

                        $('.profile-image').attr('src', '<?= ROOT_DIR ?>/uploads/images/customers/' + data.data.image);
                        // $('#profile-image-input').val('');
                        // $('#image-preview').attr('src', '');
                        // location.reload();
                    },
                    error: function(data) {
                        alertmsg('Image upload failed','error');
                        console.log(data);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        });





    
    </script>