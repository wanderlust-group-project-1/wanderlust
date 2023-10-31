<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="EditEquipment.css" rel="stylesheet">

</head>

<body>
    <div class="edit-Equ">
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
                <img src="<?php echo ROOT_DIR?>/assets/images/5.png" alt="">
            </div>
            <div class="profText">Glaze Camping</div>
        </div>

        <div class="frame5">
            <div class="shopFrame2">
                <div class="shop-card">
                    <div class="img-1">
                        <img src="<?php echo ROOT_DIR?>/assets/images/3.png" alt="">
                    </div>
                    <div class="cardName">Torch</div>
                    <div class="priceText">Rs. 300</div>
                </div>
            </div>

            <div class="shopDetais">

                <!-- <img src="<?php echo ROOT_DIR?>/assets/images/4.png" alt=""> -->

                <div class="shopForm">
                    <div class="detailsForm-wrapper">
                        <input type="text" id="detailsForm" name="name" placeholder="Brand :">
                        <!-- <div class="detailsForm">Brand : Orange</div> -->
                    </div>
                    <div class="detailsForm-wrapper">
                        <input type="text" id="detailsForm" name="name" placeholder="Company :">
                        <!-- <div class="detailsForm">Brand : Orange</div> -->
                    </div>
                    <div class="detailsForm-wrapper">
                        <input type="text" id="detailsForm" name="name" placeholder="Size :">
                        <!-- <div class="detailsForm">Brand : Orange</div> -->
                    </div>
                    <div class="detailsForm-wrapper">
                        <input type="text" id="detailsForm" name="name" placeholder="Colors :">
                        <!-- <div class="detailsForm">Brand : Orange</div> -->
                    </div>
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