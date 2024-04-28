<div class=" col profile-info m-5">
    <div class="row">
        <img src="<?php echo OSURL?>images/customer/<?= $customer->image ?>" alt="Profile Image" class="mw-200px px-4 p-6">
        <div>
            <h2 id="profile-name"> <?php echo $customer->name ?> </h2>
            <p id="profile-email"><i class="fas fa-envelope m-2"></i><?php echo $customer->email ?></p>
            <p id="profile-nic"><i class="fas fa-id-card m-2"></i><?php echo $customer->nic ?></p>
            <p id="profile-number"><i class="fas fa-phone m-2"></i> <?php echo $customer->number ?></p>
            <p id="profile-address"><i class="fa fa-location-arrow m-2"></i><?php echo $customer->address ?></p>
            <!-- <p id="profile-status" class="status <?php echo $customer->status; ?>"><?php echo ucfirst($customer->status); ?></p> -->
            <div class="row">
                <button type="" class="btn-text-red border " id="see-more">
                    Remove User
                </button>
            </div>
        </div>
    </div>
</div>