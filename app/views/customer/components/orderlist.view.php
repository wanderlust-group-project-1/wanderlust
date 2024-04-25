

<?php





foreach ($orders as $order) {
    ?>
   <div class="card card-normal3"  data-id="<?php echo $order->id; ?>">
        <div class="row gap-2 order-list">

                <div class="col-lg-4 col-md-12">
                    <div class="order-header">
                        <div class="order-id">Order ID: <?php echo $order->id; ?></div>
                        <div class="order-date">Start Date: <?php echo $order->start; ?></div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-12">
                    <div class="text-overflow-ellipsis "> <?php echo $order->equipment_names; ?></div>
                    
                </div>

                <div class="col-lg-4 col-md-12 order-button-container">

                <!-- view button -->
                <!-- <a class="btn btn-primary order-view-button">View</a> -->
                <button class="btn-text-green order-view-button" id="view-button"><i class="fa fa-list" aria-hidden="true"></i> View</button>

                
                <?php if ($order->payment_status == 'pending') { ?>
                    <button class="btn-text-blue order-pay-button"><i class="fa fa-credit-card" aria-hidden="true"></i> Pay</button>
                <?php } ?>

                <?php if ($order->rent_status == 'rented') { ?>
                    <button class="btn-text-blue order-fullpay-button"><i class="fa fa-credit-card" aria-hidden="true"></i> Pay</button>
                <?php } ?>

                <!-- if rent_status accepted, mark as rented --> 
                <?php if ($order->rent_status == 'accepted') { ?>
                    <button class="btn-text-green order-rent-button"><i class="fa fa-check-square" aria-hidden="true"></i> Mark as Rented</button>
                    <!-- Report button -->
                    <button class="btn-text-orange order-report-button"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Complain</button>
                    

                    

                
                   
                <?php } ?>

                <!-- if rent_status pending or accepted -->
                <?php if ($order->rent_status == 'pending' || $order->rent_status == 'accepted') { ?>
                    <!-- cancel -->
                    <button class="btn-text-red order-cancel-button"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>
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


<!-- Report Modal -->
<!-- Complain Form with title and description -->

<div class="modal" id="report-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="report-order-id">Report for Order ID: </h2>
        <form id="report-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="report-title" name="title" class="form-control-lg" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="report-description" name="description" class="form-control-lg" required></textarea>
            </div>
            <button class="btn btn-primary" id="report-submit">Submit</button>
        </form>
    </div>
</div>


<!-- pay modal  -->

<div class="modal" id="pay-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="pay-data">
        </div>
        </div>
    </div>

</div>