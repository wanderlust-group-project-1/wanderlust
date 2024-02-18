<?php
require_once('../app/views/layout/header.php');
//require_once('../app/views/components/navbar-auth.php');
?>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY  ?>&libraries=places&callback=initialize" async defer></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">








<div class="signup-container">
    <div class="column">

    <div class="signup-form">
        <h2>Who are you?</h2>
        <p>It's the time to start your journey! Register here.</p>
            <div class="main">
                <div class="row-btn">
                    <button class="signup-card" onclick="load('customer')">
                        <div class="btn-div">
                            <h3>Customer</h3>
                            <h4>Plan your journey now!</h4>
                        </div>
                    </button>
                    <i class="fa fa-users" aria-hidden="true"></i>
                </div>
                <div class="row-btn">
                    <button class="signup-card" onclick="load('guide')">
                        <div class="btn-div">
                            <h3>Guide</h3>
                            <h4>Explore the extraordinary now!</h4>
                        </div>
                    </button>
                    <i class="fa fa-compass" aria-hidden="true"></i>
                </div>
                <div class="row-btn">
                    <button class="signup-card" onclick="load('rental-service')">
                        <div class="btn-div">
                            <h3>Rental Service</h3>
                            <h4>Register your business now!</h4>
                        </div>
                    </button>
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                </div>
            </div>

            <h4>Have an account? <a href="<?= ROOT_DIR ?>/login">Login</a></h4>
            </div>

            <div class="signup-image">
                    <img src="<?php echo ROOT_DIR?>/assets/images/signup.jpg" alt="signup-image" class="signup-image">
                    <img src="<?php echo ROOT_DIR?>/assets/images/logo.png" alt="overlay-logo" class="overlay-logo2">
                </div>
        </div>

        



       