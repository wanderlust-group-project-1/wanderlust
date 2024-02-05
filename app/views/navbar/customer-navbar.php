<header class="header">
    <!-- <nav class="nav__container"> -->
    <nav class="nav__container" style="display: flex;align-items: center;justify-content: space-between;padding-left: 5rem; height: 10rem;">
        <div class="nav__data">
            <a href="#" class="nav__logo">
                <img src="<?= ROOT_DIR ?>/assets/images/logo.png" alt="logo">
            </a>

            <div class="nav__toggle" id="nav-toggle">
                <i class="ri-menu-line nav__burger"></i>
                <i class="ri-close-line nav__close"></i>
            </div>
        </div>

        <!--=============== NAV MENU ===============-->
        <!-- <div class="nav__menu" id="nav-menu"> -->
        <div class="nav__menu" id="nav-menu" style="display: flex;align-items: center;justify-content: space-between;padding-right: 5rem;">
            <ul class="nav__list">
                <li><a href="#" class="nav__link">Home</a></li>

                <!--=============== DROPDOWN 1 ===============-->
                <li class="dropdown__item">
                    <div class="nav__link">
                        Dashboard <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                    </div>

                    <ul class="dropdown__menu">
                        <li>
                            <a href="#" class="dropdown__link">
                                <i class="ri-pie-chart-line"></i> My Booking
                            </a>
                        </li>
                    </ul>
                </li>
                <li><a href="#" class="nav__link">Guides</a></li>
                <li><a href="#" class="nav__link">Rental Services</a></li>
                <li><a href="#" class="nav__link">Blogs</a></li>
                <li><a href="#" class="nav__link">Tips and Knowhows</a></li>

                <!--=============== DROPDOWN 2 ===============-->
                <li class="dropdown__item">
                    <div class="nav__link">
                        Complains <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                    </div>

                    <ul class="dropdown__menu">
                        <li>
                            <a href="#" class="dropdown__link">
                                <i class="ri-user-line"></i> Add Complain
                            </a>
                        </li>

                        <li>
                            <a href="#" class="dropdown__link">
                                <i class="ri-lock-line"></i> My Complains
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- check role avalable or not -->
            <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
                $user = $_SESSION['USER'];   ?>


                <!-- profile avatar with dropdown -->

                <div class="nav__profile-avatar" style="display: flex; align-items: center; padding-left: 3rem">
                    <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="profile picture">
                    <li class="dropdown__item" id="nav-dropdown" >
                        <!-- <div class="nav__link">
                            Complains <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                        </div> -->

                        <ul class="dropdown__menu">
                            <li><a href="<?= ROOT_DIR . ($user->role == 'guide' || $user->role == 'rentalservice' ? '/Dashboard' : '/profile') ?>" class="dropdown__link">Profile</a></li>
                            <li><a href="<?= ROOT_DIR ?>/settings" class="dropdown__link">>Settings</a></li>
                            <li><a href="<?= ROOT_DIR ?>/logout" class="dropdown__link">>Logout</a></li>
                        </ul>
                    </li>
                </div>

            <?php } else if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) && $_SESSION['USER']->role == 'admin') {
                $admin = $_SESSION['USER'];   ?>

                <!-- profile avatar with dropdown -->
                <div class="nav__profile-avatar">
                    <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="profile picture">
                    <li class="dropdown__item" id="nav-dropdown">
                        <ul class="dropdown__menu">
                            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
                            <li><a href="<?= ROOT_DIR ?>/settings">Settings</a></li>
                            <li><a href="<?= ROOT_DIR ?>/logout">Logout</a></li>
                        </ul>
                    </li>
                </div>

            <?php } else {  ?>

 
                <div class="login-button"><a href="<?= ROOT_DIR ?>/login">Login</a></div>

            <?php } ?>
        </div>
    </nav>
</header>

<!--=============== MAIN JS ===============-->
<script src="../../../public/assets/js/navbar.js"></script>
</body>

</html>