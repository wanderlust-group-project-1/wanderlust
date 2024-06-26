<?php
// Assuming $equipment is an array of stdClass objects


foreach ($equipment as $item) {
    ?>
    <div class="equipment-details" id="equipment-details" data-id="<?php echo htmlspecialchars($item->id); ?>">
        <span class="close">&times;</span>
        <div class="container flex-d-c gap-4 p-md-0 ">
        <h2>Equipment Details</h2>

        <div class="row">

        <div class="col-lg-6 col-md-12">

        
        <!-- <p><strong>Name:</strong> <?php echo htmlspecialchars($item->name); ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($item->type); ?></p>
        <p><strong>Cost:</strong> <?php echo htmlspecialchars($item->cost); ?></p>
        <p><strong>Rental Fee:</strong> <?php echo htmlspecialchars($item->fee); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($item->description); ?></p>
        <p><strong>Count:</strong> <?php echo htmlspecialchars($item->count); ?></p> -->

        <!-- table -->

        <table class="table-details">
            <tr>
                <td><strong>Name</strong></td>
                <td><?php echo htmlspecialchars($item->name); ?></td>
            </tr>
            <tr>
                <td><strong>Type</strong></td>
                <td><?php echo htmlspecialchars($item->type); ?></td>
            </tr>
            <tr>
                <td><strong>Cost</strong></td>
                <td><?php echo htmlspecialchars($item->cost); ?></td>
            </tr>
            <tr>
                <td><strong>Rental Fee</strong></td>
                <td><?php echo htmlspecialchars($item->fee); ?></td>
            </tr>
            <tr>
                <td><strong>Description</strong></td>
                <td><?php echo htmlspecialchars($item->description); ?></td>
            </tr>
            <tr>
                <td><strong>Quantity</strong></td>
                <td><?php echo htmlspecialchars($item->count); ?></td>
            </tr>
        </table>


        



        </div>
        <div class="col-lg-6 col-md-12">
        <?php if (!empty($item->image)) { ?>
            <img class="mw-100 mw-250px" id="detail-image" src="<?php  echo OSURL . "images/equipment/" . htmlspecialchars($item->image); ?>" alt="Equipment Image">
        <?php } ?>

        </div>
        </div>

        <div class="row flex-d">
        <div class="edit-button">
        <button id="edit-equipment-button" class="btn-text-orange m-1"><i class="fas fa-edit"></i>Edit</button>        
    </div>

    <!-- increase count -->
    <div class="increase-count-button">
        <button id="increase-count-button" class="btn-text-green  m-1" data-id="<?php echo htmlspecialchars($item->id); ?>" ><i class="fa fa-plus" aria-hidden="true"></i> Increase Quantity</button>
    </div>
    <!-- Manage Items -->
    <div class="manage-items-button">
        <button id="manage-items-button" class="btn-text-orange m-1" data-id="<?php echo htmlspecialchars($item->id); ?>"><i class="fa fa-tasks" aria-hidden="true"></i>Manage Items</button>
    </div>

        </div>

        <div class="row flex-d">

    <!-- disable -->
    <div class="disable-button">
        <button id="disable-equipment-button" class="btn-text-red m-1" data-id="<?php echo htmlspecialchars($item->id); ?>"><i class="fa fa-ban" aria-hidden="true"></i>Disable</button>
    </div>


    <div class="delete-button">
        <button id="delete-equipment-button" class="btn-text-red m-1" data-id="<?php echo htmlspecialchars($item->id); ?>"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button>
    </div>
        </div>


        </div>
    </div>

    
    <!-- Manage Item Modal -->

    <div id="manage-items-modal" class="manage-items-modal modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Manage Items</h2>
         <div class="flex-d-c gap-2" id="manage-items-content">
            
         </div>
            </div>
        </div>


    <!-- increase count modal -->

    <div id="increase-count-modal" class="increase-count-modal modal" >
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="increase-count-form" class="flex-d-c gap-2 justify-content-center text-center">
                <h2>Increase Quantity</h2>

                <!-- Current count -->
                <!-- <p>Current Count: <?php echo htmlspecialchars($item->count); ?></p> -->

                <div class="flex-d gap-2 justify-content-between ">
                <label for="count">Current Quantity</label>
                <input type="text" id="current-count" name="count" value="<?php echo htmlspecialchars($item->count); ?>" disabled>
                </div>

                <div class="flex-d gap-2 justify-content-between ">
                <label for="count">Quantity</label>

                <input type="number" id="count" name="count" required>
                </div>

                <!-- Total -->
                <div class="flex-d gap-2 justify-content-between ">
                <label for="total">Total</label>
                <input type="text" id="total" name="total" value="<?php echo htmlspecialchars($item->count); ?>" disabled>
                </div>
                <div class="flex-d gap-2 justify-content-center">
                <button type="submit" id="increase-count" class="btn-text-green border"><i class="fa fa-plus" aria-hidden="true"></i> Increase Quantity</button>
                </div>
            </form>
        </div>
    </div>

    
    <!-- delete modal -->

    <div id="delete-equipment-modal" class="delete-equipment-modal modal">
        <div class="modal-content ">
            <span class="close ">&times;</span>
            <div class="flex-d-c gap-2 justify-content-center text-center">

            <h2>Delete Equipment</h2>
            <p>Are you sure you want to delete this equipment?</p>
            <div class="flex-d gap-2 mt-5">
            <button id="delete-equipment" class="btn-text-red border">Delete</button>
            <button id="cancel-delete" class="btn-text-green border  modal-close">Cancel</button>
            </div>
            </div>
  
        </div>
    </div>



    <!-- delete modal end -->


    <!-- Disable modal -->

    <div id="disable-equipment-modal" class="disable-equipment-modal modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="flex-d-c gap-2 justify-content-center text-center">
            <h2>Disable Equipment</h2>
            <p>Are you sure you want to disable this equipment?</p>
            <div class="flex-d gap-2 mt-5">
            <button id="disable-equipment" class="btn-text-red border">Disable</button>
            <button id="cancel-disable" class="btn-text-green border modal-close">Cancel</button>
            </div>
            </div>
        </div>
    </div>
    

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

<!-- Update Equipment modal -->
    <div class="edit-equipment-modal modal" id="edit-equipment-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="update-equipment-form" class="flex-d-c text-center" itemid="<?php echo htmlspecialchars($item->id); ?>"  class="flex-d gap-2"  enctype="multipart/form-data">
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
            <textarea id="description" class="form-control-lg" name="description" required><?php echo htmlspecialchars($item->description); ?></textarea>

            </div>
            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
           
            <label for="cost">Cost</label>
            <input type="number" step="0.01" id="cost" class="form-control-lg" name="cost" required value="<?php echo htmlspecialchars($item->cost); ?>">


            <!-- Standard fee -->
            <label for="standard-fee">Standard Fee</label>
            <input type="number" step="0.01" id="standard-fee" class="form-control-lg" name="standard_fee" required value="<?php echo htmlspecialchars($item->standard_fee); ?>">


            <label for="rental-fee">Rental Fee (per day)</label>
            <input type="number" step="0.01" id="rental-fee" class="form-control-lg" name="rental_fee" required value="<?php echo htmlspecialchars($item->fee); ?>">



            <!-- <label for="count">Quantity</label>
            <input type="number" id="count" class="form-control-lg" name="count" required value="<?php echo htmlspecialchars($item->count); ?>"> -->

       
            <label for="equipment-image">Equipment Image</label>
            <input type="file" id="equipment-image" class="form-control-lg" name="equipment_image" accept="image/*">


            </div>
                    </div>
            <div class="row">
            <input id="update-equipment" type="submit" class="btn-text-green border" value="Update Equipment">
            </div>
        </form>
    </div>
</div>




    <!-- edit modal end -->

  



<style>

.delete-equipment-modal {
    display: none;
    position: fixed;
    z-index: 200;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    /* overflow: hidden; */
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
    /* overflow: hidden; */
    background-color: rgba(0, 0, 0, 0.4); /* Unified background color */
    /* padding-top: 60px; */
}



    </style>




    <?php
}
?>