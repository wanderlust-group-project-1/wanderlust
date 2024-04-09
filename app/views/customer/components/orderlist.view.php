

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
                    <p> <?php echo $order->equipment_names; ?></p>
                    
                </div>

                <div class="col-lg-3 col-md-12">

                <!-- view button -->
                <a class="btn btn-primary order-view-button">View</a>
                    
                    </div>

                </div>
                            
                        </div>

 <?php
}


?>

<div class="modal" id="order-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div id="order-data">   </div>

    </div>