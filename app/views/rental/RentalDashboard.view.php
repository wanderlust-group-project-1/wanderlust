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
                        <h3>100</h3>
                        <p>Rents</p>
                    </div>
                </div>
                <span class="progress" data-value="10%"></span>
                <span class="label">10 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h3>Rs.139000</h3>
                        <p>Total Earning</p>
                    </div>
                </div>
                <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span>
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h3>35</h3>
                        <p>Equipment Quantity</p>
                    </div>
                </div>
                <!-- <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span> -->
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h3>1st March</h3>
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
                        <h3>21st February</h3>
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
                    <h1 class="title">Equipment Details</h1> 

                    <div class="add-equipment">
                        <button type="submit" class="btn-icon" id="add-equipment">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Add new
                        </button>
                    </div>
                </div>

                <!-- Add Equipment -->
                

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
                        <h2>Update Profile</h2>
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

// form reset document ready


$(document).ready(function() {
    $("#add-equipment-form").trigger('reset');
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


        // var image = $("#equipment-image").prop('files')[0];

        //  check if image is empty of the file is not valid
        if ($("#equipment-image").prop('files').length > 0) {
            var image = $("#equipment-image").prop('files')[0];
        } else {
            alertmsg('Please select an image', 'error');
        }

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
                // empty the equipment list and append new data
                $(".equipment-list").empty();
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


    </script>




<script>
    function viewEquipment(){
        console.log('view equipment');

    var modal = document.getElementById("view-equipment-modal");
    var closeButton = document.querySelector(".close-button");

    closeButton.addEventListener("click", function() {
        modal.style.display = "none";
    });

    var viewButtons = document.querySelectorAll("#equipment-view-button");
    // console.log("a",viewButtons);
    viewButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            console.log('view button clicked');
            var row = button.closest('tr');

            var id = row.getAttribute('data-id');


            fetchEquipmentDetails(id);


            
            


            modal.style.display = "block";
        });
    });

    }

    function fetchEquipmentDetails(equipmentId) {
    $.ajax({
        headers: {
            Authorization: "Bearer " + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/rentalService/getequipment/' + equipmentId,
        method: 'GET',
        success: function(data) {
            // console.log(data);

            // // Create a new div element
            // var newDiv = document.createElement("div");
            // newDiv.innerHTML = data;
            // var js = newDiv.querySelector('script').innerHTML;

            // Update the modal content and execute the script
            $('#equipment-modal-content').empty();
            $('#equipment-modal-content').html(data).promise().done(function() {
                console.log('equipment loaded');
                // viewEquipment();
                // eval(js);
            });
        },
        error: function(err) {
            console.log(err);
        }
    });
}


// filter equipment

 $(document).ready(function() {
    $('#show-filter').click(function() {
        $('.table-filter').slideDown();
        $('#show-filter').hide();
    });

    $('#hide-filter').click(function() {
        $('.table-filter').slideUp();
        $('#show-filter').show();
    });

    // client side filter (onchange)

    $('#equipment-name-filter').on('input', debounce(filterEquipment, 300));

    $('#equipment-type-filter').change(function() {
        filterEquipment();
    });

    $('#equipment-cost-filter-min').on('input', function() {
        filterEquipment();
    });

    $('#equipment-cost-filter-max').on('input', function() {
        filterEquipment();
    });




    // $('#equipment-filter-button').click(function() {
        function filterEquipment() {
        var name = $('#equipment-name-filter').val();
        var type = $('#equipment-type-filter').val();
        // var minCost = $('#equipment-cost-filter-min').val();
        // var maxCost = $('#equipment-cost-filter-max').val();

        // console.log(name, type, minCost, maxCost);

     
        $('#equipment-table tbody tr').each(function() {
            var row = $(this);
            var equipmentName = row.find('td').eq(0).text();
            var equipmentType = row.find('td').eq(1).text();
            // var equipmentCost = row.find('td').eq(2).text().replace('Rs', '');
            var equipmentCount = row.find('td').eq(3).text();

            // console.log(equipmentName, equipmentType, equipmentCost, equipmentCount);

            if (name && equipmentName.toLowerCase().indexOf(name.toLowerCase()) === -1) {
                row.hide();
            } else if (type && equipmentType.toLowerCase().indexOf(type.toLowerCase()) === -1) {
                row.hide();
            } else {
                row.show();
            }
        });
    }
});






</script>


<!-- ############### -->



<script>
        // var deleteButtons = document.querySelectorAll("#delete-equipment-button");

        // deleteButtons.forEach(function(button) {
        //     button.addEventListener("click", function() {
        //         var modal = document.getElementById("delete-equipment-modal");
        //         console.log("modal");
        //         modal.style.display = "block";
        //     });

        // });

        $(document).on('click', '#delete-equipment-button', function() {
            var modal = document.getElementById("delete-equipment-modal");
            modal.style.display = "block";
            var id = $(this).attr('data-id');
            console.log("id", id);
            $("#delete-equipment").attr('data-id', id);
        });

    

        // var editButtons = document.querySelectorAll("#edit-equipment-button");

        // editButtons.forEach(function(button) {
        //     button.addEventListener("click", function() {
        //         var modal = document.getElementById("edit-equipment-modal");
        //         console.log("modal");
        //         modal.style.display = "block";
        //     });

        // });

        $(document).on('click', '#edit-equipment-button', function() {
            var modal = document.getElementById("edit-equipment-modal");
            modal.style.display = "block";
            var id = $(this).attr('data-id');
            console.log("id", id);
            $("#update-equipment-form").attr('data-id', id);
            // fetchEquipmentDetails(id);
        });


        // update-equipment , use jquery to json , prevent default

        // $("#update-equipment-form").submit(function(e) {
            $(document).on('submit', '#update-equipment-form', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            var id = $(this).attr('itemid');

            var jsonData = {
                id: id,
                name: formData.get('equipment_name'),
                type: formData.get('equipment_type'),
                description: formData.get('description'),
                cost: formData.get('cost'),
                standard_fee: formData.get('standard_fee'),
                rental_fee: formData.get('rental_fee'),
                // count: formData.get('count'),
            };

            console.log("json data", jsonData);

            // if image is not empty then append it to formdata

            formData.append('json', JSON.stringify(jsonData));

            if (formData.get('equipment_image') != '') {
                var image = formData.get('equipment_image');

                formData.append('image', image);


            }

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },

                url: '<?= ROOT_DIR ?>/api/equipment/update/' + id,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    // console.log(data);
                    fetchEquipmentDetails(id);
                    // location.reload();
                },
                error: function(data) {
                    console.log(data);
                }

            })



            })


        // $("#delete-equipment").click(function() {
            $(document).on('click', '#delete-equipment', function() {
            // var id = $("#update-equipment-form").attr('itemid');

            var id = $(this).attr('data-id');

            console.log("delete equipment", id);


            console.log("delete equipment", id);
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/equipment/delete/' + id,
                method: 'POST',
                success: function(data) {
                    console.log(data);
                    alertmsg('Equipment deleted successfully', 'success');
                    getEquipments();
                },
                error: function(data) {
                    console.log(data);
                    alertmsg('Equipment could not be deleted', 'error');
                }
            })
        });


            
        // increase count modal , jquery

        // $("#increase-count-button").click(function() {
            $(document).on('click', '#increase-count-button', function() {
            var modal = document.getElementById("increase-count-modal");
            modal.style.display = "block";
            var id = $(this).attr('data-id');
            console.log("id", id);
            $("#increase-count-form").attr('data-id', id);


        });

        

        // $("#increase-count-form").submit(function(e) {
            // $("#increase-count").click(function(e) {
                $(document).on('click', '#increase-count', function(e) {
                // disable button

                $(this).prop('disabled', true);

            e.preventDefault();

            


            var id = $("#increase-count-form").attr('data-id');
            var count = $("#count").val();

            console.log("count", count);
            console.log(id)
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/equipment/increasecount/' + id,
                method: 'POST',
                // data: {
                //     count: count
                // },
                    //  send as json
                contentType: 'application/json', // Indicate that we're sending JSON data
                data: JSON.stringify({
                    count: count 
                }),

                success: function(data) {
                    console.log(data);
                    alertmsg('Count increased successfully', 'success');
                    getEquipments();
                },
                error: function(data) {
                    console.log(data);
                    alertmsg('Count could not be increased', 'error');
                }
            })
        });

        // calculate total , only accept positive numbers

        // $("#count").on("input", function() {
            $(document).on('input', '#count', function() {
            var count = $(this).val();
            if (count < 0 || isNaN(count) || count == ''){
                $(this).val(0);
                count = 0;
            }
            // str to int
            // Hard coded value


            var total = parseInt($("#current-count").val()) + parseInt(count);

            $("#total").val(total);
        });





        // Manage Items Modal

        // $("#manage-items-button").click(function() {
            $(document).on('click', '#manage-items-button', function() {
            var modal = document.getElementById("manage-items-modal");
            modal.style.display = "block";
            var id = $(this).attr('data-id');
            console.log("id", id);

            fetchItems(id);



        });

    function fetchItems(id) {
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/rentalService/getItems/' + id,
                method: 'GET',
                success: function(data) {
                    $("#manage-items-content").empty();
                    $("#manage-items-content").html(data);
                },
                error: function(data) {
                    console.log(data);
                }
            });
}



        // item table actions

