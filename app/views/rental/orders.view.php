<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/navbar/rental-navbar.php');

?>

<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>


    <div class="guide-dash-main">
        <h1 class="title mb-2">Orders</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Orders</a></li>
        </ul>
        <div class="info-data mt-5">
            <div class="card">
                <div class="head">
                    <div>
                        <h2>100</h2>
                        <p>Rents</p>
                    </div>
                </div>
                <span class="progress" data-value="10%"></span>
                <span class="label">10 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>Rs.139000</h2>
                        <p>Total Earning</p>
                    </div>
                </div>
                <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span>
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h2>35</h2>
                        <p>Equipment Count</p>
                    </div>
                </div>
                <!-- <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span> -->
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


        <div class="dashboard-card">

            <div class="equipment p-4">

                <div class="row justify-content-between">
                    <h1 class="title">Orders</h1>

                    <!-- Section Switch  Upcoming lented Completed -->

                    <div class="section-switch flex-d gap-3" >
                        <button class="btn btn-primary active" id="all">Today</button>
                        <button class="btn btn-primary " id="upcoming">Upcoming</button>

                        
                        
                        
                        <button class="btn btn-primary" id="rented">Rented</button>
                        <button class="btn btn-primary" id="completed">Completed</button>
                        <button class="btn btn-primary" id="cancelled">Cancelled</button>
                        <button class="btn btn-primary" id="all">All</button>

                        <!-- not rented yet -->
                        <button class="btn btn-primary" id="not-rented">Not Rented</button>

                    </div>


                    

                   
                </div>


                <div class="order-list flex-d row" id="order-list">
                   





                </div>

            </div>

        </div>
    </div>


</div>


<script>

    // Get Orders
    function getOrders(status) {
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/orders/list/' + status,
            type: 'GET',
            success: function(response) {
                // if order-list-content in document remove it
                if ($('#order-list-content').length) {
                    $('#order-list-content').remove();
                }
                $('#order-list').html(response);
            }
        });
    }

    $(document).ready(function() {
        getOrders('all');

        $('.section-switch button').click(function() {
            $('.section-switch button').removeClass('active');
            $(this).addClass('active');
            getOrders($(this).attr('id'));
        });
    });


</script>