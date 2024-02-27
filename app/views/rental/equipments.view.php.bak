<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/components/navbar-rental.php');

// require_once('../app/views/components/navbar.php');
?>

<!-- <link rel="stylesheet" type="text/css" href="<?=ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">

<?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

<div class="sidebar-flow"></div>

<div class="dashboard-content">


<div class="equipment">

    <h2>Equipments</h2>

    <!-- Add Equipment -->
    <div class="add-equipment">
        <button type="submit" class="add-equipment-button"  id="add-equipment">
            Add Equipment
        </button>
    </div>


    <div class="equipment-list">
        


    </div>

        </div>

 </div>

</div>



<!-- Add Equipment Modal -->
<div class="add-equipment-modal" id="add-equipment-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="add-equipment-form" enctype="multipart/form-data">
            <h2>Add New Equipment</h2>

            <label for="equipment-name">Equipment Name</label>
            <input type="text" id="equipment-name" name="equipment_name" required>

            <label for="equipment-type">Type</label>
            <input type="text" id="equipment-type" name="equipment_type" required>

            <label for="cost">Cost</label>
            <input type="number" step="0.01" id="cost" name="cost" required>

            <label for="rental-fee">Rental Fee</label>
            <input type="number" step="0.01" id="rental-fee" name="rental_fee" required>

            <label for="description">Description</label>
            <input type="text" id="description" name="description" required>

            <label for="count">Count</label>
            <input type="number" id="count" name="count" required>

            <label for="fee">Fee</label>
            <input type="number" step="0.01" id="fee" name="fee" required>

            <label for="equipment-image">Equipment Image</label>
            <input type="file" id="equipment-image" name="equipment_image" required>

            <input type="submit" value="Add Equipment">
        </form>
    </div>
</div>

<!-- Modal Box Add Equipment End -->





<script> 
var addEquipmentModal = document.getElementById("add-equipment-modal");
var addEquipmentBtn = document.getElementById("add-equipment");
var span = document.getElementsByClassName("close")[0]; // assuming this is the first modal

addEquipmentBtn.onclick = function() {
    addEquipmentModal.style.display = "block";
}

span.onclick = function() {
    addEquipmentModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == addEquipmentModal) {
        addEquipmentModal.style.display = "none";
    }
}

</script>




<script>
$(document).ready(function() {
    $("#add-equipment-form").submit(function(e) {
        e.preventDefault();

        var formData = new FormData();
        
        // Create JSON object from form fields
        var jsonData = {
            name: $("#equipment-name").val(),
            type: $("#equipment-type").val(),
            cost: parseFloat($("#cost").val()), // Assuming rent fee is the cost
            fee: parseFloat($("#rental-fee").val()),
            description: $("#description").val(), // Assuming condition is the description
            count: parseInt($("#count").val()),
            
        };

        var image = $("#equipment-image").prop('files')[0];
        // var filesData = {
        //     image: image
        // }
        // Append JSON data to formData
        formData.append('json', JSON.stringify(jsonData));
        formData.append('image', image);

        // const api = new ApiClient('api/equipment/addEquipment')
        // api.uploadImageWithJSON('',image,jsonData)
        // .then(response => {
        //     console.log(response);
        //     if(response.status == 200) {
        //         alert('Equipment added successfully');
        //         window.location.reload();
        //     }
        // })
        console.log(jsonData)
        console.log(formData);

        $.ajax({
        //    with authorization
           headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/equipment/addEquipment',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // console.log(response);
                if(response.success) {
                    alertmsg('Equipment added successfully', 'success');
                    
                    // close
                    addEquipmentModal.style.display = "none";
                }
            }
        });

    });
});


// read cookie and get jwt_auth_token



// console.log(getCookie('jwt_auth_token'));

</script>

<script>
    // get equipment list using ajax , get content and append to equipment list div

    function getEquipments(){
        // use Authorization header to get data

        $.ajax({
    url: '<?= ROOT_DIR ?>/rentalService/getequipments',
    type: 'GET',
    headers: {
        'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
    },
    success: function(data) {
        // console.log(data);
        // Update the modal content with the fetched data
        $(".equipment-list").html(data).promise().done(function() {
            console.log('equipment list updated');
            viewEquipment();
        });
    },
    error: function(xhr, status, error) {
        console.error("Error fetching data: " + error);
        // Handle errors here
    }
});







    }

    getEquipments();


</script>

