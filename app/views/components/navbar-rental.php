<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/navbar.css"> -->

<header class="header">
    <!-- <nav class="nav__container"> -->
    <nav class="nav__container" >
        <div class="nav__data">
            <a href="#" class="nav__logo">
                <img class="nav__logo-pic" src="<?= ROOT_DIR ?>/assets/images/logo.png" alt="logo">
            </a>

            <div class="nav__toggle" id="nav-toggle">
                <i class="ri-menu-line nav__burger"></i>
                <i class="ri-close-line nav__close"></i>
            </div>
        </div>
        <div class="nav__menu" id="nav-menu" >
            <ul class="nav__list">
                <!-- check role avalable or not -->
                <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
                    $user = $_SESSION['USER'];   ?>


                    <!-- profile avatar with dropdown -->

                    <li class="dropdown__item" id="nav-dropdown">
                        <div class="nav__profile-avatar" style="display: flex; align-items: center; padding-left: 3rem">
                            <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="profile picture">
                        </div>

                        <ul class="dropdown__menu">
                            <li><a href="<?= ROOT_DIR . ($user->role == 'guide' || $user->role == 'rentalservice' ? '/Dashboard' : '/profile') ?>" class="dropdown__link">Profile</a></li>
                            <li><a href="<?= ROOT_DIR ?>/settings" class="dropdown__link">Settings</a></li>
                            <li><a href="<?= ROOT_DIR ?>/logout" class="dropdown__link">Logout</a></li>
                        </ul>
                    </li>

                <?php } else if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'admin') {
                    $admin = $_SESSION['USER'];   ?>

                    <!-- profile avatar with dropdown -->
                    <li class="dropdown__item" id="nav-dropdown">
                        <div class="nav__profile-avatar" style="display: flex; align-items: center; padding-left: 3rem">
                            <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="profile picture">
                        </div>

                        <ul class="dropdown__menu">
                            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
                            <li><a href="<?= ROOT_DIR ?>/settings">Settings</a></li>
                            <li><a href="<?= ROOT_DIR ?>/logout">Logout</a></li>
                        </ul>
                    </li>

                <?php } else {  ?>


                    <div class="btn-small"><a href="<?= ROOT_DIR ?>/login">Login</a></div>

                <?php } ?>

            </ul>
        </div>
    </nav>
</header>

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