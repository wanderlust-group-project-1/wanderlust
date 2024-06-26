<?php
require_once('../app/views/layout/header.php');
?>

<?php require_once('../app/views/guide/layout/guide-sidebar.php'); ?>

<div class="dashboard">

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">Complains</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Complains</a></li>
        </ul>

        <div class="guide-profile-content mt-5 tiny-topic">
            <p>Add Complains if there is an issue</p>

        
            <div class="dashboard-card mt-5">

<div class="equipment p-4">

    <div class="row justify-content-between gap-3">
        <h1 class="title">Complaints</h1>

        <!-- Section Switch  Upcoming lented Completed -->

        <div class="section-switch flex-d  gap-3 flex-wrap" >
            <button class="btn-selected" id="pending">Pending</button>
            <button class="btn-selected" id="accepted">Accepted</button>
            <button class="btn-selected" id="rejected">Rejected</button>
            <button class="btn-selected" id="resolved">Resolved</button>
            <button class="btn-selected" id="cancelled">Cancelled</button>


            <!-- not rented yet -->
            <!-- <button class="btn-selected" id="not-rented">Not Rented</button> -->

        </div>

      
        


        

       
    </div>


    <div class="complaints-list  row" id="complaints-list">
       





    </div>

</div>

</div>
</div>


</div>


<script>


function getComplaints(status) {
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/complaints/returnComplaintsbyRentalService/' + status,
            type: 'GET',
            success: function(response) {
                // if complaint-list-content in document remove it
                if ($('#complaint-list-content').length) {
                    $('#complaint-list-content').remove();
                }
                $('#complaints-list').html(response);
            }
        });
    }

    $(document).ready(function() {
        getComplaints('pending');

        $('.section-switch button').click(function() {
            $('.section-switch button').removeClass('active');
            $(this).addClass('active');
            getComplaints($(this).attr('id'));
        });
    });


    // View Complaint

    $(document).on('click', '#view-button', function() {
        var complaintId = $(this).closest('.complaint').attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/complaints/viewRentComplaint/' + complaintId,
            type: 'GET',
            success: function(response) {
                $('#complaint-data').html(response);
                $('#complaint-view-modal').css('display', 'block');
            }
        });
    });
    



    $(document).on('click', '#cancel-complaint', function() {
        var complaintId = $(this).closest('.complaint').attr('data-id');
        $('#cancel-complaint-confirm').attr('data-id', complaintId);
        $('#cancel-complaint-modal').css('display', 'block');
    });




    $(document).on('click', '#cancel-complaint-confirm', function() {
        var complaintId = $(this).attr('data-id');
        $.ajax({
            headers:{
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/complaints/cancelComplaint/' + complaintId,
            type: 'GET',
            success: function(response) {
                console.log(response);
                var id = response.data.complaint_id;
                $('#complaint-card[data-id="' + id + '"]').remove();
                $('#cancel-complaint-modal').css('display', 'none');

                getComplaints('pending');



                
            },
            error: function(err) {
                console.log(err);
            }
        });
    });








</script>





<?php
require_once('../app/views/layout/footer.php');
?>

