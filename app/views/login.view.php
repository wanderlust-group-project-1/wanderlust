<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar-auth.php');


?>
    <div class="login-container">
    <!-- <h1>Login</h1> -->
    <div class="login-form">
    <form  id="loginForm" action="<?=ROOT_DIR?>/login" method="post">
    <!-- <form  id="loginForm" onsubmit="console.log('a')"> -->

    <h2>Login</h2>

    
    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
    
        <label for="email">Email</label>
        <input type="text" name="email" id="email" placeholder="Email" required>
        <label for="password" >Password</label>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <div class="message-text">
                <a href="#">Forgot your password?</a>
            </div>
        <!-- <input type="submit" name="submit" value="login"> -->
        <button id="submit" name="submit" value="login"> login </button>
        <!-- <button  value="login"> login </button> -->


        <p>
                Don't have an account? <a href="<?=ROOT_DIR?>/signup">Signup</a>
        <p>


    </form>
    <!-- <a href="<?=ROOT_DIR?>/signup" title="Signup">Signup</a>
    <a href="<?=ROOT_DIR?>" title="Home">Home</a> -->
    </div>
    </div>


    <script src="<?=ROOT_DIR?>/assets/js/login.js"></script>

    
    <?php
require_once('../app/views/layout/footer.php');


?>



