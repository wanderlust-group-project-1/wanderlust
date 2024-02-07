<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/components/navbar.php');

?>




<div class="container flex-d-c justify-content-center gap-2">

<div class="row gap-2 justify-content-end">
    <!-- Cart -->
    <!-- <button id="cart" class="btn " type="button"> <i class="fa fa-shopping-cart"></i> Cart</button> -->
    <!-- with number of item in cart -->
    <button id="cart" class="btn " type="button"> <i class="fa fa-shopping-cart"></i> Cart <span id="cart-count" class="badge bg-primary">0</span></button>

 
</div>

    <div class="row gap-2">
        <!-- Change date -->
        <button id="change-date" class="btn" type="button">Change Date</button>
        
    </div>



<div class="row">
    <div class="col-lg-8 flex-d justify-content-center">


        <div class = "search-container col-lg-12">
            <form action="<?= ROOT_DIR ?>/search" method="get">
            <div class="row gap-2">
                <input type="text" id="search-input" placeholder="Search.." name="search">
                <!-- Select Type of result -->
                <select name="type" id="type">
                    <option value="all">All</option>
                    <option value="shops">Shops</option>
                    <option value="items">Items</option>
                </select>
                <!-- Select location button  -->
                <input type="text" class="form-control no-display" id="latitude" name="latitude" hidden/>
                <input type="text" class="form-control no-display" id="longitude" name="longitude" hidden/>
                <button id="get-location" class="btn" > <i class="fa fa-map-marker"></i></button>
                <button id="search-button"  class="btn" type="submit"><i class="fa fa-search"></i></button>
            </div>
            </form>
    </div>

</div>



<div class="row" id="search-result"> 
</div>
<div class="row" id="item-list">

</div>

<div class="modal" id="location-modal">

    <div class="modal-content ">
    <span class="close">&times;</span>

    <div class=" col-lg-12 flex-d-c gap-2 ">

        <input id="pac-input" class="controls " type="text" placeholder="Enter Location" />

        <div id="map-canvas" class="map-canvas"> </div>

            <!-- <input type="text" class="form-control" id="latitude"  hidden/>
            <input type="text" class="form-control" id="longitude" hidden/> -->

        <div class="location-button-container">
            <button id="confirm-location" class="location-button btn" type="button">Confirm Location</button>
        </div>

    </div>
    </div>

</div>

<div class="modal" id="select-date">

<!-- Start date and End date for rent  -->
<div class="modal-content">
    <span class="close">&times;</span>
    <div class=" col-lg-12 flex-d-c gap-2 ">
        <h2 class="justify-content-center flex-d"> Select Date for Rent </h2>
        <div class="row gap-2">
            <label for="start-date">Start Date</label>
            <input type="date" id="start-date" class="form-control-lg " name="start-date" required>
            <label for="end-date">End Date</label>
            <input type="date" id="end-date" class="form-control-lg" name="end-date" required>
        </div>
        <div class="row gap-2">
            <button id="confirm-date" class="btn" type="button">Confirm Date</button>
        </div>
    </div>
</div>

</div>


<!-- Cart Model -->
<div class="modal" id="cart-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class=" col-lg-12 flex-d-c gap-2 ">
            <h2 class="justify-content-center flex-d"> Cart </h2>
            <div class="row gap-2">
                <div class="col-lg-12" id="cart-items">
                </div>
            </div>
            <div class="row gap-2">
                <button id="checkout" class="btn" type="button">Checkout</button>
            </div>
        </div>
    </div>
</div>


<script>

    // Get the modal
    var modal = document.getElementById("location-modal");

    // Get the button that opens the modal
    var btn = document.getElementById("get-location");

    // Get the <span> element that closes the modal
    var locationClose = document.getElementById("location-modal").querySelector(".close");

    // When the user clicks the button, open the modal

    btn.onclick = function(event) {
        event.preventDefault();
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal

    locationClose.addEventListener("click", function() {
        modal.style.display = "none";
    });

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





        // Get the modal
        var dateModal = document.getElementById("select-date");

// Get the button that opens the modal
var dateModalBtn = document.getElementById("change-date");

// Get the <span> element that closes the modal
var dateClose = document.getElementById("select-date").querySelector(".close");

console.log(dateClose);
// When the user clicks the button, open the modal
dateModalBtn.onclick = function() {
    dateModal.style.display = "block";
}

// When the user clicks on <span> (x) or anywhere outside of the modal, close it
dateClose.addEventListener("click", function() {
    dateModal.style.display = "none";
});

// When the user clicks on <span> (x) or anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == dateModal) {
        dateModal.style.display = "none";
    }
}

