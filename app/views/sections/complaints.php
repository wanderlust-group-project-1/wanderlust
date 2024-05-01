<div class="home_second">
    <div class="home_container">
        <div class="home_card">
            <div class="home_text">
                <div class="home_subtitle">Complaints</div>
                <div class="home_secondtitle">Where Complaints Find Solutions!</div>
                <div class="home_sub">
                    <p>Tired of unreliable guides and missing gear? <br>Discover our camping equipment rental and guide hiring platform, where adventure meets satisfaction.</p>
                </div>
            </div>

        <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER'])) {
            $user = $_SESSION['USER']; ?>

            <div class="home_button">
                <a href="#" class="btn-text-green border">Add Complains</a>
            </div>

        <?php } else {  ?>
            <div class="home_button">
            <a href="<?= ROOT_DIR ?>/login" class="btn-text-green border">Add Complain</a>
            </div>
        <?php } ?>
    </div>

        <div class="home_card">
            <img class="home_img" src="<?= ROOT_DIR ?>/assets/images/Complaint.png" alt="Hero Image">
        </div>
    </div>
</div>