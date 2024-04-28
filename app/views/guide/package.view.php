<?php
foreach ($data["package"] as $package) {
?>
    <div class="package-details" id="package-details-<?php echo htmlspecialchars($package->id); ?>" data-id="<?php echo htmlspecialchars($package->id); ?>">
        <span class="modal-close close">&times;</span>
        <div class="container flex-d-c gap-4 p-md-0 ">
            <h2 class="guide-h2-title">Package Details</h2>

            <div class="row">

                <div class="col-lg-6 col-md-12">

                    <table class="table-details">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($package->name); ?></td>

                        </tr>

                        <tr>
                            <td><strong>Price:</strong></td>
                            <td><?php echo htmlspecialchars($package->price); ?></td>
                        </tr>

                        <tr>
                            <td><strong>Maximum Group Size:</strong></td>
                            <td><?php echo htmlspecialchars($package->max_group_size); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Max Distance:</strong></td>
                            <td><?php echo htmlspecialchars($package->max_distance); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Transport Needed:</strong></td>
                            <td><?php echo $package->transport_needed ? 'Yes' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Places:</strong></td>
                            <td><?php echo htmlspecialchars($package->places); ?></td>
                        </tr>
                    </table>

                </div>

            </div>

            <div class="edit-button">
                <button class="edit-package-button btn btn-full m-1" data-id="<?php echo htmlspecialchars($package->id); ?>">Edit</button>
            </div>

            <div class="delete-button">
                <button class="delete-package-button btn btn-danger btn-full m-1" data-id="<?php echo htmlspecialchars($package->id); ?>">Delete</button>
            </div>

        </div>
    </div>
<?php
}
?>


<!--modal box edit package-->

<div class="modal" id="edit-package-modal" style="display: none;">
    <div class="modal-content">
        <span class="modal-close close">&times;</span>
        <form id="update-package-form" packageId="<?php echo htmlspecialchars($package->id); ?>" class="form" method="POST" enctype="multipart/form-data">
            <h2 class="guide-h2-title">Update Package</h2>

            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">

                <label for="name">Name</label>
                <input type="text" id="name" class="form-control-lg" name="name" required value="<?php echo htmlspecialchars($package->name); ?>">

                <label for="price">Price</label>
                <input type="text" id="price" class="form-control-lg" name="price" required value="<?php echo htmlspecialchars($package->price); ?>">

                <label for="max_group_size">Maximum Group Size</label>
                <input type="number" id="max_group_size" class="form-control-lg" name="max_group_size" required value="<?php echo htmlspecialchars($package->max_group_size); ?>">


                <label for="max_distance">Maximum Distance</label>
                <input type="number" id="max_distance" class="form-control-lg" name="max_distance" required value="<?php echo htmlspecialchars($package->max_distance); ?>">

                <label for="transport_needed">Transport Needed</label>
                <input type="checkbox" id="transport_needed2" class="form-control-lg" name="transport_needed" <?php echo $package->transport_needed == 1 ? 'checked' : ''; ?>>y

                <label for="places">Places</label>
                <textarea id="places" class="form-control-lg" name="places" required><?php echo htmlspecialchars($package->places); ?></textarea>

            </div>

            <div class="row">
                <input type="submit" class="btn" value="Done">
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function() {
        const placesContainer = document.getElementById('places-container');
        const addPlaceFieldBtn = document.getElementById('add-place-field');

        // Function to add a new place field
        function addPlaceField() {
            const newPlaceField = document.createElement('div');
            newPlaceField.classList.add('place-fields');
            newPlaceField.innerHTML = `
                <select class="form-control-lg place-dropdown" name="places" required>
                    <option value="">Select a place</option>
                    <option value="place1">Place 1</option>
                    <option value="place2">Place 2</option>
                    <option value="place3">Place 3</option>
                </select>
                <input type="text" class="form-control place-details" placeholder="Enter details for selected place" disabled>
                <button type="button" class="remove-place-field" onclick="removePlaceField(this)">Remove</button>
            `;
            placesContainer.appendChild(newPlaceField);

            // Add event listener to new place dropdown
            newPlaceField.querySelector('.place-dropdown').addEventListener('change', function() {
                if (this.value !== '') {
                    enablePlaceDetailsField(this);
                }
            });
        }

        // Function to enable place details field
        function enablePlaceDetailsField(selectElement) {
            const placeDetailsField = selectElement.parentElement.querySelector('.place-details');
            placeDetailsField.disabled = false;
        }

        // Function to remove a place field
        function removePlaceField(button) {
            button.parentElement.remove();
        }

        // Add event listener to "Add Place Field" button
        addPlaceFieldBtn.addEventListener('click', addPlaceField);
    });
</script>

<div id="delete-package-modal" class="delete-package-modal modal">
    <div class="modal-content ">
        <span class="close ">&times;</span>
        <h2 class="guide-h2-title">Delete Package</h2>
        <p>Are you sure you want to delete this package?</p>
        <div class="flex-d gap-2 mt-5">
            <button id="delete-package" class="btn btn-danger">Delete</button>
            <button id="cancel-delete" class="btn modal-close">Cancel</button>
        </div>

    </div>
</div>

<style>
    .delete-package-modal {
        display: none;
        position: fixed;
        z-index: 200;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.4);
        /* Unified background color */
        /* padding-top: 60px; */
    }

    .edit-package-modal {
        display: none;
        position: fixed;
        z-index: 200;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.4);
        /* Unified background color */
        /* padding-top: 60px; */
    }
</style>