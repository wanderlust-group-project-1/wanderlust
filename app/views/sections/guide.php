<!-- <div class="guide">
    <div class="home-container row">
        <div class="col col-2">
            <h2 class="about-subtitle">Guides</h2>
            <h1 class="guide-title">Find Guides with Ease!</h1>
            <p class="hero-description">Find your ideal camping guides to simplify your<br> outdoor adventures with ease, ensuring a <br>memorable and stress-free experience.</p>
            <div class="section-button"><a href="#">Book now</a></div>
        </div>
        <div class="col col-2">
            <img class=" locked-aspect-ratio" src=" <?= ROOT_DIR ?>/assets/images/guide.png" alt="Mask group Image">
        </div>
    </div>

</div> -->

<div class="home_second">
    <div class="home_container">
        <div class="home_card">
            <div class="home_text">
                <div class="home_subtitle">Guides</div>
                <div class="home_secondtitle">Find Guides with Ease!</div>
                <div class="home_sub">
                    <p>Find your ideal camping guides to simplify your<br> outdoor adventures with ease, ensuring a <br>memorable and stress-free experience.</p>
                </div>
            </div>

        <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER'])) {
            $user = $_SESSION['USER']; ?>

            <div class="home_button">
                <a href="#" class="btn-text-green border">Book Now</a>
            </div>

        <?php } else {  ?>
            <div class="home_button">
            <a href="<?= ROOT_DIR ?>/login" class="btn-text-green border">Book Now</a>
            </div>
        <?php } ?>
    </div>

        <div class="home_card">
            <img class="home_img" src="<?= ROOT_DIR ?>/assets/images/Guides.png" alt="Hero Image">
        </div>
    </div>
</div>