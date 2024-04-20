<div id="order-item-content">

<?php



    ?>

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

    <!-- Button to download report -->
    <div class="row m-2">
        <div class="col-lg-12 col-md-12 m-4">
            <button id="download-report" class="btn btn-primary" data-id="<?= $order->id ?>" >Download Report</button>
        </div>
    </div>




    </div>
        


</div>