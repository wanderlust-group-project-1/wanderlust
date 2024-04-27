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

    <div class="row card-grid m-2">
        <!-- Complain details -->

        <div class="col-lg-5 col-md-12">
            <table class="order-details">
                <tr>
                    <td>Complain ID:</td>
                    <td><?= $complaint->id ?></td>
                </tr>
                <tr>
                    <td>Complain Date:</td>
                    <td><?= date('Y-m-d', strtotime($complaint->created_at)) ?></td>
                </tr>
                <tr>
                    <td class="status-view">Complaint Status:</td>
                    <td class="status-view"><?= $complaint->status ?></td>
                </tr>
            </table>
            
        </div>

    </div>
       
    <div class="row card-grid m-2">
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
                    <td class="status-view">Order Status:</td>
                    <td class="status-view"><?= $order->status ?></td>
                </tr>
            </table>


        </div>
        <div class="col-lg-6 col-md-12">
            <!-- <h3>Customer: <span id="customer-name"><?= $order->customer_name ?></span></h3>
            <h3>Email: <span id="customer-email"><?= $order->customer_email ?></span></h3> -->
            <table class="order-details">
                <tr> 
                    <td>Rental Service:</td>
                    <td><?= $order->rental_service_name ?></td>
                </tr>
                <tr>
                    <td>Rental Service ID:</td>
                    <td><?= $order->rental_service_id ?></td>
                </tr>
            </table>

        </div>
            
    </div>

    <div class="row card-grid-last m-2">

    <!-- item list -->

    <div class="col-lg-12 col-md-12">
        <h3>Equipment List</h3>
        <div class="table-container">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Item_number </th>
                        <th>Name</th>
                        <th>Complain</th>
                        <th>Charge</th>
                    </tr>
                </thead>
                <?php foreach ($items as $item) : ?>
                    <tbody>
                        <tr>
                            <td><?= $item->item_number ?></td>
                            <td><?= $item->equipment_name?></td>
                            <td><?= $item->complaint ? $item->complaint->complaint_description : 'No Complain' ?></td>
                            <td><?= $item->charge ?></td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>

    </div>
    </div>
 </div>
</div>