<!-- <div class="main">
    <div class="home-container row">
        <div class="col col-2">
            <h1 class="hero-title">Campers,</h1>
            <p class="hero-description"><b>Your Gateway to Outdoor Adventures:</b> <br>
                Gear Up & Go with Expert Guides!</p>
            <div class="section-button"><a href="#">Get Started</a></div>
        </div>
        <div class="col col-2">
            <img src="<?= ROOT_DIR ?>/assets/images/hero.png" alt="Hero Image">
        </div>

    </div>

</div> -->

<div class="home_main">
    <div class="home_container">
        <div class="home_card">
            <div class="home_text">
                <div class="home_title">Campers,</div>
                <div class="home_sub">
                    <p>Your Gateway to Outdoor Adventures:</p>
                    <p>Gear Up &amp; Go with Expert Guides!</p>
                </div>
            </div>

        <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER'])) {
            $user = $_SESSION['USER']; ?>

            <div class="home_button">
                <a href="#" class="btn">Get Started</a>
            </div>

        <?php } else {  ?>
            <div class="home_button">
            <a href="<?= ROOT_DIR ?>/login" class="btn">Get Started</a>
            </div>
        <?php } ?>
    </div>

        <div class="home_card">
            <img class="home_img" src="<?= ROOT_DIR ?>/assets/images/hero.png" alt="Hero Image">
        </div>
    </div>
</div>