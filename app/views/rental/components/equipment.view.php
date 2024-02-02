<?php
// Assuming $equipment is an array of stdClass objects


foreach ($equipment as $item) {
    ?>
    <div class="equipment-details">
        <span class="close-button">&times;</span>
        <h2>Equipment Details</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($item->name); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($item->type); ?></p>
        <p><strong>Cost:</strong> <?php echo htmlspecialchars($item->cost); ?></p>
        <p><strong>Rental Fee:</strong> <?php echo htmlspecialchars($item->fee); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($item->description); ?></p>
        <p><strong>Count:</strong> <?php echo htmlspecialchars($item->count); ?></p>
        <?php if (!empty($item->image)) { ?>
            <img id="detail-image" src="<?php  echo OSURL . "images/equipment/" . htmlspecialchars($item->image); ?>" alt="Equipment Image">
        <?php } ?>
    </div>

    <div class="edit-button">
        <button id="edit-equipment-button" class="btn btn-full m-1">Edit</button>

        
    </div>
    <div class="delete-button">
        <button id="delete-equipment-button" class="btn btn-danger btn-full m-1">Delete</button>
    </div>

    
    <!-- delete modal -->

    <div id="delete-equipment-modal" class="delete-equipment-modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Delete Equipment</h2>
            <p>Are you sure you want to delete this equipment?</p>
            <form id="delete-equipment-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item->id); ?>">
                <input type="submit" value="Delete">
            </form>
        </div>
    </div>

    <!-- delete modal end -->

    <!-- edit modal -->

    <div id="edit-equipment-modal" class="edit-equipment-modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form id="edit-equipment-form" enctype="multipart/form-data">
                <h2>Edit Equipment</h2>

                <label for="equipment-name">Equipment Name</label>
                <input type="text" id="equipment-name" name="equipment_name" value="<?php echo htmlspecialchars($item->name); ?>" required>

                <label for="equipment-type">Type</label>
                <input type="text" id="equipment-type" name="equipment_type" value="<?php echo htmlspecialchars($item->type); ?>" required>

                <label for="cost">Cost</label>
                <input type="number" step="0.01" id="cost" name="cost" value="<?php echo htmlspecialchars($item->cost); ?>" required>

                <label for="rental-fee">Rental Fee</label>
                <input type="number" step="0.01" id="rental-fee" name="rental_fee" value="<?php echo htmlspecialchars($item->fee); ?>" required>

                <label for="description">Description</label>
                <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($item->description); ?>" required>

                <label for="count">Count</label>
                <input type="number" id="count" name="count" value="<?php echo htmlspecialchars($item->count); ?>" required>

                <label for="fee">Fee</label>
                <input type="number" step="0.01" id="fee" name="fee" value="<?php echo htmlspecialchars($item->fee); ?>" required>

                <label for="equipment-image">Equipment Image</label>
                <input type="file" id="equipment-image" name="equipment_image">

                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item->id); ?>">

                <input type="submit" value="Update">
            </form>
        </div>


    </div>

    <!-- edit modal end -->

    <script>
        var deleteButtons = document.querySelectorAll("#delete-equipment-button");

        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                var modal = document.getElementById("delete-equipment-modal");
                console.log("modal");
                modal.style.display = "block";
            });

        });

        console.log("delete buttons", deleteButtons);
    

        var editButtons = document.querySelectorAll("#edit-equipment-button");

        editButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                var modal = document.getElementById("edit-equipment-modal");
                console.log("modal");
                modal.style.display = "block";
            });

        });


        

        </script>



<style>

.delete-equipment-modal {
    display: none;
    position: fixed;
    z-index: 200;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.4); /* Unified background color */
    /* padding-top: 60px; */
}

.edit-equipment-modal {
    display: none;
    position: fixed;
    z-index: 200;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.4); /* Unified background color */
    /* padding-top: 60px; */
}



    </style>




    <?php
}
?>