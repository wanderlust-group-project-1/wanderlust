<div class="row mh-100px">
    <div class="col-lg-12 flex-d align-items-center gap-2">
        <img src="<?php echo ROOT_DIR; ?>/assets/images/rental/1.webp" alt="Image" class="img-fluid mh-50px rounded-7">
        <h1 class="rental-name"> ABC Rent </h1>
        <!-- <?php show($equipment) ?> -->
    </div>
</div>
<div class="row flex-d mt-5">

        <div class="col-lg-6">
            <img src="<?php echo htmlspecialchars(OSURL); ?>images/equipment/<?php echo htmlspecialchars($equipment->image); ?>" alt="Image" class="img-fluid mh-200px rounded-7">
            
        </div>

            <div class="col-lg-6">
                <h1 class="rental-name"> <?php echo htmlspecialchars($equipment->name); ?> </h1>
                <p class="rental-description"> <?php echo htmlspecialchars($equipment->description); ?> </p>
                <h5>Price: Rs. <?php echo htmlspecialchars($equipment->cost); ?></h5>
                <button id="add-to-cart" class="btn btn-primary">Add to Cart</button>
            </div>


</div>