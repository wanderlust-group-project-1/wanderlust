
<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/admin/components/navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
<?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Settings</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Settings</a></li>
        </ul>

        <div class="info-data mt-5 flex-d-c ">
            <div class="card">
                <div class="row justify-content-between">
                    

            <!-- Rennting Settings -->
                <div class="card-header">
                    <h3>Renting Settings</h3>
                </div>
                
                <div class="card-body mw-100px">
                    <button class="btn btn-primary" id="renting-settings">Edit</button>
                </div>
                </div>
                
               

            </div>

            <div class="card">
                <div class="row justify-content-between">
                    

            <!-- Rennting Settings -->
                <div class="card-header">
                    <h3>Renting Settings</h3>
                </div>
                
                <div class="card-body mw-100px">
                    <button class="btn btn-primary" id="settings">Edit</button>
                </div>
                </div>
                
               

            </div>
            
        </div>
       


        <!-- Report  -->







    </div>
</div>

<?php
require_once('../app/views/layout/footer.php');

?>
<!-- Include the ApexCharts library -->

<script>
    // jQuery, 
    $(document).ready(function () {
        // Renting Settings
        $('#renting-settings').click(function () {
            $('#renting-settings-modal').css('display', 'block');
        });

        $('.close').click(function () {
            $('#renting-settings-modal').css('display', 'none');
        });

        $('#disable-renting').click(function () {
            // $('#disable-renting').text('Disable');

            

        });

        $('#duration').change(function () {
            $('#duration-save').removeAttr('disabled');
        }); 

    });

   





</script>


<!-- renting Settings -->

<div class="modal" id="renting-settings-modal">
    <div class="modal-content">
        <div class="modal-header">
        <span class="close">&times;</span>

            <h3>Renting Settings</h3>
        </div>
        <!-- Disable further renting -->
        <!-- Duration between two rentals -->

        <div class="modal-body">   
            <!-- Button and change  -->
            <div class="row  justify-content-between">
                <div class="col-lg-6">
                    <h3 for="disable-renting">Disable further renting</h3>
                </div>
                <div class="mw-100px">
                    <button class="btn btn-primary" id="disable-renting">Enable</button>
                </div>
            </div>
            <!-- Duration between two rentals -->
            <div class="row  justify-content-between">
                <div class="col-lg-6">
                    <h3 for="disable-renting">Duration between two rentals</h3>
                </div>
                <div class="mw-200px">
                    <input type="number" id="duration" name="duration" value="1" min="1" max="15">
                </div>
                <button class="btn btn-primary" id="duration-save" disabled>Save</button>
            </div>
            
        </div>
    </div>
</div>

