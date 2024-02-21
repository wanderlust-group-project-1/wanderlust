<?php
// Assuming $equipment is an array of stdClass objects


foreach ($equipment as $item) {
    ?>
    <div class="equipment-details">
        <span class="close-button">&times;</span>
        <div class="container flex-d-c gap-4 ">
        <h2>Equipment Details</h2>

        <div class="row">

        <div class="col-lg-6">

        
        <p><strong>Name:</strong> <?php echo htmlspecialchars($item->name); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($item->type); ?></p>
        <p><strong>Cost:</strong> <?php echo htmlspecialchars($item->cost); ?></p>
        <p><strong>Rental Fee:</strong> <?php echo htmlspecialchars($item->fee); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($item->description); ?></p>
        <p><strong>Count:</strong> <?php echo htmlspecialchars($item->count); ?></p>
        </div>
        <div class="col-lg-6">
        <?php if (!empty($item->image)) { ?>
            <img class="mw-100" id="detail-image" src="<?php  echo OSURL . "images/equipment/" . htmlspecialchars($item->image); ?>" alt="Equipment Image">
        <?php } ?>

        </div>
        </div>

        <div class="edit-button">
        <button id="edit-equipment-button" class="btn btn-full m-1">Edit</button>

        
    </div>
    <div class="delete-button">
        <button id="delete-equipment-button" class="btn btn-danger btn-full m-1">Delete</button>
    </div>


        </div>
    </div>

    

    
    <!-- delete modal -->

    <div id="delete-equipment-modal" class="delete-equipment-modal modal">
        <div class="modal-content ">
            <span class="close ">&times;</span>
            <h2>Delete Equipment</h2>
            <p>Are you sure you want to delete this equipment?</p>
            <div class="flex-d gap-2 mt-5">
            <button id="delete-equipment" class="btn btn-danger">Delete</button>
            <button id="cancel-delete" class="btn modal-close">Cancel</button>
            </div>
  
        </div>
    </div>

    <!-- delete modal end -->

    <!-- edit modal -->

    <!-- <div id="edit-equipment-modal" class="edit-equipment-modal">
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


    </div> -->


    <div class="edit-equipment-modal modal" id="edit-equipment-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="update-equipment-form" itemid="<?php echo htmlspecialchars($item->id); ?>"  class="flex-d gap-2"  enctype="multipart/form-data">
            <h2>Update Equipment</h2>

            <div class="row align-items-start">
            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">

            <label for="equipment-name">Equipment Name</label>
            <input type="text" id="equipment-name" class="form-control-lg" name="equipment_name" required value="<?php echo htmlspecialchars($item->name); ?>">

            <!-- <label for="equipment-type">Type</label>
            <input type="text" id="equipment-type" class="form-control-lg" name="equipment_type" required> -->
            <label for="equipment-type">Type</label>
            <select id="equipment-type" class="form-control-lg" name="equipment_type" required value="<?php echo htmlspecialchars($item->type); ?>">
                <option value="Tent">Tent</option>
                <option value="Cooking">Cooking</option>
                <option value="Backpack">Backpack</option>
                <option value="Sleeping">Sleeping</option>
                <option value="Climbing">Climbing</option>
                <option value="Clothing">Clothing</option>
                <option value="Footwear">Footwear</option>
                <option value="Other">Other</option>


                
            </select>

            
            <label for="description">Description</label>
            <!-- <input type="text" id="description" class="form-control-lg" name="description" required> -->
            <textarea id="description" class="form-control-lg" name="description" required>
            <?php echo htmlspecialchars($item->description); ?>
            </textarea>

            </div>
            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
           
            <label for="cost">Cost</label>
            <input type="number" step="0.01" id="cost" class="form-control-lg" name="cost" required value="<?php echo htmlspecialchars($item->cost); ?>">


            <!-- Standard fee -->
            <label for="standard-fee">Standard Fee</label>
            <input type="number" step="0.01" id="standard-fee" class="form-control-lg" name="standard_fee" required value="<?php echo htmlspecialchars($item->standard_fee); ?>">


            <label for="rental-fee">Rental Fee (per day)</label>
            <input type="number" step="0.01" id="rental-fee" class="form-control-lg" name="rental_fee" required value="<?php echo htmlspecialchars($item->fee); ?>">



            <label for="count">Quantity</label>
            <input type="number" id="count" class="form-control-lg" name="count" required value="<?php echo htmlspecialchars($item->count); ?>">

       
            <label for="equipment-image">Equipment Image</label>
            <input type="file" id="equipment-image" class="form-control-lg" name="equipment_image" >


            </div>
                    </div>
            <div class="row">
            <input id="update-equipment" type="submit" class="btn" value="Update Equipment">
            </div>
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


        // update-equipment , use jquery to json , prevent default

        $("#update-equipment-form").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            var id = $(this).attr('itemid');

            var jsonData = {
                id: id,
                name: formData.get('equipment_name'),
                type: formData.get('equipment_type'),
                description: formData.get('description'),
                cost: formData.get('cost'),
                standard_fee: formData.get('standard_fee'),
                rental_fee: formData.get('rental_fee'),
                count: formData.get('count'),
            };

            console.log("json data", jsonData);

            // if image is not empty then append it to formdata

            formData.append('json', JSON.stringify(jsonData));

            if (formData.get('equipment_image') != '') {
                var image = formData.get('equipment_image');

                formData.append('image', image);


            }

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },

                url: '<?= ROOT_DIR ?>/api/equipment/update/' + id,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    // console.log(data);
                    fetchEquipmentDetails(id);
                    // location.reload();
                },
                error: function(data) {
                    console.log(data);
                }

            })



            })


        $("#delete-equipment").click(function() {
            var id = <?php echo htmlspecialchars($item->id); ?>;
            console.log("delete equipment", id);
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/equipment/delete/' + id,
                method: 'POST',
                success: function(data) {
                    console.log(data);
                    alertmsg('Equipment deleted successfully', 'success');
                    getEquipments();
                },
                error: function(data) {
                    console.log(data);
                    alertmsg('Equipment could not be deleted', 'error');
                }
            })
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