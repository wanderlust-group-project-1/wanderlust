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




        <div class="dashboard-card mt-5">

            <div class="equipment p-4">

                <div class="row justify-content-between gap-3">
                    <h1 class="title">Orders</h1>

                    <!-- Section Switch  Upcoming lented Completed -->

                    <div class="section-switch flex-d  gap-3 flex-wrap" >
                        <button class="btn-selected" id="pending">Pending</button>
                        <button class="btn-selected" id="upcoming">Upcoming</button>
                        <button class="btn-selected" id="rented">Rented</button>
                        <button class="btn-selected" id="completed">Completed</button>
                        <button class="btn-selected" id="cancelled">Cancelled</button>
                        
                        <button class="btn-selected" id="all">All</button>

                        <!-- not rented yet -->
                        <button class="btn-selected" id="not-rented">Not Rented</button>

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
        getOrders('pending');

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



    // Order View Modal

    $(document).on('click', '#view-button', function() {
        var orderId = $(this).closest('.order').attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/orders/viewOrder/' + orderId,
            type: 'GET',
            success: function(response) {
                $('#order-item-modal').show();
                $('#order-data').html(response);

                // setTimeout(() => {
                //     loadCalender();
                // }, 1000);
            },
            error: function(err) {
                console.log(err);
            }
        });
    });


    // Order report download

    $(document).on('click', '#download-report', function() {
        var orderId = $(this).attr('data-id');
        window.open('<?= ROOT_DIR ?>/report/orderReportByRental/' + orderId, '_blank');
    });






    //  Returned Report Complaint

    $(document).on('click', '#report-return-complaint', function() {
        var orderId = $('#mark-as-returned-modal').attr('data-id');
        console.log(orderId);
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/orders/reportReturnComplaintByRentalservice/' + orderId,
            type: 'GET',
            success: function(response) {

                $('#complaint-form-data').attr('data-id', orderId);

                $('#report-complaint-modal').show();
                $('#complaint-form-data').html(response);





              
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    $(document).on('click', '#report-complaint-submit', function() {
        // var orderId = $('#complaint-form-data').attr('data-id');
        var orderId = $('#report-complaint-table').attr('data-order-id');
        // var data = {
        //     order_id: orderId,
        //     complaints: [],
        //     complaint_descriptions: [],
        //     charges: []
        // };

        var data = {
            order_id: orderId,
            complaints: [],
            charge: 0.00
            }

        $('#complaint-form-data .report-item-checkbox').each(function() {
            if ($(this).is(':checked')) {
                var id = $(this).closest('tr').attr('data-id');
                var complaintDescription = $(this).closest('tr').find('textarea').val();
                var charge = $(this).closest('tr').find('input[type="number"]').val();

                // data.complaints.push(id);
                // data.complaint_descriptions.push(complaintDescription);
                // data.charges.push(charge);
                let complaint = {
                    equipment_id: id,
                    complaint_description: complaintDescription,
                    charge: charge
                }

                data.complaints.push(complaint);
                data.charge += parseFloat(charge);
         



            }
        });

        console.log(data);

        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/orders/submitReturnComplaint',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                console.log(response);
                $('#report-complaint-modal').hide();
            },
            error: function(err) {
                console.log(err);
            }
        });
    });








    // jQuery for filter order

// Filter order card
// $('#filter-order-button').click(function() {

    $(document).on('click', '#filter-order-button', function() {
    let start_date = $('#start-date').val();
    let end_date = $('#end-date').val();
    

    console.log(start_date, end_date, status);

    // Get Orders
    
    // from all order-cards
    let orderCards = $('.order-card-item');

    // loop through all order-cards
    orderCards.each(function() {
        let orderCard = $(this);
        // let orderStartDate = orderCard.find('.order-body .order-dates').text().split(' ')[1];
        // let orderEndDate = orderCard.find('.order-body .order-dates').text().split(' ')[3];
        let orderStartDate = orderCard.find('.order-body .order-dates').attr('data-start');
        let orderEndDate = orderCard.find('.order-body .order-dates').attr('data-end');
        // convert date to timestamp
        // let orderStartDate = new Date(orderCard.find('.order-body .order-dates').attr('data-start')).getTime();
        // let orderEndDate = new Date(orderCard.find('.order-body .order-dates').attr('data-end')).getTime();
        let orderStatus = orderCard.find('.order-header .order-status').text().split(' ')[1];

        // console.log(orderStartDate, orderEndDate, orderStatus);


        // if start date and end date is not empty
        if (start_date != '' && end_date != '') {

            console.log(orderStartDate, start_date, orderEndDate, end_date);
            // check if order start date is greater than or equal to start date and order end date is less than or equal to end date
            if (orderStartDate >= start_date && orderEndDate <= end_date) {
                // show order-card
                orderCard.show();
            } else {
                // hide order-card
                orderCard.hide();
            }
        }
    });

});

</script>


<?php
require_once('../app/views/layout/footer.php');
?>


