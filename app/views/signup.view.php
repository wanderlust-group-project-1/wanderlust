<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar.php');


?>
 <div class="login-container">
    <!-- <h1>Signup</h1> -->
<div class="login-form">
<div id="select" class="col col-2 ">
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
    

    <form hidden id="customer" class="signup-form"  action="<?=ROOT_DIR?>/signup" method="post">

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

    <?php if(isset($errors)): ?>
        <div>  <?= implode('<br>', $errors)?>  </div>
    <?php endif; ?>
        
    
            <label for="email">Email</label>
            <input type="text" name="email" id="email">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            <input type="submit" name="submit" value="Signup">

        </form>
    <a href="<?=ROOT_DIR?>/signup" title="Signup">Signup</a>


    <a href="<?=ROOT_DIR?>/login" title="Login">Login</a>
    <br>
    <a href="<?=ROOT_DIR?>" title="Home">Home</a>

    </div>
 </div>


 
 <script>
        function load(id){
            // use css display none and block
            document.getElementById(id).style.display = "flex";
            document.getElementById('select').style.display = "none";

           
        }

    </script>
 <!-- script -->
    <script src="<?=ROOT_DIR?>/assets/js/signup.js"></script>

    <?php
require_once('../app/views/layout/footer.php');


?>




