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




        <div class="dashboard-card">

            <div class="equipment p-4">

                <div class="row justify-content-between gap-3">
                    <h1 class="title">Orders</h1>

                    <!-- Section Switch  Upcoming lented Completed -->

                    <div class="section-switch flex-d  gap-3 flex-wrap" >
                        <button class="btn btn-primary active" id="today">Today</button>
                        <button class="btn btn-primary " id="upcoming">Upcoming</button>
                        <button class="btn btn-primary" id="rented">Rented</button>
                        <button class="btn btn-primary" id="completed">Completed</button>
                        <button class="btn btn-primary" id="cancelled">Cancelled</button>
                        
                        <button class="btn btn-primary" id="all">All</button>

                        <!-- not rented yet -->
                        <button class="btn btn-primary" id="not-rented">Not Rented</button>

                    </div>

                  
                    


                    

                   
                </div>


                <div class="order-list  row" id="order-list">
                   





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