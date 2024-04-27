<div id="order-item-content">

<?php
    //  [complaint] => stdClass Object
    //  (
    //      [complaint_no] => CC000003
    //      [rent_id] => 65
    //      [rent_date] => 2024-02-25 10:24:39
    //      [customer_id] => 32
    //      [paid_amount] => 0.00
    //      [paid_date] => 2024-02-25 10:24:39
    //      [start_date] => 2024-02-25
    //      [end_date] => 2024-02-28
    //      [rent_status] => accepted
    //      [total_amount] => 1400.00
    //      [rental_id] => 56
    //      [rental_name] => nirmal
    //      [rental_mobile] => 0713458323
    //      [title] => kjbaekr
    //      [description] => lkjbk dflnfdl kjbdf
    //      [created_at] => 2024-04-23 09:54:10
    //      [status] => pending
    //  )



    ?>

    <div class="order-item">

    <div class="row card-grid m-2">
        <!-- Complain details -->

        <div class="col-lg-5 col-md-12">
            <table class="order-details">
                <tr>
                    <td>Complain No:</td>
                    <td><?= $complaint->complaint_no ?></td>
                </tr>
                <tr>
                    <td>Complaint Date:</td>
                    <td><?= date('Y-m-d', strtotime($complaint->created_at)) ?></td>
                </tr>
                <tr>
                    <td class="status-view">Complaint Status:</td>
                    <td class="status-view"><?= $complaint->status ?></td>
                </tr>
            </table>
            
        </div>
        <div class="col-lg-5 col-md-12">
            <table class="order-details">
                <tr>
                    <td>Total:</td>
                    <td><?= $complaint->total_amount ?></td>
                </tr>
                <tr>
                    <td>Paid:</td>
                    <td><?= $complaint->paid_amount ?></td>
                </tr>
                <tr>
                    <td>Payment Date:</td>
                    <td><?= date('Y-m-d', strtotime($complaint->created_at)) ?></td>
                </tr>
            </table>
            
        </div>

    </div>
       
    <div class="row card-grid m-2">
        <div class="col-lg-5 col-md-12">

            <table class="order-details">
                <tr >
                    <td>Order ID:</td>
                    <td><?= $complaint->rent_id ?></td>
                </tr>
                <tr>
                    <td>Order Date:</td>
                    <td><?= date('Y-m-d', strtotime($complaint->rent_date)) ?></td>
                </tr>
                <tr>
                    <td class="status-view">Order Status:</td>
                    <td class="status-view"><?= $complaint->rent_status ?></td>
                </tr>
            </table>


        </div>
        <div class="col-lg-6 col-md-12 mr-0">
            <!-- <h3>Customer: <span id="customer-name"><?= $order->customer_name ?></span></h3>
            <h3>Email: <span id="customer-email"><?= $order->customer_email ?></span></h3> -->
            <table class="order-details">
                <tr> 
                    <td>Rental ID:</td>
                    <td><?= $complaint->rental_id ?></td>
                </tr>
                <tr>
                <td>Rental name:</td>
                    <td><?= $complaint->rental_name ?></td>
                </tr>
                <tr>
                    <td>Rental Mobile:</td>
                    <td><?= $complaint->rental_mobile ?></td>
                </tr>
            </table>

        </div>
            
    </div>

    <div class="row card-grid-last m-2">

    <!-- item list -->

    <div class="col-lg-12 col-md-12">
        <!-- [equipment_id] => 53
        [equipment_name] => BBQ Grill
        [item_number] => I000539029
        [equipment_cost] => 5600.00 -->

        <h3>Equipment List</h3>
        <div class="table-container">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Item_number</th>
                        <th>Item Name</th>
                        <!-- <th>Complaint</th> -->
                        <th>Equipment cost</th>
                    </tr>
                </thead>
                <?php foreach ($rentitems as $rentitem) : ?>
                    <tbody>
                        <tr>
                            <td><?= $rentitem->item_number ?></td>
                            <td><?= $rentitem->equipment_name?></td>
                            <!-- <td><?= $rentitem->complaint ? $rentitem->complaint->complaint_description : 'No Complain' ?></td> -->
                            <td><?= $rentitem->equipment_cost ?></td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>

    </div>
   
        



        

    </div>




    </div>
        


</div>