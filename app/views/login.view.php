<?php
require_once('../app/views/layout/header.php');
//require_once('../app/views/components/navbar-auth.php');
?>

    <div class="login-container">
        
        <!-- <div class="login-container-col"> -->
            <div class="column">
                
                <div class="login-image">
                    <img src="<?php echo ROOT_DIR?>/assets/images/login.jpg" alt="login-image" class="login-image">
                    <img src="<?php echo ROOT_DIR?>/assets/images/logo.png" alt="overlay-logo" class="overlay-logo">
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
                        <i class="fa fa-user" aria-hidden="true"></i>
                    </div>
                    <div class="login-input">
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </div>

                    <div class="message-text">
                        <label><input type="checkbox">Remember Me</label>
                        <a href="#">Forgot your password?</a>
                    </div>
            
                    <button class="btn btn-full" id="submit" name="submit" value="login"> login </button>
               
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

    
    <?php
require_once('../app/views/layout/footer.php');


?>