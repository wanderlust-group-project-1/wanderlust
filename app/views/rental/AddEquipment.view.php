<link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/AddEquipment.css">

<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/components/navbar.php');
?>
<?php

echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
?>

<div class="add-Equ">
    <div class="frame4">
        <div class="frameShop">
            <div class="searchbar">
                <input type="text" id="SearchShops" name="name" placeholder="Search Items">
            </div>
        </div>

        <div class="div-3">

            <div class="frame-wrapper">
                <button class="search-button">Most rated</button>
            </div>

            <div class="frame-wrapper">
                <button class="search-button">Price low to high</button>
            </div>

            <div class="frame-wrapper">
                <button class="search-button">No of Saless</button>
            </div>

            <div class="frame-wrapper">
                <button class="search-button">Popular</button>
            </div>

            <button class="small-button">Search</button>

        </div>
    </div>

    <div class="frameProf">
        <div class="profImg">
            <img src="<?php echo ROOT_DIR ?>/assets/images/5.png" alt="">
        </div>
        <div class="profText">Glaze Camping</div>
    </div>

    <div class="frame5">
        <div class="shopFrame2">
            <div class="shop-card">
                <div class="img-1">
                    <img src="<?php echo ROOT_DIR ?>/assets/images/rectangle.png" alt="">
                </div>
                <!-- <div class="cardName">Torch</div>
                <div class="priceText">Rs. 300</div> -->
            </div>
        </div>

        <div class="shopDetais">

            <!-- <img src="<?php echo ROOT_DIR ?>/assets/images/4.png" alt=""> -->

            <form class="shopForm" method="POST" action="<?= ROOT_DIR ?>/wanderlust/public/Product/store">
                <?php if (isset($errors)) : ?>
                                <div> <?= implode('<br>', $errors) ?> </div>
                            <?php endif; ?>

                <div class="detailsForm-wrapper">
                    <input type="text" id="detailsForm" name="brand" placeholder="Brand :">
                    <!-- <div class="detailsForm">Brand : Orange</div> -->
                </div>
                <div class="detailsForm-wrapper">
                    <input type="text" id="detailsForm" name="company" placeholder="Company :">
                    <!-- <div class="detailsForm">Brand : Orange</div> -->
                </div>
                <div class="detailsForm-wrapper">
                    <input type="text" id="detailsForm" name="size" placeholder="Size :">
                    <!-- <div class="detailsForm">Brand : Orange</div> -->
                </div>
                <div class="detailsForm-wrapper">
                    <input type="text" id="detailsForm" name="colors" placeholder="Colors :">
                    <!-- <div class="detailsForm">Brand : Orange</div> -->
                </div>
                <div class="detailsForm-wrapper">
                    <input type="text" id="detailsForm" name="prize" placeholder="Prize :">
                    <!-- <div class="detailsForm">Brand : Orange</div> -->
                </div>
                <button type="submit" name="submit" class="search-button">Submit</button>
            </div>

        </div>

    </div>

    <div class="frame4">
        <div class="infoBox">
            <div class="detailsForm">Additional Information</div>
        </div>
    </div>

    <!-- <div class="frame4">
        <div class="infoBox">
                <input type="text" id="detailsForm" name="detailsForm" placeholder="Additional Information">
            < <div class="detailsForm">Additional Information</div></div> -->
</div>

</body>

</html>