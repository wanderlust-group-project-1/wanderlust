<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/admin/components/navbar.php');

// require_once('../app/views/admin/layout/sidebar.php');

?>

<div class="dashboard">
    <?php require_once('../app/views/admin/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Guides</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Guides</a></li>
        </ul>


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
                    // Assuming guides is an array of data
                    foreach ($guides as $guide) {
                    ?>
                        <tr key="<?php echo $guide->id ?>">
                            <td><?php echo $guide->name; ?></td>
                            <td><?php echo $guide->mobile; ?></td>
                            <td><span class="status <?php echo $guide->status; ?>"><?php echo $guide->status; ?></span></td>
                            <td><button class="btn-text-green" id="view-button"> <i class="fa fa-list"> </i> View</button></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <!-- Modal  -->
        <div class="modal" id="guides-modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="user">
                    <div class="profile-info">
                        <img src="<?php echo ROOT_DIR ?>/assets/images/dp.jpg" alt="Profile Image" class="profile-image">
                        <h2 id="profile-name">Sandali </h2>
                        <p id="profile-email">sandali@gmail.com</p>
                        <p id="profile-address">Maharagama</p>
                        <p id="profile-status" class="accepted">Accepted</p>
                        <div class="profile-links">
                            <a href="#" id="link-1">Link 1</a>
                            <a href="#" id="link-2">Link 2</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <script>
            // Get the modal
            var modal = document.getElementById("guides-modal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // Get all view buttons
            var viewButtons = document.querySelectorAll('#view-button');

            // Function to handle modal display
            function openModal(id) {
                // document.getElementById("modal-content").innerHTML = content;

                modal.style.display = "block";
                $.get(`<?php echo ROOT_DIR ?>/admin/guides/viewUser/${id}`, function(data) {
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


                    var key = this.parentElement.parentElement.getAttribute('key');

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
    </div>