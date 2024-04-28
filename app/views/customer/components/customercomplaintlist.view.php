
<div id="complaint-list-content" class=" col-lg-12">

    
<?php

//  oreders list 
  ($complaints) ?  : print('<div class="col-lg-12"><h1>No Complaints</h1></div>');  

    foreach ($complaints as $complaint) {
        ?>
        
        <div class = "row flex-d col-lg-12 complaint-card-item row-content" id="complaint-card">
        <div class="complaint card  card-normal3 col-lg-12 flex-md-c miw-200px" data-id="<?= $complaint->complaint_no ?>">
            <div class="complaint-header">
                <div class="complaint-id">Complaint ID: <?= $complaint->complaint_no ?></div>
                <div class="complaint-status">Status: <?= $complaint->status ?></div>
            </div>

            <div class="complaint-body">
                <div class="complaint-description"> <?= $complaint->description ?></div>
            </div>

            <div class="complaint-actions flex-d gap-3">
                <button class="btn-text-green" id="mycom-view-button"><i class="fa fa-list" aria-hidden="true"></i> View</button>
                
                <!-- if status pending set show  -->
                <?php 
                if ($complaint->status == 'pending') { ?>
                    <button class="btn-text-red" id="cancel-complaint"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button> 
                <?php }?>
                

  
            </div>
        </div>
        </div>
    <?php
    }
?>


</div>



<!-- Complaint Modals -->

<div id="report-complaint-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div id="complaint-form-data">

        </div>
    </div>
</div>

<style>
 
</style>