
<div id="order-list-content" class=" col-lg-12">
    
<?php

// show($orders);

// [0] => stdClass Object
// (
//     [id] => 13
//     [customer_id] => 32
//     [rentalservice_id] => 25
//     [start_date] => 2024-02-22
//     [end_date] => 2024-04-29
//     [status] => pending
//     [total] => 1206.00
//     [paid_amount] => 0.00
//     [update_at] => 2024-02-23 15:01:21
//     [payment_status] => completed
// )


//  oreders list 
  ($orders) ?  : show('No orders found');

    foreach ($orders as $order) {
        ?>
        
        <div class = "row flex-d col-lg-12">
        <div class="order card  card-normal col-lg-12 flex-md-c miw-200px" data-id="<?= $order->id ?>">
            <div class="order-header">
                <div class="order-id">Order ID: <?= $order->id ?></div>
                <div class="order-status">Status: <?= $order->status ?></div>
            </div>
            <div class="order-body">
                <div class="order-dates">Dates: <?= $order->start_date ?> - <?= $order->end_date ?></div>
                <div class="order-total">Total: <?= $order->total ?></div>
                <div class="order-payment-status">Payment Status: <?= $order->payment_status ?></div>
            </div>
            <div class="order-actions flex-d gap-3">
                <button class="btn btn-primary" id="view-button">View</button>
                <!-- if status pending set show  -->
                <?php if ($order->status == 'pending') { ?>
                    <div class="flex-d-c">
                    <button class="btn btn-primary flex-d" id="mark-as-rented">
                    Mark as Rented
                    

                </button>
                    <button class="btn btn-danger" id="cancel-request" hidden>Cancel</button>
                    </div>
                <?php } ?>

  
            </div>
        </div>
        </div>
        <?php
    }



?>


</div>

<style>
 
</style>