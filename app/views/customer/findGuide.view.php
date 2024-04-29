<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/navbar/customer-navbar.php');

?>
<div class="container flex-d-c justify-content-center gap-2">
    <div class="row gap-2 mt-7">

        <form id="searchForm" class="searchForm form">
            <label> Date: </label>
            <input type="date" name="date" id="date" required>

            <label> Location: </label>
            <input type="text" name="location" id="location" required>

            <label> No of People: </label>
            <input type="number" name="no_of_people" id="no_of_people" required>

            <label> Transport Supply: </label>
            <input type="checkbox" name="transport_supply" id="transport_supply">

            <button id="search-button" class="btn-icon" type="submit"><i class="fa fa-search"></i></button>

        </form>

    </div>


    <div id="all-guides" class="row gap-2 justify-content-center mt-7">
        <!-- All guides will be displayed here -->
    </div>
</div>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form input values
        var date = document.getElementById('date').value;
        var location = document.getElementById('location').value;
        var no_of_people = document.getElementById('no_of_people').value;
        var transport_supply = document.getElementById('transport_supply').checked;

        // Create an object with the form data
        var jsonData = {
            date: date,
            location: location,
            no_of_people: no_of_people,
            transport_supply: transport_supply ? 1 : 0
        };

        // Save the data to local storage
        localStorage.setItem('searchData', JSON.stringify(jsonData));

        console.log(jsonData);

        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/findGuide/search',
            method: 'POST',
            data: JSON.stringify(jsonData),
            contentType: 'application/json',
            processData: false,
            success: function(data) {
                console.log(data);
                $('#all-guides').html(data);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>

<?php
    require_once('../app/views/layout/footer-main.php');
?>

<?php
    require_once('../app/views/layout/footer.php');
?>