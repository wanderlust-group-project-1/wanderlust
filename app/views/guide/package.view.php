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
                <button class="btn-text-green border btn-full m-1" id="edit-package-button" data-id="<?php echo htmlspecialchars($package->id); ?>">Edit</button>
            </div>

            <div class="delete-button">
                <button class="btn-text-red border btn-full m-1" id="delete-package-button" data-id="<?php echo htmlspecialchars($package->id); ?>">Delete</button>
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
                <input type="checkbox" id="transport_needed2" class="form-control-lg" name="transport_needed" <?php echo $package->transport_needed == 1 ? 'checked' : ''; ?>>

                <div>
    <?php
    // Assuming $package->places is a comma-separated string of places
    $places = explode(',', $package->places); // Split places string into an array

    // Iterate through each place and generate dropdowns
    foreach ($places as $index => $place) {
    ?>
        <div class="dropdown">
            <label for="location<?php echo $index + 1; ?>">Area <?php echo $index + 1; ?>:</label>
            <select id="location<?php echo $index + 1; ?>" name="location<?php echo $index + 1; ?>">
                <!-- Populate options dynamically -->
                <option value="Kandy" <?php echo ($place === "Kandy") ? 'selected' : ''; ?>>Kandy</option>
                <option value="Ella" <?php echo ($place === "Ella") ? 'selected' : ''; ?>>Ella</option>
                <option value="Nuwara Eliya" <?php echo ($place === "Nuwara Eliya") ? 'selected' : ''; ?>>Nuwara Eliya</option>
            </select>
            <textarea id="textarea<?php echo $index + 1; ?>" name="textarea<?php echo $index + 1; ?>" rows="4" cols="50"></textarea>
            <button onclick="removeDropdown(this)">-</button>
        </div>
    <?php
    }
    ?>
    <div id="dropdownContainer">
        <!-- This is where dynamically added dropdowns will go -->
    </div>

    <button onclick="editDropdown()">+</button>
    <div class="row">
        <input type="submit" class="btn" value="Done">
    </div>
</div>

<script>
    $(document).ready(function() {
        const editDropdownButton = document.querySelector('button[onclick="editDropdown()"]');
        editDropdownButton.click();
    });
    function editDropdown(place = '') {
        const container = document.getElementById('dropdownContainer');
        const dropdownCount = container.getElementsByClassName('dropdown').length;

        const newDropdown = document.createElement('div');
        newDropdown.classList.add('dropdown');

        const label = document.createElement('label');
        label.textContent = `Location ${dropdownCount + 1}:`;
        newDropdown.appendChild(label);

        const select = document.createElement('select');
        select.name = `location${dropdownCount + 1}`;
        select.id = `location${dropdownCount + 1}`;

        const options = ['Kandy', 'Ella', 'Nuwara Eliya'];
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            select.appendChild(optionElement);
        });

        newDropdown.appendChild(select);

        const textarea = document.createElement('textarea');
        textarea.id = `textarea${dropdownCount + 1}`;
        textarea.name = `textarea${dropdownCount + 1}`;
        textarea.rows = "4";
        textarea.cols = "50";
        newDropdown.appendChild(textarea);

        const removeButton = document.createElement('button');
        removeButton.textContent = '-';
        removeButton.onclick = function() {
            removeDropdown(this);
        };
        newDropdown.appendChild(removeButton);

        container.appendChild(newDropdown);

        // Set the default place if provided
        if (place !== '') {
            select.value = place;
        }
    }

    function removeDropdown(button) {
        button.parentNode.remove();
    }
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