
<div id="complaint-list-content" class=" col-lg-12">

    
<?php

//  oreders list 
  ($complaints) ?  : print('<div class="col-lg-12"><h1>No Complaints</h1></div>');  

    foreach ($complaints as $complaint) {
        ?>
        
        <div class = "row flex-d col-lg-12 complaint-card-item " id="complaint-card">
        <div class="complaint card  card-normal3 col-lg-12 flex-md-c miw-200px" data-id="<?= $complaint->id ?>">
            <div class="complaint-header">
                <div class="complaint-id">Complaint ID: <?= $complaint->id ?></div>
                <div class="complaint-status">Status: <?= $complaint->status ?></div>
            </div>

            <div class="complaint-body">
                <div class="complaint-description"> <?= $complaint->description ?></div>
            </div>

            <div class="complaint-actions flex-d gap-3">
                <button class="btn-text-green" id="view-button"><i class="fa fa-list" aria-hidden="true"></i> View</button>
                <!-- if status pending set show  -->
                <?php if ($complaint->status == 'myComplaints') {
                   

                    
                        
                    
                    ?>
                    <div class="flex-d-c">
                    <button class="btn-text-red" id="cancel-complaint"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>  


                    <button class="btn-text-red" id="cancel-rented" hidden>Cancel</button>
                    </div>
                <?php 
                 }
                elseif ($complaint->status == 'rented') { ?>
                    <button class="btn btn-primary" id="mark-as-returned">Mark as Returned</button>
                <?php }
                elseif ($complaint->status == 'pending') { ?>
                    <button class="btn-text-orange" id="accept-complaint"><i class="fa fa-check" aria-hidden="true"></i> Accept</button>
                    <button class="btn-text-red" id="cancel-complaint"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>  
                <?php }
                ?>

  
            </div>
        </div>
        </div>
        <?php
    }



?>


</div>




<!-- View modal -->

<div class="modal" id="complaint-view-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        
        <div id="complaint-data">   </div>
    </div>
</div>



<!-- Complaint Modals -->

<div id="report-complaint-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div id="complaint-form-data">

        </div>
    </div>
</div>


<!-- complaint cancel -->

<div id="cancel-complaint-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Cancel Complaint</h2>
        <p>Are you sure you want to cancel this complaint?</p>
        <div class="flex-d gap-3 mt-3">
            <button class="btn btn-primary" id="cancel-complaint-confirm">Yes</button>
            <button class="btn btn-danger modal-close" id="cancel-complaint-cancel">No</button>
        </div>
    </div>
</div>

<style>
 
</style>