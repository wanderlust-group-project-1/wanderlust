<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar-auth.php');
?>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY  ?>&libraries=places&callback=initialize" async defer></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<div class="signup-container" id="blur">
    <div class="customer-bg-image">
        <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
    </div>
    <div class="column">
        <div class="signup-form">
        <h2>Who are you?</h2>
        <p>It's time to start your journey, join us! Register here.</p>
            <div class="main gap-2">
                <div class="row-btn">
                    <button class="signup-card zoom-inn zoom-out" onclick="signupToggleCustomer()" >
                        <div class="btn-div">
                            <h3>Customer</h3>
                            <h4>Plan your journey now!</h4>
                        </div>
                        <div class="icon">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                    </button>       
                </div>
                <div class="row-btn">
                    <button class="signup-card zoom-inn zoom-out" onclick="signupToggleGuide()">
                        <div class="btn-div">
                            <h3>Guide</h3>
                            <h4>Explore the extraordinary now!</h4>
                        </div>
                        <div class="icon">
                        <i class="fa fa-compass" aria-hidden="true"></i>
                        </div>
                    </button>
                </div>
                <div class="row-btn">
                    <button class="signup-card zoom-inn zoom-out" onclick="signupToggleRental()">
                        <div class="btn-div">
                            <h3>Rental Service</h3>
                            <h4>Register your business now!</h4>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        </div>
                    </button>
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
                <form action="" class="flex-d" id="customer">
                    <h2>Customer Sign Up</h2>

                    <?php if (isset($errors)) : ?>
                        <div> <?= implode('<br>', $errors) ?> </div>
                    <?php endif; ?>
                    
                    <div class="row align-items-start">
                        <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                            <div class="form-element">
                                <label class="placeholder" for="name">Name</label>
                                <input class="form-control-lg" type="text" name="name" id="name" required>
                            </div>

                            <div class="form-element">
                                <label for="address">Address</label>
                                <input class="form-control-lg" type="text" name="address" id="address" required >
                            </div>

                            <div class="form-element">
                                <label for="email">Email</label>
                                <input class="form-control-lg" type="text" name="email" id="email" required>
                            </div>
                        </div>
                
                        <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                            <div class="form-element">
                                <label for="number">Moble Number</label>
                                <input class="form-control-lg" type="text" name="number" id="number" required>
                            </div>

                            <div class="form-element">
                                <label for="nic">NIC</label>
                                <input class="form-control-lg" type="text" name="nic" id="nic" required>
                            </div>

                            <div class="form-element">
                                <label for="password">Password</label>
                                <div class="pwd-input">
                                    <input class="form-control-lg" type="password" name="password" id="password" required>
                                    <div class="pwd-icon">
                                        <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <button class="btn-text-green border center miw-200px" id="customer-signup" name="submit" value="signup">Sign Up</button>
                            <!-- <button class="btn-cancel btn-medium" id="customer-signup-cancel" name="submit" value="cancel">Cancel</button> -->
                        
                            <!-- <script>
                                let eyeicon = document.getElementById("eyeicon");
                                let passwordc = document.getElementById("password");

                                eyeicon.onclick = function() {
                                    if(passwordc.type == "password"){
                                        passwordc.type = "text";
                                        eyeicon.className = "fa fa-eye";
                                    }else{
                                        passwordc.type = "password";
                                        eyeicon.className =  "fa fa-eye-slash";
                                    }
                                }
                            </script> -->
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
                <form action="" class="flex-d" id="guide">
                    <h2>Guide Sign Up</h2>

                    <?php if (isset($errors)) : ?>
                        <div><?= implode('<br>', $errors) ?></div>
                    <?php endif; ?>
                    <div class="row align-items-start">

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">    
                        <div class="form-element">
                            <label for="business_name">Name</label>
                            <input class="form-control-lg" type="text" name="name" id="name">
                        </div>

                        <div class="form-element">
                            <label for="registration_number">NIC</label>
                            <input class="form-control-lg" type="text" name="nic" id="nic">
                        </div>

                        <div class="form-element">
                            <label for="gender"> Gender </label>
                            <select class="form-control-lg" name="gender" id="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select></br>

                        </div>

                        <div class="form-element">
                            <label for="mobile_number">Mobile Number</label>
                            <input class="form-control-lg" type="text" name="mobile" id="number">
                        </div>

                        <div class="form-element">
                            <label for="email">Email</label>
                            <input class="form-control-lg" type="text" name="email" id="email">
                        </div>
                        </div>

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                        <div class="form-element">
                            <label for="address">Address</label>
                            <input class="form-control-lg" type="text" name="address" id="address" required>
                        </div>
                    

                    
                        <div class="form-element">
                            <label for="address">Location</label><br>
                            <div class=""  id="location-button-container">
                                <div class="select-location-btn">
                                    <div class="select-location-container">
                                        <label  id="select-location" class=" btn-text-green center border miw-200px location-button w-100" type="button">
                                       
                                            <!-- Select location in the map -->
                                            <i class="fa fa-map-marker" aria-hidden="true"></i> Select location
                                            </label>
                                    </div>
                                    <!-- <div class="location-icon"> <i class="fa fa-map-marker" aria-hidden="true"></i></div> -->
                                </div>
                            </div>
                        </div>

                        <div class="form-element">
                            <!-- <div class="tooltip-container" class="hoverable-are"> -->
                                <div class="file-input-container">
                                    <div><label>Upload verification Documents</label>
                                    </div>
                                    <div class="file-upload-button">
                                    <label for="verification_document" class="btn-text-green border center miw-200px file-label w-100">Browse here</label>
                                    </div>
                                    <input type="file" name="verification_document" id="verification_document" class="file-input">
