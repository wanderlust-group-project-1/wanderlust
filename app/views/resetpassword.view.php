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
                    <form  id="resetform" method="post">
                    <!-- <div class="logo-login">
                        
                    </div> -->

                    <h2>Reset Password</h2>
                    <p>Enter New Password</p>


                    <?php if(isset($errors)): ?>
                    <div>  <?= implode('<br>', $errors)?>  </div>
                    <?php endif; ?>
                    <input type="hidden" name="token"  id="token" value="<?= $token ?>">
                    
                    <div class="login-input">
                        <input type="password" name="password" id="password" placeholder="New Password" required>
                        <div class="login-icon">
                        <i class="fa fa-eye-slash" aria-hidden="true" id="eyeicon"></i>
                        </div>
                    </div>
                    <div class="login-input">
                        <input type="password" name="verifypassword" id="verifypassword" placeholder="Verify Password" required>
                        <div class="login-icon">
                        <i class="fa fa-eye-slash" aria-hidden="true" id="verifyeyeicon"></i>
                        </div>
                    </div>

                   
            
                    <button class="btn btn-full" id="submit" name="submit" value="login"> Reset </button>
               
                    <h4><a href="<?=ROOT_DIR?>/login">Back to Login</a></h4>
                    


                </form>
                <!-- <a href="<?=ROOT_DIR?>/signup" title="Signup">Signup</a>
                <a href="<?=ROOT_DIR?>" title="Home">Home</a> -->
                </div>
            </div>

            <!-- <div class="column">
                <img src="/app/public/assets/images/background.jpg">
            </div> -->
    </div>



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


        let verifyeyeicon = document.getElementById("verifyeyeicon");
        let verifypassword = document.getElementById("verifypassword");
        verifyeyeicon.onclick = function() {
            if(verifypassword.type == "password"){
                verifypassword.type = "text";
                verifyeyeicon.className = "fa fa-eye";
            }else{
                verifypassword.type = "password";
                verifyeyeicon.className =  "fa fa-eye-slash";
            }
        }


        $(document).ready(function(){
            $(document).on('submit', '#resetform', function(e){
                e.preventDefault();

                // use ajax to submit form json

                password = $('#password').val();
                verifypassword = $('#verifypassword').val();
                token = $('#token').val();

                if(password != verifypassword){
                    alertmsg('Passwords do not match','error');
                    return;
                }

                // password regex
                var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
                if(!passwordRegex.test(password)){
                    alertmsg('Password must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters','error');
                    return;
                }



                $.ajax({
                    url: '<?=ROOT_DIR?>/api/forgotPassword/change',
                    method: 'POST',
                    data: JSON.stringify({
                        password: password,
                        token: token
                    }),
                    contentType: 'application/json',
                    success: function(response){
                        if(response.success){
                            alertmsg(response.message,'success');
                            setTimeout(() => {
                                window.location.href = '<?=ROOT_DIR?>/login';
                            }, 2000);
                        }else{
                            alertmsg(response.message,'error');
                        }
                    }
                })



                
                
            })
        })


    </script>
    
    <?php
require_once('../app/views/layout/footer.php');


?>