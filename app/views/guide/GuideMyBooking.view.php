<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="GuideMyBooking.css" rel="stylesheet">

</head>

<body>
    <div class="guide-booking">
        <div class="CalendarView">
            <img class="image" src="<?php echo ROOT_DIR?>/assets/images/6.png" />
            <button id=updateCal class="small-button">Update</button>
        </div>

        <script>
            document.getElementById("updateCal").addEventListener("click", function () {
                window.location.href = "calendar.html";
            });
        </script>

        <div class="frame2">
            <div class="yellow-card">
                <div class="upper-card-text">
                    <div class="text-card-topic">Upcoming Tour</div>
                    <div class="edit-prof-button">
                        <button type="submit" class="small-button-middle">
                            More &gt
                        </button>
                    </div>
                </div>
                <div class="number-card">Oct 02</div>
            </div>

            <div class="yellow-card">
                <div class="upper-card-text">
                    <div class="text-card-topic">Upcoming Tour</div>
                    <div class="edit-prof-button">
                        <button type="submit" class="small-button-middle">
                            More &gt
                        </button>
                    </div>
                </div>
                <div class="number-card">Sep 25</div>
            </div>

            <div class="yellow-card">
                <div class="upper-card-text">
                    <div class="text-card-topic">First Tour</div>
                    <div class="edit-prof-button">
                        <button type="submit" class="small-button-middle">
                            More &gt
                        </button>
                    </div>
                </div>
                <div class="number-card">June 22</div>
            </div>
        </div>

        <div class="frame2">
            <div class="sec2">
                <div class="uppersec2">
                    <div class="text-topic">Upcoming Tours</div>
                    <button class="small-button">Done</button>
                </div>

                <div class="sec3-booking">
                    <div class="sec3-booking-main">
                        <div class="img-2">
                            <img src="<?php echo ROOT_DIR?>/assets/images/2.png" alt="">
                        </div>
                    </div>

                    <div class="formSet">
                        <div class="formContent">
                            <div class="formText">Place :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Customer :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Date :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Time :</div>
                        </div>
                    </div>

                </div>
       
                <div class="uppersec2">
                    <div class="text-topic">Notes</div>
                    <button class="small-button">Add Notes</button>
                </div>

        <div class="whiteBox">
        </div>

        <div class="frame2">
            <div class="sec2">
                <div class="uppersec2">
                    <div class="text-topic">Previous Tours</div>
                    <button class="small-button">Done</button>
                </div>

                <div class="sec3-booking">
                    <div class="sec3-booking-main">
                        <div class="img-2">
                            <img src="<?php echo ROOT_DIR?>/assets/images/2.png" alt="">
                        </div>
                    </div>

                    <div class="formSet">
                        <div class="formContent">
                            <div class="formText">Place :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Customer :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Date :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Time :</div>
                        </div>
                    </div>

                </div>
       
                <div class="uppersec2">
                    <div class="text-topic">Notes</div>
                    <button class="small-button">Add Notes</button>
                </div>

        <div class="whiteBox">
        </div>
        
    </div>
    </div>

    </div>

</body>

</html>

<!-- Modal Box Add Equipment -->
<!-- Add Equipment Modal -->
<div class="add-equipment-modal" id="add-equipment-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="add-equipment-form"  class="flex-d "  enctype="multipart/form-data">
            <h2>Add New Equipment</h2>

            <div class="row align-items-start">
            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">

            <label for="equipment-name">Equipment Name</label>
            <input type="text" id="equipment-name" class="form-control-lg" name="equipment_name" required>

            <!-- <label for="equipment-type">Type</label>
            <input type="text" id="equipment-type" class="form-control-lg" name="equipment_type" required> -->
            <label for="equipment-type">Type</label>
            <select id="equipment-type" class="form-control-lg" name="equipment_type" required>
                <option value="Tent">Tent</option>
                <option value="Cooking">Cooking</option>
                <option value="Backpack">Backpack</option>
                <option value="Sleeping">Sleeping</option>
                <option value="Climbing">Climbing</option>
                <option value="Clothing">Clothing</option>
                <option value="Footwear">Footwear</option>
                <option value="Other">Other</option>


                
            </select>

            
            <label for="description">Description</label>
            <!-- <input type="text" id="description" class="form-control-lg" name="description" required> -->
            <textarea id="description" class="form-control-lg" name="description" required></textarea>

            </div>
            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
           
            <label for="cost">Cost</label>
            <input type="number" step="0.01" id="cost" class="form-control-lg" name="cost" required>

            <label for="rental-fee">Rental Fee</label>
            <input type="number" step="0.01" id="rental-fee" class="form-control-lg" name="rental_fee" required>



            <label for="count">Count</label>
            <input type="number" id="count" class="form-control-lg" name="count" required>

       
            <label for="equipment-image">Equipment Image</label>
            <input type="file" id="equipment-image" class="form-control-lg" name="equipment_image" required>


            </div>
                    </div>
            <div class="row">
            <input type="submit" class="btn" value="Add Equipment">
            </div>
        </form>
    </div>
</div>

<!-- Modal Box Add Equipment End -->



<script> 
var addEquipmentModal = document.getElementById("add-equipment-modal");
var addEquipmentBtn = document.getElementById("add-equipment");
var span = document.getElementsByClassName("close")[1]; // assuming this is the second modal

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
                console.log(response);
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



