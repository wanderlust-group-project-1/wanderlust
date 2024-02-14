<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar-auth.php');
?>

    <div class="login-container">
        
        <!-- <div class="login-container-col"> -->
            <div class="column">
                <div class="login-form">
                <form  id="loginForm" action="<?=ROOT_DIR?>/login" method="post">

                    <h2>login</h2>
                     <p>Welcome! Please fill email and password to sign in to your account.</p>


                     <?php if(isset($errors)): ?>
                     <div>  <?= implode('<br>', $errors)?>  </div>
                     <?php endif; ?>
                    
                    <label class="label-class" for="email">Email</label>
                    <input type="text" name="email" id="email" placeholder="Email" required>
                    <label class="label-class" for="password" >Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    
                    <div class="message-text">
                     <a href="#">Forgot your password?</a>
                    </div>
                     <!-- <input type="submit" name="submit" value="login"> -->
                    <button class="btn btn-full" id="submit" name="submit" value="login"> login </button>
                    <!-- <button  value="login"> login </button> -->


                    
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



