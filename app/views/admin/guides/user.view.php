      
     <div class=" col profile-info">
        <div class="row">
    <img src="<?php echo ROOT_DIR?>/assets/images/dp.jpg" alt="Profile Image" class="profile-image">
    <div>
        <h2 id="profile-name"> <?php echo $guide->name ?> </h2>
        <p id="profile-email"><?php echo $guide->email ?></p>
        <p id="profile-address"><?php echo $guide->address ?></p>
        <p id="profile-status" class="status <?php echo $guide->status; ?>"><?php echo ucfirst($guide->status); ?></p>
    </div>
    </div>
    <div class="status-links">
        
        <?php 
        // echo $guide->status;
       echo ($guide->status == "waiting") ?  
        '<button class="change-status-btn accepted" data-status="accepted">Accepted</button>
        <button class="change-status-btn rejected" data-status="rejected">Rejected</button>'

        :   ''
        ?>

        <?php echo ($guide->status == "accepted") ?  
        '<button class="change-status-btn waiting" data-status="waiting">Waiting</button>
        <button class="change-status-btn rejected" data-status="rejected">Rejected</button>'
        :   ''
        ?>

         <?php echo ($guide->status == "rejected") ?  
        '<button class="change-status-btn waiting" data-status="waiting">Waiting</button>
        <button class="change-status-btn accepted" data-status="accepted">Accepted</button>'
        :   ''
        ?> 


        <!-- <button class="change-status-btn waiting" data-status="waiting">Waiting</button>
        <button class="change-status-btn accepted" data-status="accepted">Accepted</button>
        <button class="change-status-btn rejected" data-status="rejected">Rejected</button>   -->
      </div>
      <div class="row"> 

      <div class="document-view">
    <a href="<?= OSURL ?>guides/<?= $guide->verification_document ?>" target="_blank" class="view-document-button">View Document</a>
</div>
      </div>


      
</div>

<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<script>
   $(document).ready(function () {
        // Event listener for the button click
        $('.change-status-btn').on('click', function () {
            // Assuming you have a server-side script to handle the status update
            var userId = <?php echo $guide->id; ?>; // Add the user ID here
            var newStatus = $(this).data('status');

            // AJAX request to update the status
            $.ajax({
                type: 'POST',
                url: 'api/guide/updateStatus', // Update with your server-side script URL
                contentType: 'application/json', // Set the content type to JSON
                data: JSON.stringify({ userId: userId, newStatus: newStatus }),
                success: function (data) {
                    // Update the status on the page
                    $('#profile-status').text(data.newStatus);
                    openModal(userId);

                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });
    });
</script>


<style>
    /* Profile Container */
.profile-info {
    display: flex;
    align-items: flex-start;
}

/* Profile Image */
.profile-image {
    width: 100px; /* Adjust the size as needed */
    height: 100px; /* Adjust the size as needed */
    border-radius: 50%;
    margin-right: 20px;
}

/* Profile Name and Email */
#profile-name,
#profile-email {
    margin: 0;
}

/* Profile Status */
#profile-status {
    margin: 5px 0;
}

/* View Button */
.status-links {
    margin-top: 10px;
}

/* Style for a generic button (adjust as needed) */
.status-links button {
    padding: 10px 15px;
    font-weight: 600;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Hover effect for the button */
.status-links button:hover {
    opacity: 0.9;
}

.document-view {
    margin-top: 10px;
}

/* Style for the "View Document" button */
.view-document-button {
    display: inline-block;
    padding: 10px 15px;
    text-decoration: none;
    color: #fff;
    background-color: #3498db; /* Adjust color as needed */
    border: 1px solid #3498db; /* Adjust color as needed */
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

/* Hover effect for the button */
.view-document-button:hover {
    background-color: #2980b9; /* Adjust color as needed */
}


</style>