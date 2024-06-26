<link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">


<header class="header ">
    <!-- <nav class="nav__container"> -->
    <nav class="nav__container">
        <div class="nav__data">
            <a href="#" class="nav__logo">
                <img class="nav__logo-pic ml-5" src="<?= ROOT_DIR ?>/assets/images/logo.png" alt="logo">
            </a>

            <div class="nav__toggle" id="nav-toggle">
                <i class="ri-menu-line nav__burger"></i>
                <i class="ri-close-line nav__close"></i>
            </div>
        </div>

        <!--=============== NAV MENU ===============-->
        <!-- <div class="nav__menu" id="nav-menu"> -->
        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <!-- <li><a href="#" class="nav__link">Home</a></li> -->

                <!--=============== DROPDOWN 1 ===============-->
                <!-- <li class="dropdown__item">
                    <div class="nav__link">
                        Dashboard <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                    </div>

                    <ul class="dropdown__menu">
                        <li>
                            <a href="#" class="dropdown__link">
                                My Booking
                            </a>
                        </li>
                    </ul>
                </li>
                <li><a href="#" class="nav__link">Guides</a></li>
                <li><a href="#" class="nav__link">Rental Services</a></li> -->

                <li><a href="/admin/dashboard" class="nav__link mr-5"><i class="fas fa-window-maximize"></i>Dashboard</a></li>
                <!-- <li><a href="#" class="nav__link">Tips and Knowhows</a></li> -->

                <!--=============== DROPDOWN 2 ===============-->
                <!-- <li class="dropdown__item">
                    <div class="nav__link">
                        Complains <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                    </div>

                    <ul class="dropdown__menu">
                        <li>
                            <a href="<?php echo ROOT_DIR ?>/admin/complains" class="dropdown__link">
                                Manage Complains
                            </a>
                        </li>
                    </ul>
                </li> -->


                <!-- check role avalable or not -->
                <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
                    $user = $_SESSION['USER'];   ?>


                    <!-- profile avatar with dropdown -->

                    <!-- <li class="dropdown__item" id="nav-dropdown">
                        <div class="nav__profile-avatar" style="display: flex; align-items: center; padding-left: 3rem">
                            <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="profile picture">
                        </div>
                        <div class="dropdown-menu" id="nav-dropdown">
                            <ul class="dropdown__menu">
                                <li><a href="<?= ROOT_DIR . ($user->role == 'guide' || $user->role == 'admin' ? '/Dashboard' : '/profile') ?>" class="dropdown__link">Dashboard</a></li>
                                <li><a href="<?= ROOT_DIR ?>/settings" class="dropdown__link">>Settings</a></li>
                                <li><a href="<?= ROOT_DIR ?>/logout" class="dropdown__link">>Logout</a></li>
                            </ul>
                        </div>
                    </li> -->

                <?php } else if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'admin') {
                    $admin = $_SESSION['USER'];   ?>

                    <!-- profile avatar with dropdown -->
                    <!-- <li class="dropdown__item" id="nav-dropdown">
                        <div class="nav__profile-avatar" style="display: flex; align-items: center; padding-left: 3rem">
                            <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="profile picture">
                        </div>

                        <ul class="dropdown__menu">
                            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
                            <li><a href="<?= ROOT_DIR ?>/settings">Settings</a></li>
                            <li><a href="<?= ROOT_DIR ?>/logout">Logout</a></li>
                        </ul>
                    </li> -->

                <?php } else {  ?>


                    <div class="btn-small"><a href="<?= ROOT_DIR ?>/login">Login</a></div>

                <?php } ?>

            </ul>
        </div>
    </nav>
</header>

<script>
    const showMenu = (toggleId, navId) => {
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId)

        toggle.addEventListener('click', () => {
            // Add show-menu class to nav menu
            nav.classList.toggle('show-menu')

            // Add show-icon to show and hide the menu icon
            toggle.classList.toggle('show-icon')
        })
    }

    showMenu('nav-toggle', 'nav-menu')
</script>