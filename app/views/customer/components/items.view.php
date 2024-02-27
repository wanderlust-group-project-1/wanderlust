<div class="container">
    <!-- <?php show($equipments); ?> -->
    <div class="rent-items">
        <?php foreach ($equipments as $equipment): ?>
            <div class="rent-item-card" data-id="<?= htmlspecialchars($equipment->id) ?>">
                <div class="rent-item-image">
                    <!-- Assuming you have a way to generate the image URL from the image name -->
                    <img src="<?=OSURL?>images/equipment/<?php echo htmlspecialchars($equipment->image); ?>" alt="Image" class="card-img">
                </div>
                <div class="rent-item-details">
                    <h5 class="rent-item-name"><?php echo htmlspecialchars($equipment->name); ?></h5>
                    <p class="rent-item-description"><?php echo $equipment->description; ?></p>
                    <div class="rent-item-price">
                        <h5>Price: Rs. <?php echo htmlspecialchars($equipment->total); ?></h5>
                        <button id="add-to-cart" class="btn btn-primary">Add to Cart</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>



<!-- modal for equipment details -->

<!-- <div class="modal" id="equipment-details-modal" style="display: block;"> -->
<div class="modal" id="equipment-details-modal">
<div class="modal-content gap-2">
    <span class="close">&times;</span>


    <div class="row mh-100px">
        <div class="col-lg-12 flex-d align-items-center gap-2">
            <img src="<? echo ROOT_DIR?>/assets/images/rental/1.webp" alt="Image" class="img-fluid mh-50px rounded-7">
            <h1 class="rental-name"> ABC Rents </h1>
        </div>
    </div>
    <div class="row flex-d mt-5">

           <div class="col-lg-6">
                <img src="<? echo OSURL?>images/equipment/<?php echo $equipment->image; ?>" alt="Image" class="img-fluid mh-200px rounded-7">
               
           </div>

              <div class="col-lg-6">
                 <h1 class="rental-name"> <?php echo $equipment->name; ?> </h1>
                 <p class="rental-description"> <?php echo $equipment->description; ?> </p>
                 <h5>Price: Rs. <?php echo $equipment->cost; ?></h5>
                 <button id="add-to-cart" class="btn btn-primary">Add to Cart</button>
              </div>


    </div>



</div>

</div>