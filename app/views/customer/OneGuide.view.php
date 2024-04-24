<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/navbar/customer-navbar.php');

?>



?>

<div class="dashboard">


    <?php
    // show(UserMiddleware::getUser());
    ?>
    <div class="guide-dash-main flex-d-c m-6">
        <!-- <h1 class="title mb-2">My Guide Profile</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Guide Profile</a></li>
        </ul> -->

        <div class="guide-availability" id="guide-availability">
            <i class="fas fa-calendar"></i></a>
        </div>

        <div class="guide-profile" id="guide-profile">
            <div class="guide-profile-img">
                <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
            </div>
            <div class="guide-profile-content" data-id="<?= htmlspecialchars($guide[0]->id) ?>" id="guide-details">
                <h1>Hi, It's <span><?php echo $guide[0]->guide_name; ?></span></h1>
                <h3 class="typing-text"> I'm a <span>Guide</span></h3>
                <p><?= htmlspecialchars($guide[0]->description) ?></p>
                <!-- <p>Hi, I'm Nirmal, a professional tour guide with 5 years of experience. I have a passion for history and culture and love sharing my knowledge with others. I specialize in tours of ancient ruins, temples, and historical sites. I'm also an expert in local cuisine and can recommend the best places to eat in town. Let me show you the beauty of my country and help you create memories that will last a lifetime.</p> -->

            </div>
        </div>

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="card">
                <span class="label">Packages For You</span>
                <?php
                $loopIndex = -1;
                foreach ($packages as $package) :
                    $loopIndex++; ?>
                    <div class="mt-4 mb-2" data-id="<?= htmlspecialchars($packages[$loopIndex][0]->id) ?>">
                        <span class="label">Package <?= $loopIndex + 1 ?></span>
                        <p><?= htmlspecialchars($packages[$loopIndex][0]->price) ?></p>
                        <p><?= htmlspecialchars($packages[$loopIndex][0]->max_group_size) ?></p>
                        <p><?= htmlspecialchars($packages[$loopIndex][0]->max_distance) ?></p>
                        <button class="btn btn-primary mt-5" id="book-guide-btn">Book</button>
                    <?php endforeach; ?>
                    </div>
            </div>
        </div>

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="card">
                <span class="label">Languages</span>

                <?php
                $languages = explode(',', $guide[0]->languages); // Assuming languages are comma-separated in the database
                ?>

                <?php foreach ($languages as $language) : ?>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p><?= htmlspecialchars(trim($language)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card">
                <span class="label">Certifications</span>

                <?php
                $certifications = explode(',', $guide[0]->certifications); // Assuming languages are comma-separated in the database
                ?>

                <?php foreach ($certifications as $certification) : ?>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p><?= htmlspecialchars(trim($certification)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="info-data mt-5 ml-5 mr-5">
            <div class="card">
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

<!-- Modal Box View Booking Guide -->

<div id="book-guide" class="modal book-guide" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Book Guide</h2>
        <div id="book-package-details">
        </div>
    </div>
</div>

<script>
    function viewBookGuide() {
        console.log('book guide');

        var modal = document.getElementById("book-guide");

        // closeBtn = document.querySelectorAll("close")[0];
        // closeBtn.addEventListener("click", function(){
        $(document).on('click', '#book-guide .close', function() {
            modal.style.display = "none";
        });

        // var viewButtons = document.querySelectorAll('#book-guide-btn');
        // console.log(viewButtons);
        // viewButtons.forEach(function(button) {
        //     button.addEventListener('click', function() {
        //         var packageId = button.getAttribute('data-id');
        //         modal.style.display = "block";
        //     });
        // });

        $(document).on('click', '#book-guide-btn', function() {

            var packageId = $(this).parent().attr('data-id');
            console.log(packageId);
            fetchGuidePackages(packageId);
            modal.style.display = "block";
        });
    }

    function fetchGuidePackages(packageId) {
        console.log('fetch packages');
        $.ajax({
            headers: {
                Authorization: "Bearer " + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/FindGuide/viewGuidePackage/' + packageId,
            method: 'GET',
            success: function(data) {
                var modalContent = document.querySelector('#book-guide .modal-content');
                modalContent.innerHTML = data;
                console.log(packageId);

                // var closeBtn = document.querySelector('#book-guide .close');
                // closeBtn.addEventListener('click', function(){
                $(document).on('click', '#book-guide .close', function() {
                    var modal = document.getElementById("book-guide");
                    modal.style.display = "none";
                    console.log("Modal closed");
                });
            },
            error: function(err) {
                console.log(err);
            }
        })
    }


    document.addEventListener("DOMContentLoaded", function() {
        viewBookGuide();
    });
</script>


<!--Booking Guide AJAX request -->
<script>
    $(document).on('click', '#book-guide-package', function() {
        var packageId = $(this).attr('data-id');
        console.log(packageId);

        // Retrieve data from local storage
        var searchData = localStorage.getItem('searchData');
        if (searchData) {
            var jsonData = JSON.parse(searchData); // Parse JSON string to object
            jsonData.package_id = packageId; // Add package_id to the JSON object
            console.log(jsonData);

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/GuideBookings/bookRequest',
                method: 'POST',
                data: JSON.stringify(jsonData),
                contentType: 'application/json',
                processData: false,
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        alertmsg("Guide booking request is successful");
                        window.location.href = '<?= ROOT_DIR ?>/myBookings';
                        // Update UI with new profile details
                        // For example, update name, description, languages, certifications
                        // closeModal(); // Close the modal after successful update
                    } else {
                        // Display errors
                        var errorDiv = document.getElementById("profile-errors");
                        errorDiv.innerHTML = data.errors.join('<br>');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            console.log("No search data found in local storage.");
        }
    });
</script>