$(document).on('click', '#equipment-item', function() {
    var id = $(this).data('id');
    var status = $(this).data('status');
    var number = $(this).data('number');
    var count = $(this).data('count');

    
    console.log(id);
    console.log(status);
    // show modal

    $("#item-number").empty();
    $("#item-number").html(number);


    // if available
    if (status == 'available') {
        // add id to
        $("#make-unavailable-t").attr('data-id', id);
        $("#make-unavailable-t").show();
        $("#make-unavailable-p").attr('data-id', id);
        $("#make-unavailable-p").show();
        $("#make-unavailable-p").attr('disabled', false);

        if(count >0){
            $("#make-unavailable-p").attr('disabled', true);
            // You can't make this item unavailable permanently because it has upcoming bookings.
            $("#make-unavailable-p").attr('data-tooltip', 'You can\'t make this item unavailable temporarily because it has upcoming bookings.');
            
        }
        $("#make-available").hide();
    } else {
        $("#make-unavailable-t").hide();
        $("#make-unavailable-p").attr('data-id', id);
        $("#make-unavailable-p").show();
        $("#make-unavailable-p").attr('disabled', false);
        console.log(count);
        if(count >0){
            $("#make-unavailable-p").attr('disabled', true);
            // You can't make this item unavailable permanently because it has upcoming bookings.
            $("#make-unavailable-p").attr('data-tooltip', 'You can\'t make this item unavailable temporarily because it has upcoming bookings.');
            
        }
        $("#make-available").attr('data-id', id);
        $("#make-available").show();
    }


    $('#change-item-status-modal').show();

    

});