<!-- d -->

                                </div>
                            <!-- </div> -->
                        </div>

                        <div class="form-element">
                            <label for="password">Password</label>
                            <div class="pwd-input">
                                <input class="form-control-lg" type="password" name="password" id="password" required>
                                <div class="pwd-icon">
                                    <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

            <div class="row">
                <button class="btn-text-green border center miw-200px" id="rental-service-signup" name="submit" value="Sign Up" onclick="guideSignup(event)" > Sign Up </button>
                <!-- <button class="btn-cancel btn-medium" id="customer-signup-cancel" name="submit" value="cancel">Cancel</button> -->
        
                <!-- <script>
                    let eyeicon = document.getElementById("eyeicon");
                    let passwordg = document.getElementById("password");
                        eyeicon.onclick = function() {
                            if(passwordg.type == "password"){
                                passwordg.type = "text";
                                eyeicon.className = "fa fa-eye";
                            }else{
                                passwordg.type = "password";
                                eyeicon.className =  "fa fa-eye-slash";
                            }
                        }
                </script> -->
                <div id="error-message"></div>
                </div>
            </div>
        </form>
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
                <form action="" class="flex-d" id="rental-service">
                    <h2>Rental Service Sign Up</h2>

                    <?php if (isset($errors)) : ?>
                        <div><?= implode('<br>', $errors) ?></div>
                    <?php endif; ?>
                    <div class="row align-items-start">

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">    
                        <div class="form-element">
                            <label class="rental-label" for="business_name">Business Name</label>
                            <input class="form-control-lg" type="text" name="name" id="business_name">
                        </div>

                        <div class="form-element">
                            <label class="rental-label" for="registration_number">Business Registration Number / NIC</label>
                            <input class="form-control-lg" type="text" name="regNo" id="registration_number">
                        </div>

                        <div class="form-element">
                            <label class="rental-label" for="mobile_number">Mobile Number</label>
                            <input class="form-control-lg" type="text" name="mobile" id="number">
                        </div>

                        <div class="form-element">
                            <label class="rental-label" for="email">Email</label>
                            <input class="form-control-lg" type="text" name="email" id="email">
                        </div>
                        </div>

                    <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                        <div class="form-element">
                            <label class="rental-label" for="address">Address</label>
                            <input class="form-control-lg" type="text" name="address" id="address" required>
                        </div>
                    

                        <div class="form-element">
                            <label for="address">Location</label><br>
                            <div class=""  id="location-button-container">
                                <div class="select-location-btn">
                                    <div class="select-location-container">
                                        <label  id="select-location" class=" btn-text-green center border location-button w-100" type="button">
                                       
                                            <!-- Select location in the map -->
                                            <i class="fa fa-map-marker" aria-hidden="true"></i> Select location
                                            </label>
                                    </div>
                                    <!-- <div class="location-icon"> <i class="fa fa-map-marker" aria-hidden="true"></i></div> -->
                                </div>
                            </div>
                        </div>

                        <div class="form-element">
                            <!-- <div class="tooltip-container" class="hoverable-are"> -->
                                <div class="file-input-container">
                                    <div><label>Upload verification Documents</label>
                                    </div>
                                    <div class="file-upload-button">
                                    <label for="verification_document-rental" class="btn-text-green center border file-label  file-label-rental w-100">Browse here</label>
                                    </div>
                                    <input type="file" name="verification_document" id="verification_document-rental" class="file-input">
