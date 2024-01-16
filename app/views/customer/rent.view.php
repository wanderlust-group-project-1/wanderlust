<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/components/navbar.php');

?>

<div class="container">

<div class="row">
    <div class="col-12">
        <div class = "search-container">
            <form action="<?= ROOT_DIR ?>/search" method="get">
                <input type="text" id="search-input" placeholder="Search.." name="search">
                <!-- Select Type of result -->
                <select name="type" id="type">
                    <option value="all">All</option>
                    <option value="shops">Shops</option>
                    <option value="items">Items</option>
                </select>
                <!-- Select location button  -->
                <input type="text" class="form-control" id="latitude" name="latitude" hidden/>
                <input type="text" class="form-control" id="longitude" name="longitude" hidden/>
                <button id="get-location" > <i class="fa fa-map-marker"></i></button>
                <button id="search-button" type="submit"><i class="fa fa-search"></i></button>
            </form>
    </div>

</div>

<div class="row" id="search-result"> 
</div>

<div class="modal" id="location-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <input id="pac-input" class="controls" type="text" placeholder="Enter Location" />

        <div id="map-canvas" class="map-canvas"> </div>

            <!-- <input type="text" class="form-control" id="latitude"  hidden/>
            <input type="text" class="form-control" id="longitude" hidden/> -->

        <div class="location-button-container">
            <button id="confirm-location" class="location-button" type="button">Confirm Location</button>
        </div>
    </div>

</div>


<script>

    // Get the modal
    var modal = document.getElementById("location-modal");

    // Get the button that opens the modal
    var btn = document.getElementById("get-location");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal

    btn.onclick = function(event) {
        event.preventDefault();
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal

    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // confirm location button
    var confirmLocationButton = document.getElementById("confirm-location");



    confirmLocationButton.onclick = function() {
      



        modal.style.display = "none";
    }

    </script>



<!-- Search -->
<script>
    var searchButton = document.getElementById("search-button");
    var searchInput = document.getElementById("search-input");
    var searchForm = document.getElementById("search-form");

    searchButton.addEventListener("click", function(event) {
        event.preventDefault();
        getResults();
    });
 



    function getResults(){
        var search = document.getElementById("search-input").value;
        var type = document.getElementById("type").value;
        var latitude = document.getElementById("latitude").value;
        var longitude = document.getElementById("longitude").value;

        console.log(search);
        console.log(type);
        console.log(latitude);
        console.log(longitude);

        $.ajax({
    headers: {
        'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
        'Content-Type': 'application/json'  // Specify the content type as JSON
    },
    url: '<?= ROOT_DIR ?>/rent/search',
    method: 'POST',
    dataType: 'json',  // Expect JSON response from the server
    data: JSON.stringify({  // Convert the data object to a JSON string
        search: search,
        type: type,
        latitude: latitude,
        longitude: longitude
    }),
    success: function(data) {
        console.log(data);
        $('#search-results').html(data);
    },
    error: function(err) {
        console.log(err);
    }
});

    }

    </script>



<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY  ?>&libraries=places&callback=initialize" async defer></script>
<script src="<? echo ROOT_DIR ?>/assets/js/map.js"></script>