//    item status change APIs

// make unavailable temporarily
$(document).on('click', '#make-unavailable-t', function() {
    var id = $(this).data('id');
    console.log(id);
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/api/item/makeunavailabletemporarily/' + id,
        method: 'POST',
        success: function(data) {
            console.log(data);
            alertmsg('Item made unavailable temporarily', 'success');

            fetchItems(data.data.equipment_id);

        },
        error: function(data) {
            console.log(data);
            alertmsg('Item could not be made unavailable temporarily', 'error');
        }
    })
});

// make unavailable permanently
$(document).on('click', '#make-unavailable-p', function() {
    var id = $(this).data('id');
    console.log(id);
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/api/item/makeunavailablepermanently/' + id,
        method: 'POST',
        success: function(data) {
            console.log(data);
            alertmsg('Item made unavailable permanently', 'success');
            fetchItems(data.data.equipment_id);
        },
        error: function(data) {
            console.log(data);
            alertmsg('Item could not be made unavailable permanently', 'error');
        }
    })
});

// make available
$(document).on('click', '#make-available', function() {
    var id = $(this).data('id');
    console.log(id);
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/api/item/makeavailable/' + id,
        method: 'POST',
        success: function(data) {
            console.log(data);
            alertmsg('Item made available', 'success');
            fetchItems(data.data.equipment_id);
        },
        error: function(data) {
            console.log(data);
            alertmsg('Item could not be made available', 'error');
        }
    })
});



        

        </script>


<?php
require_once('../app/views/layout/footer.php');

?>