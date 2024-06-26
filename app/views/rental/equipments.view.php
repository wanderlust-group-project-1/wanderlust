<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/navbar/rental-navbar.php');

?>




<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Equipments</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Equipments</a></li>
        </ul>


       



        <div class="dashboard-card mt-5">

            <div class="equipment p-4">

                <div class="row justify-content-between">
                    <h3 class="guide-topics">Equipment Details</h3> 






                    <div class="section-switch flex-d  gap-3 flex-wrap" >
                        <button class="btn-selected active" id="available">Available</button>
                        <button class="btn-selected" id="unavailable">Unavailable</button>
                     
                     

                    </div>









                    <!-- <div class="add-equipment">
                        <button type="submit" class="btn-icon" id="add-equipment">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Add new
                        </button>
                    </div> -->
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
    <div class="add-equipment-modal modal" id="add-equipment-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="add-equipment-form flex-d-c  text-center" class="flex-d " enctype="multipart/form-data">
                <h2 class="text-center">Add New Equipment</h2>

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
                        <input type="number" step="0.01" id="cost" class="form-control-lg" name="cost" required min="0" max="200000">

                        <!-- Standard fee -->
                        <label for="standard-fee">Standard Fee</label>
                        <input type="number" step="0.01" id="standard-fee" class="form-control-lg" name="standard_fee" required min="0" max="20000">


                        <label for="rental-fee">Rental Fee (per day)</label>
                        <!-- add max value -->
                        <input type="number" step="0.01" id="rental-fee" class="form-control-lg" name="rental_fee" required min="0" max="10000">

                        <label for="count">Quantity</label>
                        <input type="number" id="quantity" class="form-control-lg" name="count" required min="0" max="1000">

                        <label for="equipment-image1">Equipment Image</label>
                        <input type="file" id="equipment-image" class="form-control-lg" name="equipment_image" hidden>
                        <button type="button" class="btn-text-green border" id="equipment-image-upload-button">Upload Image</button>
                        

                    </div>
                </div>
                <div class="row">
                    <input id="add-equipment-form-submit" type="submit" class="btn-text-green border" value="Add Equipment">
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Box Add Equipment End -->
</div>

<script>
    // Open image upload modal, use jquery
    $(document).on('click', '#equipment-image-upload-button', function() {
        $('#equipment-image-upload').show();
    });

  
    
    </script>


<!-- Equipment Image Upload Modal -->
<div class="modal" id="equipment-image-upload">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="flex-d-c text-center">
            
        <h2>Upload Equipment Images</h2>
        <!-- Equipment image upload form -->
        <form method="post" class="flex-d-c text-center justify-content-center align-items-center" enctype="multipart/form-data">
            <div class="flex-d mt-3 ">
            <label for="equipment-image-input" class="btn-text-green border"> <i class="fa fa-file"></i> Select Images</label>
            <input type="file" name="equipment_images[]" id="equipment-image-input" class="form-control-lg hidden" accept="image/png, image/jpg, image/gif, image/jpeg , image/webp" required >
            </div>
            <div class="equipment-image-preview-container flex-d mt-2  align-items-center" id="equipment-image-preview">

            </div>
            <input type="submit" class="btn-text-green border mt-4" name="submit" value="Upload" id="equipment-image-submit">
        </form>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    $('#equipment-image').hide();
    $('#equipment-image-input').change(function() {
        var files = this.files;
        var previewContainer = $('#equipment-image-preview');
        previewContainer.empty(); // Clear existing previews

        Array.from(files).forEach((file, index) => {
            var imgContainer = $('<div/>', { 'class': 'img-preview-item' });
            var img = $('<img>', { 'class': 'image-preview' }).appendTo(imgContainer);

            // validate file type
            var fileType = file['type'];
            var validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            if (!validImageTypes.includes(fileType)) {
                alertmsg('Invalid file type. Please select an image file' , 'error');
                // clear 
                $('#equipment-image-input').val('');
                return;
            }


            var removeButton = $('<button/>', {
                text: '',
                'class': 'icon remove-icon',
                click: function() {
                    // Remove this image preview
                    imgContainer.remove();
                    // Optionally, handle file list updates if necessary
                }

            })
            var removeIcon = $('<i/>', { 'class': 'fa fa-times'}).appendTo(removeButton);
            removeButton.appendTo(imgContainer);


            previewContainer.append(imgContainer);

            var reader = new FileReader();
            reader.onload = function(e) {
                img.attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });
    });
});

