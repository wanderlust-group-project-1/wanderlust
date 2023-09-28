<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar.php');


?>
 <div class="login-container">
    <!-- <h1>Signup</h1> -->
<div class="login-form">
<div id="select" class="col  ">
    <div class="row">
       
        <div class="signup-card"  onclick="load('customer')">
            Customer
        </div>
    </div>
    <div class="row">
       
       <div class="signup-card"  onclick="load('rental-service')">
          Rental Services
       </div>
   </div>
   <div class="row">
       
       <div class="signup-card"  onclick="load('guide')">
           Guide
       </div>
   </div>
</div>
    

    <form hidden id="customer"   action="<?=ROOT_DIR?>/signup" method="post">
    <h2>Customer Sign Up</h2>
    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
    

        <label for="email">Email</label>
        <input type="text" name="email" id="email">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <input type="submit" name="submit" value="Signup">

    </form>
    <!-- Rental Services -->
    <form hidden id="rental-service"  action="<?=ROOT_DIR?>/signup" method="post">
    <h2>Rental Services Sign Up</h2>

    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
        
    
            <label for="email">Email</label>
            <input type="text" name="email" id="email">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            <input type="submit" name="submit" value="Signup">

        </form>

        <!-- Guide -->
        <form hidden id="guide"  action="<?=ROOT_DIR?>/signup" method="post">
        <h2>Guide Sign Up</h2>

    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
        
    
            <label for="email">Email</label>
            <input type="text" name="email" id="email">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            <input type="submit" name="submit" value="Signup">

        </form>

    <p>
                Have an account? <a href="<?=ROOT_DIR?>/login">Login</a>
            <p>

    <!-- <a href="<?=ROOT_DIR?>/login" title="Login">Login</a>
    <br>
    <a href="<?=ROOT_DIR?>" title="Home">Home</a> -->

    </div>
 </div>


 
 <script>
        

    </script>
    <script src="<?=ROOT_DIR?>/assets/js/signup.js"></script>

    <?php
require_once('../app/views/layout/footer.php');


?>




