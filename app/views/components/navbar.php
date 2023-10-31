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
    <?php if (isset($_SESSION['USER'])){ $user = $_SESSION['USER'];   ?>

        <!-- profile avatar with dropdown -->

 <div class="profile-avatar">
        <img src="<?php echo ROOT_DIR?>/assets/images/1.png" alt="profile picture">
        <div class="dropdown-menu" id="nav-dropdown">
            <ul>
                <li><a href="<?= ROOT_DIR ?>/profile">Profile</a></li>
                <li><a href="<?= ROOT_DIR ?>/settings">Settings</a></li>
                <li><a href="<?= ROOT_DIR ?>/logout">Logout</a></li>
            </ul>
        </div>
    </div>


    <?php }else {  ?>


        
    <div class="login-button"><a href="<?= ROOT_DIR ?>/login">Login</a></div>

    <?php } ?>
</nav>

<script>
// Get the profile avatar element and the dropdown menu
var profileAvatar = document.querySelector('.profile-avatar');
var dropdownMenu = document.getElementById('nav-dropdown');

// Toggle the dropdown menu when clicking on the profile avatar
profileAvatar.addEventListener('click', function(event) {
    // Prevent the default behavior of the anchor tags
    event.preventDefault();

    // Toggle the display style of the dropdown menu
    if (dropdownMenu.style.display === 'block') {
        dropdownMenu.style.display = 'none';
    } else {
        dropdownMenu.style.display = 'block';
    }
});

// Close the dropdown menu if the user clicks outside of it
document.addEventListener('click', function(event) {
    if (!profileAvatar.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
    }
});

// Prevent event propagation when clicking on dropdown links
dropdownMenu.addEventListener('click', function(event) {
    event.stopPropagation();
});


    </script>