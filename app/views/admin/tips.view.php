<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/admin/components/navbar.php');

// require_once('../app/views/admin/layout/sidebar.php');

?>

<div class="dashboard">
    <?php require_once('../app/views/admin/layout/sidebar.php');
    ?>



    <div class="sidebar-flow"></div>
    <!-- Add button -->
    <!-- <div class="tips-page"> -->


    <div class="guide-dash-main">
        <h1 class="title mb-2">Tips & Knowhows</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Tips & Knowhows</a></li>
        </ul>


        <div class="table-container">
            <div>
                <button class="btn ml-2">Add</button>

                <!-- 
                <div class="info-data mt-5">
                    <div class="card"> -->
                <!-- <div class="head"> -->

                <table class="data-table table-custom my-0 px-0 mt-3">
                    <thead>
                        <tr>
                            <th class="pl-2 pr-2">
                                Title
                            </th>
                            <th class=" pl-5 pr-6">
                                Description
                            </th>
                            <th class="pl-6 text-align-end pr-6">
                                Action
                            </th>


                        </tr>
                    </thead>


                    <tbody>
                        <?php foreach ($tips as $tip) : ?>
                            <tr>
                                <td><?php echo $tip->title; ?></td>
                                <td class="ll"><?php echo $tip->description; ?></td>
                                <td><button key="<?php echo $tip->id; ?>" class="view-button">Update</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

            <!-- Add Form Modal -->
            <div class="modal" id="add-modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Add Form</h2>
                    <form id="add-form" action="<?php echo ROOT_DIR ?>/admin/tips/add" method="post">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required><br>

                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4" required></textarea><br>

                        <button type="submit">Add</button>
                    </form>
                </div>
            </div>

            <!-- Update Form Modal -->
            <div class="modal" id="update-modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Tips Update Form</h2>
                    <form id="update-form" action="<?php echo ROOT_DIR ?>/admin/tips/update" method="post">
                        <input name="id" id="tip-id" type="hidden" value="">

                        <label for="updated-title">Title:</label>
                        <input type="text" id="updated-title" name="title" required><br>

                        <label for="updated-description">Description:</label>
                        <textarea id="updated-description" name="description" rows="4" required></textarea><br>

                        <button class="btn-success">Update</button>
                        <button class="btn-danger"> <a id="delete-tip" href="<?php echo ROOT_DIR ?>/admin/tips/delete/  "> Delete </a> </button>
                    </form>
                </div>
            </div>

        </div>


        <script>
            // Get the modal elements
            var addModal = document.getElementById("add-modal");
            var updateModal = document.getElementById("update-modal");

            // Get the buttons that open the modals
            var addButton = document.querySelector('.add-button');
            var updateButtons = document.querySelectorAll('.view-button');

            // Function to open add form modal
            function openAddModal() {
                addModal.style.display = "block";
            }

            // Function to open update form modal
            function openUpdateModal(name, email, id) {
                // Populate update form fields with existing data
                document.getElementById("updated-title").value = name;
                document.getElementById("updated-description").value = email;
                document.getElementById("tip-id").value = id;
                document.getElementById("delete-tip").href = "<?php echo ROOT_DIR ?>/admin/tips/delete/" + id;
                updateModal.style.display = "block";
            }

            // Event listener for add button click
            addButton.addEventListener('click', function() {
                openAddModal();
            });

            // Event listeners for update buttons click
            updateButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
                    var email = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
                    var id = this.getAttribute('key');
                    openUpdateModal(name, email, id);
                });
            });

            // Close modals when the close button is clicked
            var closeButtons = document.querySelectorAll('.close');

            closeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    addModal.style.display = "none";
                    updateModal.style.display = "none";
                });
            });

            // Handle form submissions (you can add your form submission logic here)
            document.getElementById("add-form").addEventListener('submit', function(event) {
                // event.preventDefault();
                // Handle add form submission logic
                // ...
                // Close the add modal after submission if successful
                addModal.style.display = "none";
            });

            document.getElementById("update-form").addEventListener('submit', function(event) {
                // event.preventDefault();
                // Handle update form submission logic
                // ...
                // Close the update modal after submission if successful
                updateModal.style.display = "none";
            });
        </script>




        <?php
        require_once('../app/views/admin/layout/footer.php');


        ?>