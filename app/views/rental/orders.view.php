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
                        <button class="btn btn-primary " id="pending">Pending</button>
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


    // Mark as Rented

    $(document).on('click', '#mark-as-rented', function() {
        var orderId = $(this).closest('.order').attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/orders/markAsRentedByRentalservice/' + orderId,
            type: 'GET',
            success: function(response) {
               var id = response.data.order_id;
               console.log(id);

            //  change button to cancel
            var button = $(`[data-id=${id}]`).find('#mark-as-rented');
            
            button.text('Requested');
            



            // add btn-danger class
            button.removeClass('btn-primary');
            button.addClass('btn-danger');
            // disable button
            button.prop('disabled', true);

            // show cancel button
            $(`[data-id=${id}]`).find('#cancel-rented').show();
            
            


            }
        });
    });

    $(document).on('click', '#cancel-rented', function() {
        var orderId = $(this).closest('.order').attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/orders/cancelRentedByRentalservice/' + orderId,
            type: 'GET',
            success: function(response) {
               var id = response.data.order_id;
               console.log(id);

            //  change button to cancel
            var button = $(`[data-id=${id}]`).find('#mark-as-rented');

            button.text('Mark as Rented');
            button.prop('disabled', false);
            // add btn-danger class

            button.removeClass('btn-danger');
            button.addClass('btn-primary');
            // hide cancel button
            $(`[data-id=${id}]`).find('#cancel-rented').hide();


            }
        });
    });


    // Open Mark as Returned Modal 
    $(document).on('click', '#mark-as-returned', function() {
        var orderId = $(this).closest('.order').attr('data-id');
        $('#mark-as-returned-modal').attr('data-id', orderId);
        $('#mark-as-returned-modal').show();
    });

    // Mark as Returned Confirm

    $(document).on('click', '#mark-as-returned-confirm', function() {
        var orderId = $('#mark-as-returned-modal').attr('data-id');
        console.log(orderId);
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/orders/markAsReturnedByRentalservice/' + orderId,
            type: 'GET',
            success: function(response) {
                console.log(response);
                var id = response.data.order_id;
                // console.log(id);
                // hide modal
                $('#mark-as-returned-modal').hide();
                // change status
                $(`[data-id=${id}]`).find('.order-status').text('Status: returned');
                // hide mark as returned button
                $(`[data-id=${id}]`).find('#mark-as-returned').hide();
            },
            error: function(err) {
                console.log(err);
            }
        });
    });



    // Pending Orders Accept and Cancel

    $(document).on('click', '#accept-request', function() {
        var orderId = $(this).closest('.order').attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/orders/acceptRequestByRentalservice/' + orderId,
            type: 'GET',
            success: function(response) {
                console.log(response.data);
                var id = response.data.order_id;
                // console.log(id);
                // change status
                $(`[data-id=${id}]`).find('.order-status').text('Status: accepted');
                // hide accept and cancel buttons
                $(`[data-id=${id}]`).find('#accept-request').hide();
                $(`[data-id=${id}]`).find('#cancel-request').hide();
                // show mark as rented button
                
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    $(document).on('click', '#cancel-request', function() {
        var orderId = $(this).closest('.order').attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/orders/cancelRequestByRentalservice/' + orderId,
            type: 'GET',
            success: function(response) {
                console.log(response);
                var id = response.data.order_id;
                // console.log(id);
                // change status
                $(`[data-id=${id}]`).find('.order-status').text('Status: cancelled');
                // hide accept and cancel buttons
                $(`[data-id=${id}]`).find('#accept-request').hide();
                $(`[data-id=${id}]`).find('#cancel-request').hide();
                // show mark as rented button
                
            },
            error: function(err) {
                console.log(err);
            }
        });
    });




</script>


<?php
require_once('../app/views/layout/footer.php');
?>