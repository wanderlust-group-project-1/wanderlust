<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/navbar/rental-navbar.php');

?>

<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>


    <div class="guide-dash-main">
        <h1 class="title mb-2">Complaints</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Complaints</a></li>
        </ul>




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
                // if order-list-content in document remove it
                if ($('#order-list-content').length) {
                    $('#order-list-content').remove();
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


</script>





<?php
require_once('../app/views/layout/footer.php');
?>
