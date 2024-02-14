<div class="card"  data-id="1">
                <div class="row gap-2">

                <div class="col-lg-4 col-md-12">
                    <h3>Order ID: 1</h3>
                    <h3>Order Date: 2021-08-01</h3>
                 
                </div>

                <div class="col-lg-4 col-md-12">
                    <p> Tent, BBQ Grill, Table, Chair, Cooler</p>
                    
                </div>

                <div class="col-lg-3 col-md-12">

                <!-- view button -->
                <a class="btn btn-primary">View</a>
                    
                    </div>

                </div>
                            
                        </div>

<?php

foreach ($orders as $order) {
    ?>
   <div class="card"  data-id="'.$order->id.'">
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
                <a class="btn btn-primary">View</a>
                    
                    </div>

                </div>
                            
                        </div>

 <?php
}


?>