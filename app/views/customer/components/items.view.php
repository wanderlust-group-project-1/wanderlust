<div class="container">
    <!-- <?php show($equipments); ?> -->
    <div class="rent-items">
        <?php foreach ($equipments as $equipment): ?>
            <div class="rent-item-card" id="rent-item-card" data-id="<?= htmlspecialchars($equipment->id); ?>">
            <div id="rent-item-details" class="h-100 flex-d-c justify-content-center aligh-items-center gap-3 rent-item-details p-3" data-id="<?= htmlspecialchars($equipment->id);?>">
                <div class="rent-item-image">
                    <!-- Assuming you have a way to generate the image URL from the image name -->
                    <img src="<?=OSURL?>images/equipment/<?php echo htmlspecialchars($equipment->image); ?>" alt="Image" class="card-img">
                </div>
                <div class="rent-item-details align-items-end">
                    <h5 class="rent-item-name"><?php echo htmlspecialchars($equipment->name); ?></h5>
                    <p class="rent-item-description"><?php echo $equipment->description; ?></p>
                    <div class="rent-item-price ">
                        <h5>Rs. <?php echo htmlspecialchars($equipment->total); ?></h5>
                        <!-- <div class="cost-text">
                            <p class="cost">total cost</p>
                        </div> -->
                    </div>
                    <!-- <div class="rating">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star-half-o" aria-hidden="true"></i>
                    <i class="fa fa-star-o" aria-hidden="true"></i>
                    </div> -->
                    <button  class="btn btn-primary">View</button>
                </div>
            </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>



<!-- modal for equipment details -->

<div class="modal" id="equipment-details-modal" style="display: none;">
<!-- <div class="modal" id="equipment-details-modal"> -->
<div class="modal-content gap-2">
    <span class="close">&times;</span>

    <div id="equipment-details" >

    

    </div>







</div>

</div>