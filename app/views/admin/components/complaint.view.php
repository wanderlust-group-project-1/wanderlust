<div id="order-item-content">

    <!-- <?php show($data) ?> -->

    <div class="order-item">

        <div class="row card-normal m-2">
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
                        <td>Complain Status:</td>
                        <td><?= $complaint->status ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-5 col-md-12">
                <table class="order-details">
                    <tr>
                        <td> Rental Serivece Name: </td>
                        <td><?= $order->rental_service_name ?> </td>
                    </tr>
                    <tr>
                        <td> Rental Serivece ID:</td>
                        <td><?= $order->rental_service_id ?> </td>
                    </tr>
                    <!-- <tr>
                        <td> Rental Serivece Email:</td>
                        <td><?= $order->rental_service_email ?> </td>
                    </tr> -->

                </table>
            </div>

        </div>

        <div class="row card-normal m-2">
            <div class="col-lg-5 col-md-12">

                <!-- <h3>Order ID: <span id="order-id"><?= $order->id ?></span></h3>
            <h3>Order Date: <span id="order-date"><?= $order->created_at ?></span></h3>
            <h3>Order Status: <span id="order-status"><?= $order->status ?></span></h3> -->

                <table class="order-details">
                    <tr>
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
                        <th>Complain</th>
                        <th>Charge</th>
                    </tr>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><?= $item->item_number ?></td>
                            <td><?= $item->equipment_name ?></td>
                            <td><?= $item->complaint ? $item->complaint->complaint_description : 'No Complain' ?></td>
                            <td><?= $item->charge ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            </div>







        </div>




    </div>



</div>