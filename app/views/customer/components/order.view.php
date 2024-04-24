

<!-- stdClass Object
(
    [id] => 26
    [customer_id] => 32
    [rentalservice_id] => 25
    [start_date] => 2024-02-01
    [end_date] => 2025-02-19
    [status] => pending
    [sub_status] => 
    [total] => 1206.00
    [paid_amount] => 0.00
    [update_at] => 2024-02-23 15:01:21
    [created_at] => 2024-02-25 06:22:50
    [customer_name] => Customer 
    [customer_email] => customer@wl.com
    [customer_number] => +94716024499
    [rental_service_name] => ABC Rent
    [rental_service_id] => 25
    [equipment_list] => BackPack - 80L (1), Baker Mueller (1)
) -->

<!-- <?php
show ($complaints);
?> -->

<div class="order-item">
       
    <div class="row card-normal m-2">
        <div class="col-lg-5 col-md-12">
            
         
            <table class="order-details">
                <tr >
                    <td>Order ID:</td>
                    <td><?= $order->id ?></td>
                </tr>
                <tr>
                    <td>Order Date:</td>
                    <td><?= date('Y-m-d', strtotime($order->created_at)) ?></td>
                </tr>
                <tr>
                    <td>Order Status:</td>
                    <td><?= $order->status ?></td>
                </tr>
            </table>


        </div>
        <div class="col-lg-6 col-md-12">
            <!-- <h3>Customer: <span id="customer-name"><?= $order->customer_name ?></span></h3>
            <h3>Email: <span id="customer-email"><?= $order->customer_email ?></span></h3> -->
       

            <table class="order-details">
                <tr >
                    <td>Rental Service:</td>
                    <td><?= $order->rental_service_name ?></td>
                </tr>
                <tr>
                    <td>Start Date:</td>
                    <td><?= date('Y-m-d', strtotime($order->start_date)) ?></td>
                </tr>
                <tr>
                    <td>End Date:</td>
                    <td><?= date('Y-m-d', strtotime($order->end_date)) ?></td>
                </tr>
            </table>

        </div>
            
    </div>

<!-- complaint list -->
<?php if(!empty($complaints)){?>


    <div class="row card-normal m-2">
    <div class="col-lg-12 col-md-12">
        <h3>Complaints List</h3>
        <table class="item-details">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint) : ?>
                <tr>
                    <td><?= $complaint->title ?></td>
                    <td><?= $complaint->description ?></td>
                    <td><?= $complaint->status ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    </div>
    <?php } ?>


  <!-- item list -->
    
    <div class="row card-normal m-2">
        <div class="col-lg-12 col-md-12">
            <h3>Equipment List</h3>
            <table class="item-details">
                <tr>
                    <th>Item_number </th>
                    <th>Name</th>
                </tr>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><?= $item->item_number ?></td>
                        <td><?= $item->equipment_name?></td>
                    </tr>
                <?php endforeach; ?>
            </table>             
        </div>
    </div>




</div>
        


</div>