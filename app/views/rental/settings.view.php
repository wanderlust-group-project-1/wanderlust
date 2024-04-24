<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/admin/components/navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Settings</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Settings</a></li>
        </ul>

        <div class="info-data mt-5 flex-d-c ">
            <div class="card">
                <div class="row justify-content-between">


                    <!-- Rennting Settings -->
                    <div class="card-header">
                        <h3>Renting Settings</h3>
                    </div>

                    <div class="card-body mw-100px">
                        <button class="btn btn-primary" id="renting-settings">Edit</button>
                    </div>
                </div>



            </div>

            <div class="card">
                <div class="row justify-content-between">


                    <!-- Rennting Settings -->
                    <div class="card-header">
                        <h3>Account Settings</h3>
                    </div>

                    <div class="card-body mw-100px">
                        <button class="btn btn-primary" id="account-settings">Edit</button>
                    </div>
                </div>



            </div>

        </div>



        <!-- Report  -->







    </div>
</div>

<?php
require_once('../app/views/layout/footer.php');

?>
<!-- Include the ApexCharts library -->

<script>
    // jQuery, 
    $(document).ready(function() {
        // Renting Settings
        $('#renting-settings').click(function() {
            $('#renting-settings-modal').css('display', 'block');
        });

        $('.close').click(function() {
            $('#renting-settings-modal').css('display', 'none');
        });

        $('#disable-renting').click(function() {
            // $('#disable-renting').text('Disable');

            // ajax

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
                },
                url: '<?= ROOT_DIR ?>/api/settings/renting',
                type: 'POST',
                data: JSON.stringify({
                    renting_state: $('#disable-renting').text() == 'Enable' ? 0 : 1,
                }),
                success: function(response) {
                    console.log(response);


                    if ($('#disable-renting').text() == 'Enable') {
                        $('#disable-renting').text('Disable');
                    } else {
                        $('#disable-renting').text('Enable');
                    }

                    alertmsg('Renting state updated successfully', 'success')



                },
                error: function(error) {
                    console.log(error);
                    alertmsg('Renting state update failed', 'error')
                }




            });

        });

        $('#duration').change(function() {
            $('#duration-save').removeAttr('disabled');
        });

        $('#duration-save').click(function() {
            // ajax

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
                },
                url: '<?= ROOT_DIR ?>/api/settings/renting',
                type: 'POST',
                data: JSON.stringify({
                    recovery_period: $('#duration').val(),
                }),
                success: function(response) {
                    console.log(response);
                    $('#duration-save').attr('disabled', 'disabled');
                    alertmsg('Duration updated successfully', 'success')
                },
                error: function(error) {
                    console.log(error);
                    alertmsg('Duration update failed', 'error')
                }

            });
        });

    });







    // Account Settings

    $(document).ready(function() {
        // Renting Settings
        $(document).on('click', '#account-settings', function() {
            $('#account-settings-modal').css('display', 'block');
        });



        $(document).on('click', '#email-change', function() {
            $('#email-change-modal').css('display', 'block');
        });

        $(document).on('click', '#password-change', function() {
            $('#password-change-modal').css('display', 'block');
        });



        $(document).on('click', '#email-change-submit', function() {
            // ajax

            if ($('#email').val() == '') {
                alertmsg('Email cannot be empty', 'error');
                return;
            } else {
                // Regular expression for validating an email address
                var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                var email = $('#email').val();

                // Test the email value against the regex
                if (!regex.test(email)) {
                    alertmsg('Please enter a valid email address', 'error');
                    return;
                }
            }






            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
                },
                url: '<?= ROOT_DIR ?>/api/settings/emailChange',
                type: 'POST',
                data: JSON.stringify({
                    email: $('#email').val(),
                }),
                success: function(response) {
                    console.log(response);
                    // $('#email-change-modal').css('display', 'none');
                    alertmsg(response.message, 'success')
                    $('#email-msg').html(response.message);
                },

                error: function(error) {
                    console.log(error);
                    alertmsg('Email update failed', 'error')
                }

            });
        });


        // Password change


        $(document).on('click', '#password-change-submit', function() {
            // ajax

            if ($('#old-password').val() == '') {
                alertmsg('Old Password cannot be empty', 'error');
                return;
            }

            if ($('#new-password').val() == '') {
                alertmsg('New Password cannot be empty', 'error');
                return;
            }
            // Minimum 6 characters at least 1 Uppercase Alphabet, 1 Lowercase Alphabet, 1 Number and 1 Special Character
            var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,20}$/;
            var password = $('#new-password').val();

            // Test the password value against the regex
            if (!regex.test(password)) {
                alertmsg('Password must be at least 6 characters and must contain at least one number, one uppercase letter, one lowercase letter and one special character.', 'error');
                return;
            }


            if ($('#confirm-password').val() == '') {
                alertmsg('Confirm Password cannot be empty', 'error');
                return;
            }

            if ($('#new-password').val() != $('#confirm-password').val()) {
                alertmsg('New Password and Confirm Password do not match', 'error');
                return;
            }

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
                },
                url: '<?= ROOT_DIR ?>/api/settings/passwordChange',
                type: 'POST',
                data: JSON.stringify({
                    old_password: $('#old-password').val(),
                    new_password: $('#new-password').val(),
                    confirm_password: $('#confirm-password').val(),
                }),
                success: function(response) {
                    console.log(response);
                    alertmsg(response.message, 'success')
                    $('#password-change-modal').css('display', 'none');
                },

                error: function(error) {
                    console.log(error);
                    alertmsg('Password update failed', 'error')
                }

            });
        });


        // Password toggle

        $(document).on('click', '#eyeicon-old', function() {
            var input = $('#old-password');
            var icon = $('#eyeicon-old');
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash');
                icon.addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye');
                icon.addClass('fa-eye-slash');
            }
        });

        $(document).on('click', '#eyeicon-new', function() {
            var input = $('#new-password');
            var icon = $('#eyeicon-new');
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash');
                icon.addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye');
                icon.addClass('fa-eye-slash');
            }
        });

        $(document).on('click', '#eyeicon-confirm', function() {
            var input = $('#confirm-password');
            var icon = $('#eyeicon-confirm');
            if (input.attr('type') == 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash');
                icon.addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye');
                icon.addClass('fa-eye-slash');
            }
        });




    });
