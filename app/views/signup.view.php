<?php
require_once('../app/views/layout/header.php');
//require_once('../app/views/components/navbar-auth.php');
?>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY  ?>&libraries=places&callback=initialize" async defer></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">





<div class="signup-container">
    <div class="signup-form">
            <div class="header">
                <h2>Who are you?</h2>
            </div>
            <div class="main">
                <div class="row">
                    <button class="signup-card" onclick="load('customer')">
                        <div class="btn-div">
                            <h3>Customer</h3>
                            <h4>Plan your journey now!</h4>
                        </div>
                    </button>
                </div>
                <div class="row">
                    <button class="signup-card" onclick="load('guide')">
                        <div class="btn-div">
                            <h3>Guide</h3>
                            <h4>Explore the extraordinary now!</h4>
                        </div>
                    </button>
                </div>
                <div class="row">
                    <button class="signup-card" onclick="load('rental-service')">
                        <div class="btn-div">
                            <h3>Rental Service</h3>
                            <h4>Register your business now!</h4>
                        </div>
                    </button>
                </div>
            </div>
        <!-- </div> -->


        <!-- <form hidden id="customer" action="<?= ROOT_DIR ?>/signup/customer" method="post"> -->
        <form hidden id="customer" >

            <h2>Customer Sign Up</h2>
            <?php if (isset($errors)) : ?>
                <div> <?= implode('<br>', $errors) ?> </div>
            <?php endif; ?>


            <label for="name">Name</label>
            <input type="text" name="name" id="name" required></br>

            <label for="address">Address</label>
            <textarea class="address-text" name="address" id="address" required> </textarea></br>



            <label for="email">Email</label>
            <input type="text" name="email" id="email" required></br>

            <label for="number">Mobile Number</label>
            <input type="text" name="number" id="number" required></br>

            <label for="nic">NIC Number</label>
            <input type="text" name="nic" id="nic" required></br>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required></br>

            <!-- <input  type="submit" name="submit" value="Signup"> -->
            <button id="customer-signup" name="submit" value="Sign Up"> Sign Up </button>
            <div id="error-message"></div>
        </form>
        <!-- Rental Services -->
        <!-- <form hidden id="rental-service" action="<?= ROOT_DIR ?>/signup/rentalService" method="post" enctype="multipart/form-data" > -->
        <form hidden id="rental-service"  >

            <h2>Rental Services Sign Up</h2>

            <?php if (isset($errors)) : ?>
                <div><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>

            <label for="business_name">Business Name</label>
            <input type="text" name="name" id="business_name"> </br>

            <label for="address">Address</label>
            <textarea class="address-text" name="address" id="address" required> </textarea></br>

            <!-- select location from google map -->

            <div class="location-button-container"  id="location-button-container">

            <input  id="location"  hidden="true"></br>
            <button  id="select-location" class="location-button" type="button" >Get Location</button>

            </div>


            <label for="registration_number">Business Registration Number/NIC</label>
            <input type="text" name="regNo" id="registration_number"></br>

            <label for="mobile_number">Mobile Number</label>
            <input type="text" name="mobile" id="number"></br>

            <label for="email">Email Address</label>
            <input type="text" name="email" id="email"></br>


            <div class="tooltip-container" class="hoverable-are">
                Upload verification Documents
                <i class="fas fa-question-circle tooltip-trigger"></i>
                <div class="tooltip">Documents such as business registration certificate or any other document which can verify your business is legitimate.</div>
                <div class="file-input-container">
                    <label for="verification_document" class="file-label">Choose Verification Document</label>
                    <input type="file" name="verification_document" id="verification_document" class="file-input">
                </div>
            </div></br>
            <label for="password">Password</label>
            <input type="password" name="password" id="password"></br>

            <!-- <input type="submit" name="submit" value="Signup"> -->
            <button id="rental-service-signup" name="submit" value="Sign Up" onclick="rentalServiceSignup(event)" > Sign Up </button>

            <div id="error-message"></div>

        </form>


        <!-- Guide -->
        <!-- <form hidden id="guide" action="<?= ROOT_DIR ?>/signup/guide" method="post"> -->
        <form hidden id="guide">

            <h2>Guide Sign Up</h2>

            <?php if (isset($errors)) : ?>
                <div> <?= implode('<br>', $errors) ?> </div>
            <?php endif; ?>


            <label for="name">Name</label>
            <input type="text" name="name" id="name" required></br>

            <label for="address">Address</label>
            <textarea class="address-text" name="address" id="address" required> </textarea></br>

            <label for="nic">NIC</label>
            <input type="text" name="nic" id="nic" required></br>

            <label for="mobile_number">Mobile Number</label>
            <input type="text" name="mobile" id="number" required></br>

            <label for="email">Email Address</label>
            <input type="text" name="email" id="email" required></br>

            <label for="gender">Gender</label>
            <select name="gender" id="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select></br>

            <div class="tooltip-container" class="hoverable-are">
                Upload verification Documents
                <i class="fas fa-question-circle tooltip-trigger"></i>
                <div class="tooltip">Documents such as Tourist Board License or an endorsement letter from Grama Niladhari/local police station to verify your authenticity and credibility.</div>
                <div class="file-input-container">
                    <label for="verification_document" class="file-label">Choose Verification Document</label>
                    <input type="file" name="verification_document" id="verification_document" class="file-input">
                </div>
            </div></br>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required></br>

            <!-- <input type="submit" name="submit" value="Signup"> -->
            <button id="guide-signup" name="submit" value="Sign Up" onclick="guideSignup(event)"> Sign Up </button>
            <div id="error-message"></div>

        </form>

        <h4>Have an account? <a href="<?= ROOT_DIR ?>/login">Login</a></h4>

            <!-- <a href="<?= ROOT_DIR ?>/login" title="Login">Login</a>
    <br>
    <a href="<?= ROOT_DIR ?>" title="Home">Home</a> -->

    </div>
</div>


<!-- Location Modal -->
<div id="location-modal" class="location-modal">
    <div class="modal-content">


        <span class="close">&times;</span>
        <input id="pac-input" class="controls" type="text" placeholder="Enter Location" />

        <div id="map-canvas" class="map-canvas"> </div>

            <input type="text" class="form-control" id="latitude"  hidden/>
            <input type="text" class="form-control" id="longitude" hidden/>

        <div class="location-button-container">
            <button id="confirm-location" class="location-button" type="button">Confirm Location</button>
        </div>
    </div>


</div>
<script>


</script>
<script src="<?= ROOT_DIR ?>/assets/js/signup.js"></script>
<script src="<?= ROOT_DIR ?>/assets/js/map.js"></script>

<?php
require_once('../app/views/layout/footer.php');


?>