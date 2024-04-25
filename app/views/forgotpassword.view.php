<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar-auth.php');
?>

    <div class="login-container">
        
            <div class="column">
                
                <div class="login-image">
                    <img src="<?php echo ROOT_DIR?>/assets/images/login.jpg" alt="login-image" class="login-image">
                    <!-- <img src="<?php echo ROOT_DIR?>/assets/images/logo.png" alt="overlay-logo" class="overlay-logo"> -->
                </div>

                <div class="login-form">
                    <form  id="loginForm" action="<?=ROOT_DIR?>/login" method="post">
                    <!-- <div class="logo-login">
                        
                    </div> -->

                    <h2>Verify Email</h2>
                    <p>Enter your email to verify your account.</p>
               


                    <?php if(isset($errors)): ?>
                    <div>  <?= implode('<br>', $errors)?>  </div>
                    <?php endif; ?>
                    
                    <div class="login-input">
                        <input type="text" name="email" id="email" placeholder="Email" required>
                        <div class="login-icon">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        </div>
                    </div>
        

            
                    <button class="btn btn-full" id="submit" name="submit" value="verify"> Verify Email </button>
               
                    <h4> <a href="<?=ROOT_DIR?>/login">Back to Login</a></h4>
                    


                </form>
                <!-- <a href="<?=ROOT_DIR?>/signup" title="Signup">Signup</a>
                <a href="<?=ROOT_DIR?>" title="Home">Home</a> -->
                </div>
            </div>

            <!-- <div class="column">
                <img src="/app/public/assets/images/background.jpg">
            </div> -->
    </div>


    <!-- <script src="<?=ROOT_DIR?>/assets/js/login.js"></script> -->



    
    <?php
require_once('../app/views/layout/footer.php');


?>

<script>
    // Verify Email

    $('#submit').click(function(e) {
        e.preventDefault();
        showLoader();
        var email = $('#email').val();
        var data = {
            email: email
        };
        $.ajax({
            url: '<?= ROOT_DIR ?>/api/forgotPassword/email',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                console.log(response);
                if (response.success) {
                    alertmsg(response.message, 'success');
                    
                    // window.location.href = '<?= ROOT_DIR ?>/login';
                    hideLoader();
                } else {
                    alertmsg(response.message, 'error');
                    hideLoader();
                }
            },
            error: function(response) {
                console.log(response);
                alertmsg(response.responseJSON.message, 'error');
            }
        });
    });
</script>