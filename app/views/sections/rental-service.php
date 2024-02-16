<div class="home_second">
    <div class="home_container">
        <div class="home_card">
            <div class="home_text">
                <div class="home_subtitle">Rental Services</div>
                <div class="home_secondtitle">Diverse Gear Selection!</div>
                <div class="home_sub">
                    <p>Explore a diverse range of camping equipment in<br>one place, making your outdoor adventures<br>simpler and more convenient.</p>
                </div>
            </div>

        <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER'])) {
            $user = $_SESSION['USER']; ?>

            <div class="home_button">
                <a href="#" class="btn">Book Now</a>
            </div>

        <?php } else {  ?>
            <div class="home_button">
            <a href="<?= ROOT_DIR ?>/login" class="btn">Book Now</a>
            </div>
        <?php } ?>
    </div>

        <div class="home_card">
            <img class="home_img" src="<?= ROOT_DIR ?>/assets/images/Rental.png" alt="Hero Image">
        </div>
    </div>
</div>