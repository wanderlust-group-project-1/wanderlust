<div class="book-package-details" id="book-package-details-<?php echo htmlspecialchars($package[0]->id);?>">
    <span class="close">&times;</span>
    <div class="container flex-d-c gap-4 p-md-0 ">
        <h2>Package Details</h2>

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
                </table>

            </div>

        </div>

        <div class="edit-button">
            <button id=book-guide-package class="book-guide-package btn btn-full m-1" data-id="<?php echo htmlspecialchars($package[0]->id); ?>">Book</button>
        </div>
    </div>
</div>