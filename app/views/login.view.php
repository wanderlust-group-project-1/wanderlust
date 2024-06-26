<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar-auth.php');
?>

    <div class="login-container">
        <div class="customer-bg-image">
            <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
        </div>
        
            <div class="column">
                
                <div class="login-image">
                    <img src="<?php echo ROOT_DIR?>/assets/images/login.jpg" alt="login-image" class="login-image">
                    <!-- <img src="<?php echo ROOT_DIR?>/assets/images/logo.png" alt="overlay-logo" class="overlay-logo"> -->
                </div>

                <div class="login-form">
                    <form  id="loginForm" action="<?=ROOT_DIR?>/login" method="post">
                    <!-- <div class="logo-login">
                        
                    </div> -->

                    <h2>login</h2>
                    <p>Welcome! Please fill email and password to sign in to your account.</p>


                    <?php if(isset($errors)): ?>
                    <div>  <?= implode('<br>', $errors)?>  </div>
                    <?php endif; ?>
                    
                    <div class="login-input">
                        <input type="text" name="email" id="email" placeholder="Email" required>
                        <div class="login-icon">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="login-input">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <div class="login-icon">
                        <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- <label><input type="checkbox">Remember Me</label> -->
                        <h4><a href="forgotPassword">Forgot your password?</a></h4>
                    </div>
            
                    <!-- <button class="btn btn-full" id="submit" name="submit" value="login"> login </button> -->
                    <button class="btn-text-green border center miw-200px" id="submit" name="submit" value="login"> Login </button>

                    <h4>Don't have an account? <a href="<?=ROOT_DIR?>/signup">Signup</a></h4>
                    


                </form>
                <!-- <a href="<?=ROOT_DIR?>/signup" title="Signup">Signup</a>
                <a href="<?=ROOT_DIR?>" title="Home">Home</a> -->
                </div>
            </div>

            <!-- <div class="column">
                <img src="/app/public/assets/images/background.jpg">
            </div> -->
    </div>


    <script src="<?=ROOT_DIR?>/assets/js/login.js"></script>

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
    
    <?php
require_once('../app/views/layout/footer.php');


?>