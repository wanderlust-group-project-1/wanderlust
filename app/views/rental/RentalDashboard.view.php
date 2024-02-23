<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/navbar/rental-navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Dashboard</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Dashboard</a></li>
        </ul>
        <div class="info-data mt-5">
            <div class="card">
                <div class="head">
                    <div>
                        <h2>100</h2>
                        <p>Rents</p>
                    </div>
                </div>
                <span class="progress" data-value="10%"></span>
                <span class="label">10 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>Rs.139000</h2>
                        <p>Total Earning</p>
                    </div>
                </div>
                <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span>
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h2>35</h2>
                        <p>Equipment Count</p>
                    </div>
                </div>
                <!-- <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span> -->
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h2>1st March</h2>
                        <p>Upcoming Booking</p>
                    </div>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>Micheal Julius</p>
                </div>
                <!-- <span class="progress" data-value="30%"></span>
                <span class="label">30%</span> -->
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>21st February</h2>
                        <p>Recent Booking</p>
                    </div>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2 ml-2">
                    <p>Julius John</p>
                </div>
                <!-- <span class="progress" data-value="80%"></span>
                <span class="label">80%</span> -->
            </div>
        </div>

        <div class="dashboard-card">

            <div class="equipment p-4">

                <div class="row justify-content-between">
                    <h1 class="title">Equipments</h1>

                    <!-- Add Equipment -->
                    <div class="add-equipment">
                        <button type="submit" class="btn" id="add-equipment">
                            Add Equipment
                        </button>
                    </div>
                </div>


                <div class="equipment-list">



                </div>

            </div>

        </div>
    </div>

        <!-- Modal Box Profile Edit -->
        <div class="profile-editor" id="profile-editor">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div class="profile-info">
                    <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="Profile Image" class="profile-image">


                    <form id="rentalservice" action="<?= ROOT_DIR ?>/rentalService/update" method="post">
                        <h2>Update Rental Service Details</h2>
                        <?php if (isset($errors)) : ?>
                            <div> <?= implode('<br>', $errors) ?> </div>
                        <?php endif; ?>

                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" value="<?= $user->name ?>" required>

                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" value="<?= $user->address ?>" required>

                        <!-- <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?= $user->email ?>" required> -->

                        <label for="mobile">Mobile No</label>
                        <input type="text" name="mobile" id="mobile" value="<?= $user->mobile ?>" required>

                        <label for="regNo">Registration Number</label>
                        <input type="text" name="regNo" id="regNo" value="<?= $user->regNo ?>" required>

                        <!-- <label for="password">Password</label>
    <input type="password" name="password" id="password" required> -->

                        <input type="submit" class="btn mt-4" name="submit" value="Update">
                    </form>



                </div>
            </div>
        </div>
    </div>

    <!-- Modal Box Profile Edit End -->

    <!-- Modal Box Add Equipment -->
    <!-- Add Equipment Modal -->
    <div class="add-equipment-modal" id="add-equipment-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="add-equipment-form" class="flex-d " enctype="multipart/form-data">
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

                        




            <!-- Standard fee -->
            <label for="standard-fee">Standard Fee</label>
            <input type="number" step="0.01" id="standard-fee" class="form-control-lg" name="standard_fee" required>


            <label for="rental-fee">Rental Fee (per day)</label>
            <input type="number" step="0.01" id="rental-fee" class="form-control-lg" name="rental_fee" required>

                        <label for="count">Quantity</label>
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


</div>



</script>
<script>
    var modal = document.getElementById("profile-editor");

    var span = document.getElementsByClassName("close")[0];

    // Get all view buttons
    var viewButton = document.getElementById('edit-profile');

    // Function to handle modal display
    function openModal() {
        // document.getElementById("modal-content").innerHTML = content;
        modal.style.display = "block";
    }

    // Add click event listener to view buttons
    viewButton.addEventListener('click', function() {

        // var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
        // var email = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
        openModal();
    });


    // Close the modal when the close button is clicked
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>


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
            standard_fee: parseFloat($("#standard-fee").val()),
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

                    // refresh equipment list
                    getEquipments();

                    // clear form
                    $("#add-equipment-form").trigger('reset');
                    

                }
            },
            });


        });
    });


    // read cookie and get jwt_auth_token



    // console.log(getCookie('jwt_auth_token'));
</script>

<script>
    // get equipment list using ajax , get content and append to equipment list div

    function getEquipments() {
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

<script>

    $(document).on('click', '.close', function() {
        var modal = $(this).closest('.modal');
        modal.hide();
    });
    // close or modal-close both 

    $(document).on('click', '.modal-close', function() {
        var modal = $(this).closest('.modal');
        modal.hide();
    });

    </script>


<?php
require_once('../app/views/layout/footer.php');

?>