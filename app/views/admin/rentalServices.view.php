<?php
require_once('../app/views/admin/layout/header.php');
require_once('../app/views/admin/components/navbar.php');

require_once('../app/views/admin/layout/sidebar.php');

?>

<div class="table-container">

<table class="data-table">
<thead>
            <tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

    <?php
    // Assuming rentalServices is an array of data
    foreach ($rentalServices as $service) {
    ?>
    <tr key="<?php echo $service->id ?>" >
        <td><?php echo $service->name; ?></td>
        <td><?php echo $service->mobile; ?></td>
        <td><span class="status <?php echo $service->status;  ?>"><?php echo $service->status; ?></span></td>
        <td><button class="view-button">View</button></td>
    </tr>
    <?php
    }
    ?>
            </tbody>

</table>


    <!-- <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>John Doe</td>
                <td>john@example.com</td>
                <td><span class="status accepted">Accepted</span></td>
                <td><button class="view-button">View</button></td>
            </tr>
            <tr>
                <td>Jane Smith</td>
                <td>jane@example.com</td>
                <td><span class="status not-accepted">Not Accepted</span></td>
                <td><button class="view-button">View</button></td>
            </tr>
            <tr>
                <td>Bob Johnson</td>
                <td>bob@example.com</td>
                <td><span class="status waiting">Waiting</span></td>
                <td><button class="view-button">View</button></td>
            </tr>
        </tbody>
    </table> -->

</div>

<!-- Modal  -->
<div class="user-modal" id="rental-services-modal">
<div class="modal-content">
        <span class="close">&times;</span>
        <div id="user">
        <!-- <div class="profile-info">
            <img src="<?php echo ROOT_DIR?>/assets/images/dp.jpg" alt="Profile Image" class="profile-image">
            <h2 id="profile-name">Sandali </h2>
            <p id="profile-email">sandali@gmail.com</p>
            <p id="profile-address">Maharagama</p>
            <p id="profile-status" class="accepted">Accepted</p>
            <div class="profile-links">
                <a href="#" id="link-1">Link 1</a>
                <a href="#" id="link-2">Link 2</a>
            </div>
        </div> -->
        </div>
    </div>
</div>


<script>

// Get the modal
var modal = document.getElementById("rental-services-modal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// Get all view buttons
var viewButtons = document.querySelectorAll('.view-button');

// Function to handle modal display
function openModal(id) {
    // document.getElementById("modal-content").innerHTML = content;
    
    modal.style.display = "block";
    $.get(`<?php echo ROOT_DIR?>/admin/rentalServices/viewUser/${id}`, function(data) {
            // Update the modal content with the fetched data
            $("#user").html(data);
        });
    span.onclick = function() {
    modal.style.display = "none";
}
}

// Add click event listener to view buttons
viewButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        
        var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
        var email = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
        var key = this.parentElement.parentElement.getAttribute('key');
        var modalContent = {
            name: name,
            email: email
        }
        openModal(key);
    });
});

// Close the modal when the close button is clicked


// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

</script>




      <?php
require_once('../app/views/admin/layout/footer.php');


?>