<?php
require_once('../app/views/layout/header.php');
//require_once('../app/views/components/navbar-auth.php');
?>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY  ?>&libraries=places&callback=initialize" async defer></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="signup-container">
    <div class="column">
        <div class="signup-form">
        <h2>Who are you?</h2>
        <p>It's the time to start your journey! Register here.</p>
            <div class="main">
                <div class="row-btn">
                    <button class="signup-card" onclick="signupToggleCustomer()" id="customer">
                        <div class="btn-div">
                            <h3>Customer</h3>
                            <h4>Plan your journey now!</h4>
                        </div>
                    </button>
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <div class="row-btn">
                    <button class="signup-card" onclick="signupToggleGuide()">
                        <div class="btn-div">
                            <h3>Guide</h3>
                            <h4>Explore the extraordinary now!</h4>
                        </div>
                    </button>
                    <i class="fa fa-compass" aria-hidden="true"></i>
                </div>
                <div class="row-btn">
                    <button class="signup-card" onclick="signupToggleRental()">
                        <div class="btn-div">
                            <h3>Rental Service</h3>
                            <h4>Register your business now!</h4>
                        </div>
                    </button>
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                </div>
            </div>

            <h4>Have an account? <a href="<?= ROOT_DIR ?>/login">Login</a></h4>
        </div>

        <div class="signup-image-container">
        <!-- <div class="image-logo">
            <img src="<?php echo ROOT_DIR?>/assets/images/logo.png" alt="overlay-logo">
        </div> -->
            <img src="<?php echo ROOT_DIR?>/assets/images/signup.jpg" alt="signup-image" class="signup-image">
        </div>
    </div>
</div>

<!-----------------------------------------------CUSTOMER SIGN UP FORM------------------------------------------------> 

<div class="form-container">
    <div class="popupFormCustomer" id="popupForm">
        <div class="form-common">
            <div class="form-common-content">
                <div class="close-btn" onclick="signupToggleCustomer()"><i class="fa fa-times" aria-hidden="true"></i></div>
                <form action="" class="flex-d">
                    <h2>Customer Sign Up</h2>

                    <?php if (isset($errors)) : ?>
                        <div> <?= implode('<br>', $errors) ?> </div>
                    <?php endif; ?>
                    
                    <div class="row align-items-start">
                        <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                            <div class="form-element">
                                <!-- <label for="name">Name</label> -->
                                <input class="form-control-lg" type="text" name="name" id="name" placeholder="Full Name" required></br>
                            </div>

                            <div class="form-element">
                                <!-- <label for="address">Address</label> -->
                                <textarea  type="text" class="address-text" name="address" id="address" placeholder="Address" required> </textarea>
                            </div>

                            <div class="form-element">
                                <!-- <label for="email">Email</label> -->
                                <input class="form-control-lg" type="text" name="email" id="email" placeholder="Email" required>
                            </div>
                        </div>
                
                        <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                            <div class="form-element">
                                <!-- <label for="number">Mobile Number</label> -->
                                <input class="form-control-lg" type="text" name="number" id="number" placeholder="Contact No" required>
                            </div>

                            <div class="form-element">
                                <!-- <label for="nic">NIC Number</label> -->
                                <input class="form-control-lg" type="text" name="nic" id="nic" placeholder="NIC" required>
                            </div>

                            <div class="form-element">
                                <!-- <label for="password">Password</label> -->
                                <input class="form-control-lg" type="password" name="password" id="password" placeholder="Password" required>
                                <!-- <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i> -->
                                <h4>Please use a strong password. Password minimum length should be least 8 characters long containing at least one uppercase letter, one digit, and one symbol.</h4>
                            </div>
                        </div>

                        <div class="row">
                            <button class="btn btn-medium" id="customer-signup" name="submit" value="signup">Sign Up</button>
                            <!-- <button class="btn-cancel btn-medium" id="customer-signup-cancel" name="submit" value="cancel">Cancel</button> -->
                        
                            <script>
                                let eyeicon = document.getElementById("eyeicon");
                                let password = document.getElementById("password");

                                eyeicon.onclick = function() {
                                    if(password.type == "password"){
                                        password.type = "text";
                                        eyeicon.className = "fa fa-eye";
                                    }else{
                                        password.type = "password";
                                        eyeicon.className =  "fa fa-eye-slash";
                                    }
                                }
                            </script>
                            <div id="error-message"></div>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>

