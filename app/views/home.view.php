<?php
require_once('../app/views/layout/header.php');


?>

<nav class="navbar">
    <div class="logo">
        <img src="<?= ROOT_DIR ?>/assets/images/logo.png" alt="logo">
    </div>
    <ul class="nav-menu">
        <li class="nav-menu-item"><a href="#">Home</a></li>
        <li class="nav-menu-item"><a href="#">About</a></li>
        <li class="nav-menu-item"><a href="#">Guides</a></li>
        <li class="nav-menu-item"><a href="#">Rental Services</a></li>
        <li class="nav-menu-item"><a href="#">Blogs</a></li>
        <li class="nav-menu-item"><a href="#">Tips & Knowhows</a></li>
        <li class="nav-menu-item"><a href="#">Complaints</a></li>
    </ul>
    <div class="login-button"><a href="#">Login</a></div>
</nav>

<?php require_once('../app/views/sections/hero.php');
?>

<?php require_once('../app/views/sections/about.php');
?>

<?php require_once('../app/views/sections/guide.php');
?>
<!-- <h1> Home page view </h1>


<h4> Hi <?= $email ?> </h4> -->



<!-- <a href="<?= ROOT_DIR ?>/login" title="Login">Login</a>
<br>
<a href="<?= ROOT_DIR ?>/signup" title="Signup">Signup</a>
<br>
<a href="<?= ROOT_DIR ?>/logout" title="Logout">Logout</a> -->

<?php
require_once('../app/views/layout/footer.php');


?>