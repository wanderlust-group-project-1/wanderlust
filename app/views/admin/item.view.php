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
        <h1 class="title mb-2">Items</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Items</a></li>
        </ul>

        sqw
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Assuming rentalServices is an array of data

                    show($items);
                    foreach ($item as $item) {
                    ?>
                        <tr key="<?php echo $item->equipment_id ?>">
                            <td><?php echo $item->type; ?></td>
                            <td><?php echo $service->count; ?></td>
                            <td><span class="status <?php echo $service->status;  ?>"><?php echo $service->status; ?></span></td>
                            <td><button class="btn-text-green" id="view-button"> <i class="fa fa-list"> </i> View</button></td>
                        </tr>
                    <?php
                    }
                    ?>


                    <tr>
                        <td>Item 1</td>
                        <td>Type A</td>
                        <td>5</td>
                        <td>$10.00</td>
                        <td><button class="view-button">View</button></td>
                    </tr>
                    <tr>
                        <td>Item 2</td>
                        <td>Type B</td>
                        <td>10</td>
                        <td>$15.50</td>
                        <td><button class="view-button">View</button></td>
                    </tr>
                    <tr>
                        <td>Item 3</td>
                        <td>Type C</td>
                        <td>3</td>
                        <td>$8.75</td>
                        <td><button class="view-button">View</button></td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <!-- <div class="modal" id="item-details-modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div class="item-info">
                    <h2 id="item-name">Item Name</h2>
                    <p id="item-type">Type: Type A</p>
                    <p id="item-quantity">Quantity: 5</p>
                    <p id="item-price">Price: $10.00</p>
                </div>
            </div>
        </div> -->

        <script>
            // Get the modal
            var modal = document.getElementById("item-details-modal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // Get all view buttons
            var viewButtons = document.querySelectorAll('.view-button');

            // Function to handle modal display
            function openModal(content) {
                // document.getElementById("modal-content").innerHTML = content;
                modal.style.display = "block";
            }

            // Add click event listener to view buttons
            viewButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
                    var type = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
                    var quantity = this.parentElement.parentElement.querySelector('td:nth-child(3)').textContent;
                    var price = this.parentElement.parentElement.querySelector('td:nth-child(4)').textContent;
                    openModal("Name: " + name + "<br>Type: " + type + "<br>Quantity: " + quantity + "<br>Price: " + price);
                });
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

        <?php
        require_once('../app/views/admin/layout/footer.php');
        ?>

    </div>