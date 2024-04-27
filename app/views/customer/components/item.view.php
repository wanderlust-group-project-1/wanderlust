<div id="rent-item-details" data-id="<?= htmlspecialchars($equipment->id);?>">
<div class="row mh-100px justify-content-start">

<a href="<?php echo ROOT_DIR; ?>/rentalService/<?php echo htmlspecialchars($equipment->rentalservice_id); ?>" class="rental-link">
    <div class="col-lg-12 flex-d align-items-center gap-2" >

        <img src="<?php echo ROOT_DIR; ?>/uploads/images/rental_services/<?php echo htmlspecialchars($equipment->rentalservice_image); ?>" alt="Image" class="img-fluid mh-50px rounded-7">
        <h3 class="rental-name"> <?php echo htmlspecialchars($equipment->rentalservice_name); ?> </h3>
       
        <!-- <?php show($equipment) ?> -->
    </div>
    </a>
</div>
<div class="row flex-d mt-5">

        <div class="col-lg-6">
            <img src="<?php echo htmlspecialchars(OSURL); ?>images/equipment/<?php echo htmlspecialchars($equipment->image); ?>" alt="Image" class="img-fluid mh-200px rounded-7">
            
        </div>

            <div class="col-lg-6">
                <h1 class="rental-name"> <?php echo htmlspecialchars($equipment->name); ?> </h1>
                <p class="rental-description"> <?php echo htmlspecialchars($equipment->description); ?> </p>
                
                <!-- set count input -->
                <input class="form-control-lg" type="number" name="count" id="item-count" value="1" min="1" max="<?php echo htmlspecialchars($equipment->count); ?>">


                


                <h3 id="item-fee"   data-fee="<?php echo htmlspecialchars($equipment->total); ?>">Rs. <?php echo htmlspecialchars($equipment->total); ?></h3>

                <button id="add-to-cart" class="btn-text-green border">Add to Cart</button>
                <!-- <button  class="btn btn-primary">Add to Cart</button> -->

            </div>


</div>

</div>