
<div id="order-list-content" class=" col-lg-12">


<!-- Filter Order -->

<!-- Filter by Date Duration -->
<div class="filter-order gap-3 m-3">
    <div class="filter-order-header">
    </div>
    <div class="filter-order-body flex-d gap-3">
        <div class="filter-order-date">
            <label for="start-date">Start Date</label>
            <input type="date" id="start-date" name="start-date">
        </div>
        <div class="filter-order-date">
            <label for="end-date">End Date</label>
            <input type="date" id="end-date" name="end-date">
        </div>
        <!-- <div class="filter-order-cost">
            <label for="start-cost">Start Cost</label>
            <input type="number" id="start-cost" name="start-cost">

            <label for="end-cost">End Cost</label>
            <input type="number" id="end-cost" name="end-cost">
            
        </div> -->

        <div class="filter-order-button">
            <button class="btn-text-green border" id="filter-order-button"><i class="fa fa-filter" aria-hidden="true"></i>Filter</button>
        </div>

        <!-- by Cost -->
        
    </div>
</div>

<script>

// Jquery, only client side











</script>



    
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
  ($orders) ?  :print('<div class="col-lg-12 mih-100px flex-d justify-content-center
  align-items-center text-center"><h2>No Orders</h2></div>');

    

    foreach ($orders as $order) {
        ?>
        
        <div class = "row flex-d col-lg-12 order-card-item " id="order-card">
        <div class="order card  card-normal3 col-lg-12 flex-md-c miw-200px" data-id="<?= $order->id ?>">
            <div class="order-header">
                <div class="order-id">Order ID: <?= $order->id ?></div>
                <div class="order-status">Status: <?= $order->status ?></div>
            </div>
            <div class="order-body">
                <div class="order-dates" data-start="<?= $order->start_date ?>" data-end="<?= $order->end_date ?>" >Dates: <?= $order->start_date ?> - <?= $order->end_date ?></div>
                <!-- <div class="order-total">Total: <?= $order->total ?></div> -->
                                <div class="order-total">Paid Amount: <?= $order->paid_amount ?></div>

                <div class="order-payment-status">Payment Status: <?= $order->payment_status ?></div>
            </div>
            <div class="order-actions flex-d gap-3">
                <button class="btn-text-green" id="view-button"><i class="fa fa-list" aria-hidden="true"></i> View</button>
                <!-- if status pending set show  -->
                <?php if ($order->status == 'accepted') {
                    if ($order->rentalservice_req == 'rented') {

                        ?>

<div class="flex-d-c">
                    <button class="btn btn-primary flex-d btn-danger" id="mark-as-rented" disabled>
                    Requested
                    

                </button>
                    <button class="btn-text-red" id="cancel-rented" >Cancel</button>
                    </div>


<?php


                    }else {

                    
                        
                    
                    ?>
                    <div class="flex-d-c">
                    <button class="btn-text-green " id="mark-as-rented"><i class="fa fa-check" aria-hidden="true"></i>Mark as Rented
                    

                </button>
                    <button class="btn-text-red" id="cancel-rented" hidden>Cancel</button>
                    </div>
                <?php } }
                elseif ($order->status == 'rented') { ?>
                    <button class="btn-text-orange" id="mark-as-returned"><i class="fa fa-check" aria-hidden="true"></i>Mark as Returned</button>
                <?php }
                elseif ($order->status == 'pending') { ?>
                    <button class="btn-text-orange" id="accept-request"><i class="fa fa-check" aria-hidden="true"></i> Accept</button>
                    <button class="btn-text-red" id="cancel-request"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>  
                <?php }
                ?>

  
            </div>
        </div>
        </div>
        <?php
    }



?>


</div>

<!-- Mark as Returned Modal -->

<div id="mark-as-returned-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="flex-d-c justify-content-center text-center gap-3">
        <h2>Mark as Returned</h2>
        <p>Are you sure you want to mark this order as returned?</p>
        <div class="flex-d gap-3 mt-3">
            <button class="btn-text-green border" id="mark-as-returned-confirm">Yes</button>
            <button class="btn-text-orange border modal-close" id="mark-as-returned-cancel">No</button>
            <!-- Report Complaint -->

            <button class="btn-text-red border" id="report-return-complaint">Report Complaint</button>

            



        </div>
        </div>
    </div>
</div>


<!-- View modal -->

<div class="modal" id="order-item-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        
        <div id="order-data">   </div>
    </div>
</div>



<!-- Complaint Modals -->

<div id="report-complaint-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div id="complaint-form-data">

        </div>
    </div>
</div>

<style>
 
</style>