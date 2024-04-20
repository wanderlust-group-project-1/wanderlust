<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/admin/components/navbar.php');
// require_once('../app/views/navbar/rental-navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Dashboard</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Dashboard</a></li>
        </ul>
        <div class="info-data mt-5">
            <div class="card">
                <div class="head">
                    <div>


  


                        <!-- <h3>100</h3> -->
                        <h3><?php echo $stat->successful_rental_count ?></h3>
                        <p>Rents</p>
                    </div>
                </div>
                <span class="progress" data-value="10%"></span>
                <span class="label">
                    <?php echo $stat->last_month_rental_count ?> 
                    : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <!-- <h3>Rs.139000</h3> -->
                        <h3>Rs.<?php echo $stat->total_earnings ?></h3>
                        <p>Total Earning</p>
                    </div>
                </div>
                <span class="progress" data-value="60%"></span>
                <!-- <span class="label">Rs.60 000 : Per Month</span> -->
                <span class="label">Rs.<?php echo $stat->current_month_earnings ?> : Per Month</span>
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <!-- <h3>35</h3> -->
                        <h3><?php echo $stat->equipment_count ?></h3>
                        <p>Equipment Quantity</p>
                    </div>
                </div>
                <!-- <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span> -->
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h3>1st March</h3>
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
                        <h3>21st February</h3>
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
         

                <!-- Add Equipment -->


                <!-- <div class="equipment-list">



                </div> -->

            </div>

        </div>
    </div>


</div>

