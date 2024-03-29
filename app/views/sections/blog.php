<div class="home_second">
    <div class="home_container">
        <div class="home_card">
            <div class="home_text">
                <div class="home_subtitle">Blogs</div>
                <div class="home_secondtitle">Explore: Add Your Adventure!</div>
                <div class="home_sub">
                    <p>Dive into our blog section for insightful tips, adventure stories, and expert advice to enhance your camping experience. Add your own blog filled with outdoor experiences.</p>
                </div>
            </div>

        <?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER'])) {
            $user = $_SESSION['USER']; ?>

            <div class="home_button">
                <a href="#" class="btn">Read Blogs</a>
            </div>

        <?php } else {  ?>
            <div class="home_button">
            <a href="<?= ROOT_DIR ?>/login" class="btn">Read Blogs</a>
            </div>
        <?php } ?>
    </div>

        <div class="home_card">
            <img class="home_img" src="<?= ROOT_DIR ?>/assets/images/Blog.png" alt="Hero Image">
        </div>
    </div>
</div>