</script>


<!-- renting Settings -->

<div class="modal" id="renting-settings-modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>

            <h3>Renting Settings</h3>
        </div>
        <!-- Disable further renting -->
        <!-- Duration between two rentals -->

        <div class="modal-body">
            <!-- Button and change  -->
            <div class="row  justify-content-between">
                <div class="col-lg-6">
                    <h3 for="disable-renting">Disable further renting</h3>
                </div>
                <div class="mw-100px">
                    <button class="btn btn-primary" id="disable-renting">Enable</button>
                </div>
            </div>
            <!-- Duration between two rentals -->
            <div class="row  justify-content-between">
                <div class="col-lg-6">
                    <h3 for="disable-renting">Duration between two rentals</h3>
                </div>
                <div class="mw-200px">
                    <input type="number" id="duration" name="duration" value="1" min="1" max="15">
                </div>
                <button class="btn btn-primary" id="duration-save" disabled>Save</button>
            </div>

        </div>
    </div>
</div>


<!-- Account Settings -->


<div class="modal" id="account-settings-modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>

            <h3>Account Settings</h3>
        </div>

        <!-- Change Email -->

        <div class="modal-body">
            <!-- Button and change  -->
            <div class="row  justify-content-between">
                <div class="col-lg-5">
                    <h3 for="change-email">Change Email</h3>
                </div>
                <div class="mw-200px">
                    <input type="email" value="<?php echo UserMiddleware::getUser()['email'] ?>" disabled>

                </div>
                <button class="btn btn-primary" id="email-change">Change</button>
            </div>
            <!-- Change Password -->
            <div class="row  justify-content-between">
                <div class="col-lg-5">
                    <h3 for="change-password">Change Password</h3>
                </div>
                
                <button class="btn btn-primary" id="password-change">Change</button>


        </div>
    </div>
</div>
</div>




<!-- Email change modal -->

<div class="modal" id="email-change-modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>

            <h3> Enter New Email</h3>
        </div>

        <!-- Change Email -->

        <div class="modal-body">
            <div class="col-lg-12 flex-d justify-content-between align-items-center gap-2">

                <div class="mw-200px">
                    <input type="email" id="email" name="email" value="">

                </div>
                <button class="btn btn-primary" id="email-change-submit">Change</button>
            </div>
            <div  id="email-msg"></div>
        </div>
    </div>
</div>



<!-- Password change modal -->

<div class="modal" id="password-change-modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>

            <h3>Change Password</h3>
        </div>


        <div class="modal-body">
            
            <div class="col-lg-12 flex-d justify-content-between align-items-center gap-2 flex-md-c">
                <div class="col-lg-6 col-md-12">
                    <h3 for="change-password">Old Password</h3>
                </div>
                <div class="col-lg-5 col-md-12 ">
                    <div>
                    <input type="password" id="old-password" name="old-password" value="">
                    <div class="password-toggle">
                        <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon-old"></i>
                        </div>
                    </div>

                </div>
            </div>


            <div class="col-lg-12 flex-d justify-content-between align-items-center gap-2 flex-md-c">
                <div class="col-lg-6 col-md-12">
                    <h3 for="change-password">New Password</h3>
                </div>
                <div class="col-lg-5 col-md-12">
                    <input type="password" id="new-password" name="new-password" value="">
                    <div class="password-toggle">
                        <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon-new"></i>
                        </div>
                </div>
            </div>
            <div class="col-lg-12 flex-d justify-content-between align-items-center gap-2 flex-md-c">
                <div class="col-lg-6 col-md-12">
                    <h3 for="change-password">Confirm Password</h3>
                </div>
                <div class="col-lg-5 col-md-12">
                    <input type="password" id="confirm-password" name="confirm-password" value="">
                    <div class="password-toggle">
                        <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon-confirm"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 flex-d justify-content-between align-items-center gap-2">
                <button class="btn btn-primary" id="password-change-submit">Change</button>
            </div>
        </div>
    </div>
</div>



       