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
        <h1 class="title mb-2">Customers</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Customers</a></li>
        </ul>


        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Assuming rentalServices is an array of data
                    foreach ($customers as $customer) {
                    ?>
                        <tr key="<?php echo $customer->id ?>">
                            <td><?php echo $customer->name; ?></td>
                            <td><?php echo $customer->number; ?></td>
                            <td><?php echo $customer->address; ?></td>
                            <!-- <td><?php echo $customer->nic; ?></td> -->
                            <!-- <td><span class="status <?php echo $customer->status;  ?>"><?php echo $customer->status; ?></span></td> -->
                            <td><button class="btn-text-green" id="view-button"> <i class="fa fa-list"> </i> View</button></td>
                        </tr>
                    <?php
                    }
                    ?>

                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>

        <!-- Modal  -->
        <div class="modal" id="customer-modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="user">
                </div>
            </div>
        </div>



        <script>
            // Get the modal
            var modal = document.getElementById("customer-modal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // Get all view buttons
            var viewButtons = document.querySelectorAll('#view-button');

            // Function to handle modal display
            function openModal(id) {
                // document.getElementById("modal-content").innerHTML = content;.

                modal.style.display = "block";
                $.get(`<?php echo ROOT_DIR ?>/admin/customers/viewUser/${id}`, function(data) {
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
                    };

                    openModal(key);
                });
            });

            // Close the modal when the close button is clicked
            // span.onclick = function() {
            //     modal.style.display = "none";
            // }

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