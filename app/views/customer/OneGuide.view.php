<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');

?>



?>

<div class="dashboard">


    <?php 
    // show(UserMiddleware::getUser());
    ?>
    <div class="guide-dash-main flex-d-c m-6">
        <!-- <h1 class="title mb-2">My Guide Profile</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Guide Profile</a></li>
        </ul> -->

        <div class="guide-availability" id="guide-availability">
            <i class="fas fa-calendar"></i></a>
        </div>

        <div class="guide-profile" id="guide-profile">
            <div class="guide-profile-img">
                <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
            </div>
            <div class="guide-profile-content">
                <h1>Hi, It's <span><?php echo $guide[0]->guide_name; ?></span></h1>
                <h3 class="typing-text"> I'm a <span>Guide</span></h3>
                <p><?= htmlspecialchars($guide[0]->description) ?></p>
                <!-- <p>Hi, I'm Nirmal, a professional tour guide with 5 years of experience. I have a passion for history and culture and love sharing my knowledge with others. I specialize in tours of ancient ruins, temples, and historical sites. I'm also an expert in local cuisine and can recommend the best places to eat in town. Let me show you the beauty of my country and help you create memories that will last a lifetime.</p> -->

            </div>
        </div>

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="card">
                <span class="label">Languages</span>

                <?php
                $languages = explode(',', $guide[0]->languages); // Assuming languages are comma-separated in the database
                ?>

                <?php foreach ($languages as $language) : ?>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p><?= htmlspecialchars(trim($language)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card">
                <span class="label">Certifications</span>

                <?php
                $certifications = explode(',', $guide[0]->certifications); // Assuming languages are comma-separated in the database
                ?>

                <?php foreach ($certifications as $certification) : ?>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p><?= htmlspecialchars(trim($certification)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="card">
                <span class="label">Booking History</span>

                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>23rd March : Micheal Julius</p>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>23rd March : Micheal Julius</p>
                </div>
            </div>
        </div>
    </div>
</div>
