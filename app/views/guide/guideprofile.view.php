<?php
require_once('../app/views/layout/header.php');
?>

<?php require_once('../app/views/guide/layout/guide-sidebar.php'); 


?>

<div class="dashboard">

    <div class="sidebar-flow"></div>

    <?php 
    // show(UserMiddleware::getUser());
    ?>
    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">My Guide Profile</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Guide Profile</a></li>
        </ul>

        <div class="guide-availability" id="guide-availability">
            <a class="btn-text-black" href="<?= ROOT_DIR ?>/guideavailability"><i class="fas fa-calendar" aria-hidden="true"></i> Availability</i></a>
            <!-- <button class="btn-text-red" id="cancel-complaint"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>   -->

        </div>

        <div class="guide-profile" id="guide-profile">
            <div class="guide-profile-img">
                <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
            </div>
            <div class="guide-profile-content">
                <h1>Hi, It's <span><?php echo $user->name; ?></span></h1>
                <h3 class="typing-text"> I'm a <span>Guide</span></h3>
                <p><?= htmlspecialchars($guideProfile[0]->description) ?></p>
                <!-- <p>Hi, I'm Nirmal, a professional tour guide with 5 years of experience. I have a passion for history and culture and love sharing my knowledge with others. I specialize in tours of ancient ruins, temples, and historical sites. I'm also an expert in local cuisine and can recommend the best places to eat in town. Let me show you the beauty of my country and help you create memories that will last a lifetime.</p> -->

            </div>
        </div>

        <div class="guide-availability" id="guide-availability">
            <button class="btn-primary-small" id="edit-guide-profile-button">Edit Guide Profile</button>
        </div>

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="guide-card-new">
                <span class="label">Languages</span>

                <?php
                $languages = explode(',', $guideProfile[0]->languages); // Assuming languages are comma-separated in the database
                ?>

                <?php foreach ($languages as $language) : ?>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p><?= htmlspecialchars(trim($language)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="guide-card-new">
                <span class="label">Certifications</span>

                <?php
                $certifications = explode(',', $guideProfile[0]->certifications); // Assuming languages are comma-separated in the database
                ?>

                <?php foreach ($certifications as $certification) : ?>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p><?= htmlspecialchars(trim($certification)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="guide-card-new">
                <span class="label">Booking History</span>

                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>23rd March : Micheal Julius</p>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>23rd March : Micheal Julius</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Box Profile Edit -->
<div class="profile-editor" id="guide-profile-editor">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="profile-info">
            <form id="guide-profile-form" action="<?= ROOT_DIR ?>/guideprofile/update" method="post">
                <h2 class="guide-h2-title">Edit Guide Profile</h2>
                <div id="profile-errors"></div> <!-- Display errors here -->
                <label for="description">Description</label>
                <textarea name="description" id="description" cols="30" rows="100" class="form-control form-control-lg" required><?= htmlspecialchars($guideProfile[0]->description) ?></textarea>

                <label for="languages">Languages</label>
                <input type="text" name="languages" id="languages" class="form-control" value="<?= htmlspecialchars($guideProfile[0]->languages) ?>" required>

                <label for="certifications">Certifications</label>
                <input type="text" name="certifications" id="certifications" class="form-control" value="<?= htmlspecialchars($guideProfile[0]->certifications) ?>" required>


                <input type="submit" class="btn mt-4" name="submit" value="Update">
            </form>
        </div>
    </div>
</div>


<!-- Modal Box Profile Edit End -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById("guide-profile-editor");
        var closeButton = document.querySelector("#guide-profile-editor .close");
        var editProfileButton = document.getElementById("edit-guide-profile-button");

        // Function to handle modal display
        function openModal() {
            modal.style.display = "block";
        }

        // Add click event listener to edit profile button
        editProfileButton.addEventListener('click', function() {
            openModal();
        });

        // Function to close the modal
        function closeModal() {
            modal.style.display = "none";
            console.log("Modal closed");
        }

        // Add event listeners for closing the modal
        closeButton.addEventListener('click', closeModal);

        // Close the modal if the user clicks outside of it
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });


        // AJAX form submission
        var guideProfileForm = document.getElementById("guide-profile-form");
        guideProfileForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission
            var formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Add header to identify AJAX request
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI with new profile details
                        // For example, update name, description, languages, certifications
                        closeModal(); // Close the modal after successful update
                    } else {
                        // Display errors
                        var errorDiv = document.getElementById("profile-errors");
                        errorDiv.innerHTML = data.errors.join('<br>');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>