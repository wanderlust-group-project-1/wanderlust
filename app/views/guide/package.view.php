<?php

foreach ($equipment as $item) {
?>
    <div class="package-details" id="package-details" data-id="<?php echo htmlspecialchars($item->id); ?>">
        <span class="close">&times;</span>
        <div class="container flex-d-c gap-4 p-md-0 ">
            <h2>Package Details</h2>

            <div class="row">

                <div class="col-lg-6 col-md-12">

                    <table class="table-details">
                        <tr>
                            <td><strong>Maximum Group Size:</strong></td>
                            <td><?php echo htmlspecialchars($item->max_group_size); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Max Distance:</strong></td>
                            <td><?php echo htmlspecialchars($item->max_distance); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Transport Needed:</strong></td>
                            <td><?php echo $item->transport_needed ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Places:</strong></td>
                            <td><?php echo htmlspecialchars($item->places); ?></td>
                        </tr>
                    </table>


                </div>

            </div>

            <div class="edit-button">
                <button id="edit-equipment-button" class="btn btn-full m-1">Edit</button>
            </div>

            <div class="delete-button">
                <button id="delete-equipment-button" class="btn btn-danger btn-full m-1" data-id="<?php echo htmlspecialchars($item->id); ?>">Delete</button>
            </div>


        </div>
    </div>

<?php
}
?>