// equipment-image-submit
$(document).on('click', '#equipment-image-submit', function(e) {
    e.preventDefault();
    $('#equipment-image-upload').hide();
    // $('#equipment-image').val($('#equipment-image-input').prop('files'));
    // equipment-image-input to equipment-image
    $('#equipment-image').prop('files', $('#equipment-image-input').prop('files'));

    


});


</script>







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

    // addEquipmentBtn.onclick = function() {
        $(document).on('click', '#add-equipment', function(e) {           
            addEquipmentModal.style.display = "block";
        });
        

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
    $("#add-equipment-form").trigger('reset');
    // $("#add-equipment-form").submit(function(e) {
        // $(document).on('submit', '#add-equipment-form', function(e) {
            $('#add-equipment-form-submit').click(function(e) {
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
            count: parseInt($("#quantity").val()),
            
        };

        console.log(jsonData);

        // validate form fields

        if (jsonData.name == '' || jsonData.type == '' || jsonData.cost == '' || jsonData.standard_fee == '' || jsonData.fee == '' || jsonData.description == '' || jsonData.count == '') {
            alertmsg('Please fill all fields', 'error');
            return;
        }

        // name contain only letters and numbers and  - () _ . , and length is 3
        nameRegex  = /^[a-zA-Z0-9-()_., ]{3,}$/;
        if (!nameRegex.test(jsonData.name)) {
            alertmsg('Name should contain only letters, numbers, -()_,. and length should be atleast 3', 'error');
            return;
        }

        // cost between 0 and 200000
        if (jsonData.cost < 0 || jsonData.cost > 200000) {
            alertmsg('Cost should be between 0 and 200000', 'error');
            return;
        }

        // standard fee between 0 and 20000
        if (jsonData.standard_fee < 0 || jsonData.standard_fee > 20000) {
            alertmsg('Standard fee should be between 0 and 20000', 'error');
            return;
        }

        // rental fee between 0 and 10000
        if (jsonData.fee < 0 || jsonData.fee > 10000) {
            alertmsg('Rental fee should be between 0 and 10000', 'error');
            return;
        }

        // count 
        if (jsonData.count < 0 || jsonData.count > 1000) {
            alertmsg('Count should be between 0 and 1000', 'error');
            return;
        }
         

        


        // var image = $("#equipment-image").prop('files')[0];

        //  check if image is empty of the file is not valid
        if ($("#equipment-image").prop('files').length > 0) {
            var image = $("#equipment-image").prop('files')[0];
        } else {
            alertmsg('Please select an image', 'error');
            return;
        }
        //  if($("#equipment-image-input").prop('files').length > 0) {
        //     var image = $("#equipment-image-input").prop('files');
        // } else {
        //     alertmsg('Please select an image', 'error');
        // }


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
                    
                    $("#add-equipment-form").trigger('reset');
                    // another way to reset form
                    // document.getElementById("add-equipment-form").reset();
                    // not working 
                    // $("#add-equipment-form").reset();
                    // reset not a function


                    // reset 
                    $("#equipment-name").val('');
                    $("#cost").val('');
                    $("#standard-fee").val('');
                    $("#rental-fee").val('');
                    $("#description").val('');
                    $("#quantity").val('');
                    






                    // close

                    setTimeout(() => {
                    addEquipmentModal.style.display = "none";
                }, 1000);

                    // refresh equipment list
                    getEquipments();

                    // clear form
                    

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



    $(document).ready(function() {

            $(document).on('click', '#available', function() {
            $('#available').addClass('active');
            $('#unavailable').removeClass('active');
            getEquipments();
        });

        $(document).on('click', '#unavailable', function() {
            $('#unavailable').addClass('active');
            $('#available').removeClass('active');
            getEquipments('unavailable');
        });

    });




    function getEquipments(status = 'available') {
        // use Authorization header to get data


        $.ajax({
            url: '<?= ROOT_DIR ?>/rentalService/getequipments/' + status,
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
    // $('#show-filter').click(function() {(
        $(document).on('click', '#show-filter', function() {
        $('.table-filter').slideDown();
        $('#show-filter').hide();
    });

    // $('#hide-filter').click(function() {
        $(document).on('click', '#hide-filter', function() {
        $('.table-filter').slideUp();
        $('#show-filter').show();
    });



    $(document).on('input', '#equipment-name-filter', function() {
        filterEquipment();
    });

    $(document).on('change', '#equipment-type-filter', function() {
        filterEquipment();
    });

    $(document).on('input', '#equipment-cost-filter-min', function() {
        filterEquipment();
    });

    $(document).on('input', '#equipment-cost-filter-max', function() {
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


            // validate form fields

            if (jsonData.name == '' || jsonData.type == '' || jsonData.cost == '' || jsonData.standard_fee == '' || jsonData.rental_fee == '' || jsonData.description == '') {
                alertmsg('Please fill all fields', 'error');
                return;
            }

            // name contain only letters and numbers and  - () _ . , and length is 3
            nameRegex  = /^[a-zA-Z0-9-()_., ]{3,}$/;
            if (!nameRegex.test(jsonData.name)) {
                alertmsg('Name should contain only letters, numbers, -()_,. and length should be atleast 3', 'error');
                return;
            }

            // cost between 0 and 200000
            if (jsonData.cost < 0 || jsonData.cost > 200000) {
                alertmsg('Cost should be between 0 and 200000', 'error');
                return;
            }

            // standard fee between 0 and 20000
            if (jsonData.standard_fee < 0 || jsonData.standard_fee > 20000) {
                alertmsg('Standard fee should be between 0 and 20000', 'error');
                return;
            }


            // rental fee between 0 and 10000

            if (jsonData.rental_fee < 0 || jsonData.rental_fee > 10000) {
                alertmsg('Rental fee should be between 0 and 10000', 'error');
                return;
            }

            //  count between 0 and 1000

         




            console.log("json data", jsonData);

            // if image is not empty then append it to formdata

            formData.append('json', JSON.stringify(jsonData));






            if (formData.get('equipment_image').name != '') {

                console.log(formData.get('equipment_image'));

                console.log(formData.get('equipment_image').name);
                var image = formData.get('equipment_image');

                // validate file type
                var fileType = image['type'];
                var validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!validImageTypes.includes(fileType)) {
                    alertmsg('Invalid file type. Please select an image file' , 'error');
                    return;
                }

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
                    alertmsg('Equipment updated successfully', 'success');
                    // location.reload();
                },
                error: function(data) {
                    console.log(data);
                    alertmsg('Equipment could not be updated', 'error');
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
                type: 'POST',
                success: function(response) {
                    console.log(response);
                    alertmsg('Equipment deleted successfully', 'success');
                    getEquipments();
                },
                error: function(err) {
                    console.log(err);
                    // alertmsg('Equipment could not be deleted', 'error');
                    alertmsg(err.responseJSON.message, 'error');

                    // close modal
                    $("#delete-equipment-modal").hide();
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
            // $("#make-unavailable-p").attr('disabled', true);
            $("#make-unavailable-p").hide();
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
            // $("#make-unavailable-p").attr('disabled', true);
            $("#make-unavailable-p").hide();
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


// Disable modal open
$(document).on('click', '#disable-equipment-button', function() {
    var id = $(this).data('id');
    console.log(id);
    $('#disable-equipment-modal').show();
    $('#disable-equipment').attr('data-id', id);
});


// Disable 
$(document).on('click', '#disable-equipment', function() {
    var id = $(this).data('id');
    console.log(id);
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/api/equipment/disableEquipment/' + id,
        method: 'POST',
        success: function(data) {
            console.log(data);
            alertmsg('Equipment disabled successfully', 'success');
            getEquipments();
        },
        error: function(data) {
            console.log(data);
            alertmsg('Equipment could not be disabled', 'error');
        }
    })
});







        

        </script>


<?php
require_once('../app/views/layout/footer.php');

?>