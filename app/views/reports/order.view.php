<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
      
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    margin: 40px;
                    color: #333;
                    line-height: 1.6;
                }
                h1, h2 {
                    text-align: center;
                    color: #026;
                }
                table {
                    width: 100%;
                    margin-top: 20px;
                    border-collapse: collapse;
                    border-spacing: 0;
                    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 12px 15px;
                    text-align: left;
                    width: 200px;
                }
                th {
                    background-color: #f8f8f8;
                    color: #333;
                }
                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
                p {
                    text-align: center;
                }
                .footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 0.85em;
                    color: #666;
                }
                .footer a {
                    color: #333;
                }
            
    </style>
</head>

<body>
    <div id="order-item-content">

    <h1><?= $companyName ?></h1>
            <h2><?= $reportTitle ?></h2>


        <div class="order-item">
            <div class="row card-normal m-2">
                <div class="col-lg-6 col-md-12">
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

                        <tr>
                            <td>Start Date:</td>
                            <td><?= date('Y-m-d', strtotime($order->start_date)) ?></td>
                        </tr>

                        <tr>
                            <td>End Date:</td>
                            <td><?= date('Y-m-d', strtotime($order->end_date)) ?></td>
                        </tr>
                        <tr>
                            <td>Order Total:</td>
                            <td><?= $order->total ?></td>
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
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row card-normal m-2">
                <div class="col-lg-12 col-md-12">
                    <h3>Equipment List</h3>
                    <table class="item-details">
                        <tr>
                            <th>Item number</th>
                            <th>Name</th>
                            <!-- <th>Fee</th> -->
                        </tr>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?= $item->item_number ?></td>
                                <td><?= $item->equipment_name ?></td>
                                <!-- <td><?= $item->equipment_cost ?></td> -->
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>