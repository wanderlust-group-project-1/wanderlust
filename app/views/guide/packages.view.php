<?php
require_once('../app/views/layout/header.php');
?>

<!-- search bar -->

<!-- <div class="package-card flex-d-r">
    <div class="pack-img">

    </div>
    <div class="pack-set flex-d-r">
        <div>
            <h1 class="title"> Platinum <h1>
        </div>
        <div class="pack-benefits .flex-d-r">
            <div>
                <p></p> 
            </div>
            <div>
                <p></p> 
            </div>
        </div>
        <div class="pack-benefits">
            <div>
                <p></p> 
            </div>
            <div>
                <p></p> 
            </div>
        </div>
    </div>
</div> -->


<div class="dashboard">
    <?php require_once('../app/views/guide/layout/guide-sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">My Packages</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Packages</a></li>
        </ul>
        <div class="info-data mt-5">
            <div class="card">
                <div class="head">
                    <div>
                        <h2>96</h2>
                        <p>No of Tours</p>
                    </div>
                </div>
                <span class="progress" data-value="12.5%"></span>
                <span class="label">12 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>Rs.139000</h2>
                        <p>Income</p>
                    </div>
                </div>
                <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>1st March</h2>
                        <p>Upcoming Booking</p>
                    </div>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>Micheal Julius</p>
                </div>
                <!-- <span class="progress" data-value="30%"></span>
                <span class="label">30%</span> -->
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>21st February</h2>
                        <p>Recent Booking</p>
                    </div>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2 ml-2">
                    <p>Julius John</p>
                </div>
                <!-- <span class="progress" data-value="80%"></span>
                <span class="label">80%</span> -->
            </div>
        </div>
    </div>
</div>