<!-- d -->

                                </div>
                            <!-- </div> -->
                        </div>


                        <div class="form-element">
                            <label class="rental-label" for="password">Password</label>
                            <div class="pwd-input">
                                <input class="form-control-lg" type="password" name="password" id="password" required>
                                <div class="pwd-icon">
                                    <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

            <div class="row">
                <button class="btn-text-green border center miw-200px" id="rental-service-signup" name="submit" value="Sign Up" onclick="rentalServiceSignup(event)" > Sign Up </button>
                <!-- <button class="btn-cancel btn-medium" id="customer-signup-cancel" name="submit" value="cancel">Cancel</button> -->
        
                <!-- <script>
                    let eyeicon = document.getElementById("eyeicon");
                    let passwordr = document.getElementById("password");

                    eyeicon.onclick = function() {
                        if(passwordr.type == "password"){
                            passwordr.type = "text";
                            eyeicon.className = "fa fa-eye";

                        }else{
                            passwordr.type = "password";
                            eyeicon.className =  "fa fa-eye-slash";
                        }
                    }
                </script> -->

                <div id="error-message"></div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Location Modal -->
<div id="location-modal" class="location-modal modal">

    <div class="modal-content flex-d-c gap-2 pt-5">
    <span class="close">&times;</span>


<div class="flex-d-c gap-3 mt-4">
        <input id="pac-input" class="controls form-control-lg" type="text" placeholder="Enter Location" />

        <div id="map-canvas" class="map-canvas"> </div>

            <input type="text" class="" id="latitude"  hidden/>
            <input type="text" class="" id="longitude" hidden/>

        <div class="flex-d justify-content-center">
            <button id="confirm-location" class="location-button btn-text-green border center" type="button">Confirm Location</button>
        </div>

        </div>
    </div>


</div>



<script>
    function signupToggleCustomer(){
        var element1, blur;
        element1 = document.querySelector('.popupFormCustomer');
        element1.classList.toggle("popupFormCustomer-active");
        blur = document.getElementById('blur');
        blur.classList.toggle("active");     
    } 
    function signupToggleGuide(){
        var element2,blur;
        element2 = document.querySelector('.popupFormGuide');
        element2.classList.toggle("popupFormGuide-active");
        blur = document.getElementById('blur');
        blur.classList.toggle("active");
    }
    function signupToggleRental(){
        var element3, blur;
        element3 = document.querySelector('.popupFormRental');
        element3.classList.toggle("popupFormRental-active");
        blur = document.getElementById('blur');
        blur.classList.toggle("active");
    }

    // eye icon for password
    $(document).ready(function(){
        $(document).on('click', '#eyeicon', function(){
            // this closest function will find the closest parent element of the current element
            var password = $(this).closest('.pwd-input').find('input');
            if(password.attr('type') == 'password'){
                password.attr('type', 'text');
                $(this).removeClass('fa-eye-slash');
                $(this).addClass('fa-eye');
            }else{
                password.attr('type', 'password');
                $(this).removeClass('fa-eye');
                $(this).addClass('fa-eye-slash');
            }

        });
    });


</script>


<script src="<?= ROOT_DIR ?>/assets/js/signup.js"></script>
<script src="<?= ROOT_DIR ?>/assets/js/map.js"></script>

<?php
require_once('../app/views/layout/footer.php');


?>







       
