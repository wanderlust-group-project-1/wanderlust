<?php 
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');
?>

<div class="container flex-d flex-md-c justify-content-center mt-5">
    <div class="customer-bg-image">
    <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
    </div>
    <div class="cl-lg-12 flex-d-c gap-2 mt-5">
            <div class="card card-normal-glass">
                <!-- <button class="btn-text-green">hi</button> -->
                <h2 class="justufy-content-ceneter flex-d"> Complaints </h2>

                <div class="section-switch flex-d gap-3 flex-wrap">
                    <button class="btn-selected" id="rentCompaints">My complaints</button>
                <button class="btn-selected" id="returnComplaintsbyCustomer">Recieved complaints</button>
                </div>


                <div class="row gap-2">
                    <div class="col-lg-12 checkout-items overflow-scroll">

                    <div class="complaints-list  row" id="complaints-list"></div>
                    </div>
                </div>
            </div>
        </div>
    
</div>

<script>

    function getComplaints(status) {
        $.ajax({
            headers:{
                'Authorization': 'Bearer' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/complaints/' + status,
            type: 'GET',
            success: function(response) {
                //if complain-list-content in document remove it
                if ($('#complaint-list-content').length) {
                    $('#complaint-list-content').remove();
                }
                $('#complaints-list').html(response);
            }
        });
    }

    $(document).ready(function(){
        getComplaints('returnComplaintsbyCustomer');
        $('.section-switch button').click(function() {
            $('.section-switch button').removeClass('active');
            $(this).addClass('active');
            getComplaints($(this).attr('id'));
        });
    });
    //view complaints
    $(document).on('click', '#view-button', function() {
        var complaintId = $(this).closest('.complaint').attr('data-id');
        $.ajax({
            headers:{
                'Authorization' : 'Bearer' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/complaints/returnComplaintbyCustomer/' + complaintId,
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

                getComplaints('myComplaints');



                
            },
            error: function(err) {
                console.log(err);
            }
        });
    });
    
</script>

<div class="modal" id="complaint-view-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        
        <div id="complaint-data">   </div>
    </div>
</div>


<?php
require_once('../app/views/layout/footer.php');
?>