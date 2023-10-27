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
    

    <label for="name">Name</label>
    <input type="text" name="name" id="name" required>

    <label for="address">Address</label>
    <input type="text" name="address" id="address" required>

    <label for="email">Email</label>
    <input type="text" name="email" id="email" required>

    <label for="number">Number</label>
    <input type="text" name="number" id="number" required>

    <label for="nic">NIC Number</label>
    <input type="text" name="nic" id="nic" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <input type="submit" name="submit" value="Signup">
    </form>
    <!-- Rental Services -->
    <form hidden id="rental-service" action="<?= ROOT_DIR ?>/signup" method="post">
    <h2>Rental Services Sign Up</h2>

    <?php if (isset($errors)): ?>
        <div><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>

    <label for="business_name">Business Name</label>
    <input type="text" name="name" id="business_name">

    <label for="address">Address</label>
    <input type="text" name="address" id="address">

    <label for="registration_number">Business Registration Number/NIC</label>
    <input type="text" name="regNo" id="registration_number">

    <label for="mobile_number">Mobile Number</label>
    <input type="text" name="mobile" id="mobile_number">

    <label for="email">Email Address</label>
    <input type="text" name="email" id="email">

    <div class="file-input-container">
        <label for="verification_document" class="file-label">Choose Verification Document</label>
        <input type="file" name="verification_document" id="verification_document" class="file-input">
    </div>
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
        
    
    <label for="name">Name</label>
    <input type="text" name="name" id="name" required>

    <label for="address">Address</label>
    <input type="text" name="address" id="address" required>

    <label for="nic">NIC</label>
    <input type="text" name="nic" id="nic" required>

    <label for="mobile_number">Mobile Number</label>
    <input type="text" name="mobile_number" id="mobile_number" required>

    <label for="email">Email Address</label>
    <input type="text" name="email" id="email" required>

    <label for="gender">Gender</label>
    <select name="gender" id="gender" required>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select>

    <div class="file-input-container">
        <label for="verification_document" class="file-label">Choose Verification Document</label>
        <input type="file" name="verification_document" id="verification_document" class="file-input">
    </div>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

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