// Confirm Date

var confirmDateButton = document.getElementById("confirm-date");

confirmDateButton.onclick = function() {
    var startDate = document.getElementById("start-date").value;
    var endDate = document.getElementById("end-date").value;

    console.log(startDate);
    console.log(endDate);

    setNewDate(startDate, endDate);

    dateModal.style.display = "none";
}


// Cart Modal

var cartModal = document.getElementById("cart-modal");

// Get the button that opens the modal
var cartModalBtn = document.getElementById("cart");

// Get the <span> element that closes the modal

var cartClose = document.getElementById("cart-modal").querySelector(".close");

// When the user clicks the button, open the modal
cartModalBtn.onclick = function() {
    cartModal.style.display = "block";
}

// When the user clicks on <span> (x) or anywhere outside of the modal, close it
cartClose.addEventListener("click", function() {
    cartModal.style.display = "none";
});


// When the user clicks on <span> (x) or anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == cartModal) {
        cartModal.style.display = "none";
    }
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
    url: '<?= ROOT_DIR ?>/rent/items',
    method: 'POST',
    data: JSON.stringify({  // Convert the data object to a JSON string
        search: search,
        type: type,
        latitude: latitude,
        longitude: longitude
    }),
    success: function(data) {

        console.log(data);
        // replace the contents of #item-list with the response from the server
        
        $('#item-list').html(data);

    },
    error: function(err) {
        console.log("abc");

        // console.log(err);
    }

});

    }

    </script>



<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY  ?>&libraries=places&callback=initialize" async defer></script>
<script src="<? echo ROOT_DIR ?>/assets/js/map.js"></script>

<script>

// #item-list load with ajax http post request

$(document).ready(function() {
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        
        url: '<?= ROOT_DIR ?>/rent/items',

        type: 'POST',
        contentType:"application/json; charset=utf-8",

        data: JSON.stringify({
            search: '',
            type: 'all',
            latitude: '',
            longitude: ''
        }),
        
        success: function(response) {
            // console.log(response);
            $('#item-list').html(response);
        }
    });
});


// Create cart

function setNewDate(start, end) {

    // send the start and end date to the server as json data

    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
            'Content-Type': 'application/json'
        },
        url: '<?= ROOT_DIR ?>/api/cart/create',
        method: 'POST',
        data: JSON.stringify({
            start_date: start,
            end_date: end
        }),
        success: function(data) {
            console.log(data);
        },
        error: function(err) {
            console.log(err);
        }

    });


    

}


// Add to cart

    // Add to Cart Button click event
    $(document).on('click', '#add-to-cart', function() {
        console.log("add to cart clicked");
        var id = $(this).closest('.rent-item-card').attr('data-id');
        console.log(id);

        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
                'Content-Type': 'application/json'
            },
            url: '<?= ROOT_DIR ?>/api/cart/addItem',
            method: 'POST',
            data: JSON.stringify({
                equipment_id: id
            }),
            success: function(data) {
                console.log(data);
                disableButton(id);
                getCartCount();
            },
            error: function(err) {
                console.log(err);
            }
        });

        

    });


    // after adding to cart the button should change to 'Added' and should be disabled

    // function / use jQuery
    function disableButton(id) {
        var button = $(`[data-id=${id}]`).find('#add-to-cart');
        button.text('Added');
        button.prop('disabled', true);
    }

    // get cart count

    function getCartCount() {
        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/cart/count',
            method: 'GET',
            success: function(data) {
                console.log(data);
                $('#cart-count').text(data.data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    







</script>