<!-----------------------------------------------GUIDE SIGN UP FORM-----------------------------------------------> 

<div class="form-container">    
    <div class="popupFormGuide" id="popupForm">
        <div class="form-common">
            <div class="form-common-content">
                <div class="close-btn" onclick="signupToggleGuide()"><i class="fa fa-times" aria-hidden="true"></i></div>
                <form action="" class="flex-d">
                    <h2>Guide Sign Up</h2>

                    <?php if (isset($errors)) : ?>
                        <div><?= implode('<br>', $errors) ?></div>
                    <?php endif; ?>
                    <div class="row align-items-start">

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">    
                        <div class="form-element">
                            <label for="business_name">Business Name</label>
                            <input class="form-control-lg" type="text" name="name" id="business_name"> </br>
                        </div>

                        <div class="form-element">
                            <label for="registration_number">Business Registration Number/NIC</label>
                            <input class="form-control-lg" type="text" name="regNo" id="registration_number"></br>
                        </div>

                        <div class="form-element">
                            <label for="mobile_number">Mobile Number</label>
                            <input class="form-control-lg" type="text" name="mobile" id="number"></br>
                        </div>

                        <div class="form-element">
                            <label for="email">Email Address</label>
                            <input class="form-control-lg" type="text" name="email" id="email"></br>
                        </div>

                        <div class="form-element">
                            <label for="address">Address</label>
                            <textarea class="address-text" name="address" id="address" required> </textarea></br>
                        </div>
                    </div>

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                        <div class="form-element">
                            <label for="address">Location</label><br>
                            <div class="location-button-container"  id="location-button-container">
                                <input  class="form-control-lg" id="location"  hidden="true"></br>
                                <!-- <div class="select-location-btn">
                                    <div class="select-location-container">
                                        <button  id="select-location" class="location-button" type="button">
                                            <h4>Select location in the map</h4>
                                        </button>
                                    </div>
                                    <div class="location-icon"> <i class="fa fa-map-marker" aria-hidden="true"></i></div>
                                </div> -->
                            </div>
                        </div>

                        <div class="form-element">
                            <div class="tooltip-container" class="hoverable-are">
                                <div class="file-input-container">
                                    <label for="verification_document" class="file-label">Choose Verification Document</label>
                                    <input type="file" name="verification_document" id="verification_document" class="file-input">
                                    <div class="tooltip">
                                        <h5>Documents such as business registration certificate or any other document which can verify your business is legitimate.</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-element">
                            <label for="password">Password</label>
                            <input class="form-control-lg" type="password" name="password" id="password" placeholder="Password" required>
                            <!-- <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i> -->
                            <h5>Please use a strong password. Password minimum length should be least 8 characters long containing at least one uppercase letter, one digit, and one symbol.</h5>
                        </div>
                    </div>

            <div class="row">
                <button class="btn btn-medium" id="rental-service-signup" name="submit" value="Sign Up" onclick="rentalServiceSignup(event)" > Sign Up </button>
                <!-- <button class="btn-cancel btn-medium" id="customer-signup-cancel" name="submit" value="cancel">Cancel</button> -->
        
                <script>
                    let eyeicon = document.getElementById("eyeicon");
                    let password = document.getElementById("password");

                    eyeicon.onclick = function() {
                        if(password.type == "password"){
                            password.type = "text";
                            eyeicon.className = "fa fa-eye";

                        }else{
                            password.type = "password";
                            eyeicon.className =  "fa fa-eye-slash";
                        }
                    }
                </script>

                <div id="error-message"></div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-----------------------------------------------------Rental Services ------------------------------------------------------->

