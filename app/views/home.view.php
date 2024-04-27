<?php
require_once('../app/views/layout/header.php');

if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'rentalservice') {
    $user = $_SESSION['USER'];
    require_once('../app/views/navbar/rental-navbar.php');

}else if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'customer') {
    $user = $_SESSION['USER'];
    require_once('../app/views/navbar/customer-navbar.php');

}else if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'guide') {
    $user = $_SESSION['USER'];
    require_once('../app/views/navbar/guide-navbar.php');
} else if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'admin') {
    require_once('../app/views/navbar/admin-navbar.php');
} else {
    require_once('../app/views/navbar/logout-navbar.php');
}
?>

<div class="customer-bg-image">
    <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
</div>

<?php require_once('../app/views/sections/hero.php');
?>

<?php require_once('../app/views/sections/about.php');
?>

<?php require_once('../app/views/sections/guide.php');
?>

<?php require_once('../app/views/sections/rental-service.php');
?>

<?php require_once('../app/views/sections/blog.php');
?>

<?php require_once('../app/views/sections/tips.php');
?>

<?php require_once('../app/views/sections/complaints.php');
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