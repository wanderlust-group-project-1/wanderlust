<div id="order-item-content">

<?php
    // show($order);
    // show($items);

    // [id] => 13
    // [customer_id] => 32
    // [rentalservice_id] => 25
    // [start_date] => 2024-02-22
    // [end_date] => 2024-04-29
    // [status] => pending
    // [sub_status] => 
    // [total] => 1206.00
    // [paid_amount] => 0.00
    // [update_at] => 2024-02-23 15:01:21
    // [created_at] => 2024-02-25 06:22:50
    // [customer_name] => Customer 
    // [customer_email] => customer@wl.com
    // [equipment_list] => BackPack - 80L (1), Baker Mueller (1)


    ?>

    <div class="order-item">
       
    <div class="row card-normal m-2">
        <div class="col-lg-5 col-md-12">
            
            <!-- <h3>Order ID: <span id="order-id"><?= $order->id ?></span></h3>
            <h3>Order Date: <span id="order-date"><?= $order->created_at ?></span></h3>
            <h3>Order Status: <span id="order-status"><?= $order->status ?></span></h3> -->

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
                <tr>
                    <td>Customer:</td>
                    <td><?= $order->customer_name ?></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><?= $order->customer_email ?></td>
                </tr>
                <tr>
                    <td>Mobile:</td>
                    <td><?= $order->customer_number ?></td>
            </table>

        </div>
            
    </div>

    <div class="row card-normal m-2">

    <!-- item list -->

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