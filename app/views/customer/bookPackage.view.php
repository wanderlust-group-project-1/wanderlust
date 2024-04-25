<div class="payment-details" id="payment-details-<?php echo htmlspecialchars($package[0]->id); ?>">
    <span class="close">&times;</span>
    <div class="container flex-d-c gap-4 p-md-0 ">
        <h2>Booking Details</h2>

        <div class="row">

            <div class="col-lg-6 col-md-12">

                <table class="table-details">
                    <tr>
                        <td><strong>Maximum Group Size:</strong></td>
                        <td><?php echo htmlspecialchars($package[0]->max_group_size); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Max Distance:</strong></td>
                        <td><?php echo htmlspecialchars($package[0]->max_distance); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Transport Needed:</strong></td>
                        <td><?php echo $package[0]->transport_needed ? 'Yes' : 'No'; ?></td>
                    </tr>

                    <tr>
                        <td><strong>Places:</strong></td>
                        <td><?php echo htmlspecialchars($package[0]->places); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Price:</strong></td>
                        <td><?php echo htmlspecialchars($package[0]->price); ?></td>
                    </tr>
                </table>

            </div>

        </div>

        <div class="flex-d-center gap-2 mt-5">
            <button id="book-pre-pay" class="btn" data-id="<?php echo htmlspecialchars($package[0]->id); ?>">Pay</button>
        </div>

    </div>
</div>