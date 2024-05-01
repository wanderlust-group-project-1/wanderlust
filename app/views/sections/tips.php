<div class="home_second">
    <div class="home_container">
        <div class="home_card">
            <div class="home_text">
                <div class="home_subtitle">Tips & knowhows</div>
                <div class="home_secondtitle">Outdoor Knowledge Hub!</div>
                <div class="home_sub">
                    <p>Empower your camping journey with expert tips, tutorials, and resources to help enthusiasts make the most of their adventures.</p>
                </div>
            </div>

        <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER'])) {
            $user = $_SESSION['USER']; ?>

            <div class="home_button">
                <a href="#" class="btn">Browse Tips</a>
            </div>

        <?php } else {  ?>
            <div class="home_button">
            <a href="<?= ROOT_DIR ?>/login" class="btn">Browse Tips</a>
            </div>
        <?php } ?>
    </div>

        <div class="home_card">
            <img class="home_img" src="<?= ROOT_DIR ?>/assets/images/Tips.png" alt="Hero Image">
        </div>
    </div>
</div>