<div class="form-container">    
    <div class="popupFormRental" id="popupForm">
        <div class="form-common">
            <div class="form-common-content">
                <div class="close-btn" onclick="signupToggleRental()"><i class="fa fa-times" aria-hidden="true"></i></div>
                <form action="" class="flex-d">
                    <h2>Rental Service Sign Up</h2>

                    <?php if (isset($errors)) : ?>
                        <div><?= implode('<br>', $errors) ?></div>
                    <?php endif; ?>
                    <div class="row align-items-start">

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">    
                        <div class="form-element">
                            <label for="business_name">Business Name</label>
                            <input class="form-control-lg" type="text" name="name" id="business_name"> </br>
                        </div>

                        <div class="form-element">
                            <label for="registration_number">Business Registration Number/NIC</label>
                            <input class="form-control-lg" type="text" name="regNo" id="registration_number"></br>
                        </div>

                        <div class="form-element">
                            <label for="mobile_number">Mobile Number</label>
                            <input class="form-control-lg" type="text" name="mobile" id="number"></br>
                        </div>

                        <div class="form-element">
                            <label for="email">Email Address</label>
                            <input class="form-control-lg" type="text" name="email" id="email"></br>
                        </div>

                        <div class="form-element">
                            <label for="address">Address</label>
                            <textarea class="address-text" name="address" id="address" required> </textarea></br>
                        </div>
                    </div>

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                        <div class="form-element">
                            <label for="address">Location</label><br>
                            <div class="location-button-container"  id="location-button-container">
                                <input  class="form-control-lg" id="location"  hidden="true"></br>
                                <!-- <div class="select-location-btn">
                                    <div class="select-location-container">
                                        <button  id="select-location" class="location-button" type="button">
                                            <h4>Select location in the map</h4>
                                        </button>
                                    </div>
                                    <div class="location-icon"> <i class="fa fa-map-marker" aria-hidden="true"></i></div>
                                </div> -->
                            </div>
                        </div>

                        <div class="form-element">
                            <div class="tooltip-container" class="hoverable-are">
                                <div class="file-input-container">
                                    <label for="verification_document" class="file-label">Choose Verification Document</label>
                                    <input type="file" name="verification_document" id="verification_document" class="file-input">
                                    <div class="tooltip">
                                        <h5>Documents such as business registration certificate or any other document which can verify your business is legitimate.</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-element">
                            <label for="password">Password</label>
                            <input class="form-control-lg" type="password" name="password" id="password" placeholder="Password" required>
                            <!-- <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i> -->
                            <h5>Please use a strong password. Password minimum length should be least 8 characters long containing at least one uppercase letter, one digit, and one symbol.</h5>
                        </div>
                    </div>

            <div class="row">
                <button class="btn btn-medium" id="rental-service-signup" name="submit" value="Sign Up" onclick="rentalServiceSignup(event)" > Sign Up </button>
                <!-- <button class="btn-cancel btn-medium" id="customer-signup-cancel" name="submit" value="cancel">Cancel</button> -->
        
                <script>
                    let eyeicon = document.getElementById("eyeicon");
                    let password = document.getElementById("password");

                    eyeicon.onclick = function() {
                        if(password.type == "password"){
                            password.type = "text";
                            eyeicon.className = "fa fa-eye";

                        }else{
                            password.type = "password";
                            eyeicon.className =  "fa fa-eye-slash";
                        }
                    }
                </script>

                <div id="error-message"></div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function signupToggleCustomer(){
        var element1;
        element1 = document.querySelector('.popupFormCustomer');
        element1.classList.toggle("popupFormCustomer-active");     
    } 
    function signupToggleGuide(){
        var element2;
        element2 = document.querySelector('.popupFormGuide');
        element2.classList.toggle("popupFormGuide-active");
    }
    function signupToggleRental(){
        var element3;
        element3 = document.querySelector('.popupFormRental');
        element3.classList.toggle("popupFormRental-active");
    }
</script>




        



       