

<?php





foreach ($orders as $order) {
    ?>
   <div class="card"  data-id="<?php echo $order->id; ?>">
                <div class="row gap-2">

                <div class="col-lg-4 col-md-12">
                    <h3>Order ID: <?php echo $order->id; ?></h3>
                    <h3>Start Date: <?php echo $order->start; ?></h3>
                 
                </div>

                <div class="col-lg-4 col-md-12">
                    <p class="text-overflow-ellipsis "> <?php echo $order->equipment_names; ?></p>
                    
                </div>

                <div class="col-lg-3 col-md-12">

                <!-- view button -->
                <!-- <a class="btn btn-primary order-view-button">View</a> -->
                <button class="btn-text-green order-view-button" id="view-button"><i class="fa fa-list" aria-hidden="true"></i> View</button>

                
                <?php if ($order->payment_status == 'pending') { ?>
                    <button class="btn btn-primary order-pay-button">Pay</button>
                <?php } ?>

<!-- if rent_status pending or accepted -->
                <?php if ($order->rent_status == 'pending' || $order->rent_status == 'accepted') { ?>
                    <!-- cancel -->
                    <button class="btn-text-red order-cancel-button"><i class="fa fa-times" aria-hidden="true"></i>Cancel</button>
                <?php } ?>

                <!-- if rent_status accepted, mark as rented --> 
                <?php if ($order->rent_status == 'accepted') { ?>
                    <button class="btn btn-primary order-rent-button">Mark as Rented</button>
                <?php } ?>


                    </div>

                </div>
                            
                        </div>

 <?php
}


?>
<!-- Order Item Modal -->
<div class="modal" id="order-item-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div id="order-data">   </div>

    </div>
</div>


<!-- Confirm cancel modal -->

<div class="modal" id="confirm-cancel-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to cancel this order?</p>
        <div class="flex-d gap-3 mt-3">
        <button class="btn btn-primary" id="confirm-cancel">Yes</button>
        <button class="btn btn-danger modal-close" id="cancel-cancel">No</button>
        </div>
    </div>
</div>


<!-- Mark As Rented modal -->

<div class="modal" id="mark-as-rented-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Mark as Rented</h2>
        <p>Are you sure you want to mark this order as rented?</p>
        <div class="flex-d gap-3 mt-3">
        <button class="btn btn-primary" id="mark-as-rented-confirm">Yes</button>
        <button class="btn-text-red" id="mark-as-rented-cancel">No</button>
        </div>
    </div>
</div>

