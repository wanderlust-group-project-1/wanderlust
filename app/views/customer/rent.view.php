<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/navbar/customer-navbar.php');

?>




<div class="container flex-d-c justify-content-center gap-2">
<div class="customer-bg-image">
    <img src="<?php echo ROOT_DIR?>/assets/images/customerbg.jpg" alt="customer-bg-image" class="customer-bg-image">
</div>


<div class="row gap-2 justify-content-end mt-7">
    <!-- Cart -->
    <!-- <button id="cart" class="btn " type="button"> <i class="fa fa-shopping-cart"></i> Cart</button> -->
    <!-- with number of item in cart -->
    <div class="cart-notification">
        <button id="cart" class="btn-icon " type="button"> 
            <span><i class="fa fa-shopping-cart"></i></span>
            <span id="cart-count" class="badge">0</span>
        </button>
    </div>
 
</div>

<div class="customer-header w-100 flex-d-c justify-content-center align-items-center">
    




<div class="col-lg-6 flex-d justify-content-center w-100 p-1 ">
    <div class="card-normal mw-50 pl-10 w-100 mb-0">
        <div class="col gap-2 bg-transparent">
            <!-- Change date -->
            <div>
                 <p class="date-change-phase" id="date-change-phase" ><? echo isset($cart) ? "Selected Date: " . $cart->start_date . " - " . $cart->end_date : "Select Date"; ?></p>
            </div>
            <div>
            <button id="change-date" class="btn-text-green" type="button"><i class="fa fa-calendar"></i> Change Date</button>
            </div>
        </div>
    </div>
</div>




<div class="col-lg-6 flex-d justify-content-center w-100 p-1">
    <div class="card-normal-transparent mw-75 pl-10 w-100">
        <div class="row">
            <div class="col-lg-8 flex-d justify-content-center">
                <div class = "search-container col-lg-12">
                <form action="<?= ROOT_DIR ?>/search" method="get">
                    <div class="row gap-2 w-100">
                        <input type="text" id="search-input" class="form-control-lg mw-75"  placeholder="Search item by name.." name="search">
           
                        <input type="text" class="form-control no-display" id="latitude" name="latitude" hidden/>
                        <input type="text" class="form-control no-display" id="longitude" name="longitude" hidden/>
                        <button id="get-location" class="btn-icon" > <i class="fa fa-map-marker"></i></button>
                        <button id="search-button"  class="btn-icon" type="submit"><i class="fa fa-search"></i></button>

                        
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>



</div>


<div class="row" id="search-result"> 
</div>
<div class="row" id="item-list">

</div>

<div class="modal" id="location-modal">

    <div class="modal-content ">
    <span class="close">&times;</span>

    <div class=" col-lg-12 flex-d-c gap-2 location-container">

    <div class="location-form-element">    
    <input id="pac-input" class="controls " type="text" placeholder="Enter Location" />
    </div>
        <div id="map-canvas" class="map-canvas"> </div>

            <!-- <input type="text" class="form-control" id="latitude"  hidden/>
            <input type="text" class="form-control" id="longitude" hidden/> -->

        <div class="location-button-container">
            <button id="confirm-location" class="location-button btn-text-green border center" type="button">Confirm Location</button>
        </div>
    </div>
    </div>

</div>

<div class="modal" id="select-date">

<!-- Start date and End date for rent  -->
<div class="modal-content-mini">
    <span class="close">&times;</span>
    <div class=" col-lg-12 flex-d-c gap-2 ">
        <h2 class="justify-content-center flex-d">Select Date</h2>
        <div class="row gap-2">

            <div class="date"><label for="start-date">Start Date</label></div>
            <div class="date"><input type="date" id="start-date" class="form-control-lg " name="start-date" required value="<?php isset($cart)  && print($cart->start_date); ?>"></div>
            <div class="date"><label for="end-date">End Date</label></div>
            <div class="date"><input type="date" id="end-date" class="form-control-lg" name="end-date" required value="<?php isset($cart)  && print($cart->end_date); ?>"></div>
            <div class="date">
            <div class="date-para"><h5>Select days you want to rent camping equipment. If you change the date new cart will be created. Items added to the previous cart will be removed.</h5></div>
        </div>
        </div>  
        <div class="row gap-2">
            <div class="date"><button id="confirm-date" class="btn" type="button">Confirm Date</button></div>
        </div>
    </div>
</div>

</div>


<!-- Cart Model -->
<div class="modal" id="cart-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id ="cart-data">

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


    // validate the date
    if (startDate == "" || endDate == "") {
        alertmsg("Please select the date",'error');
        return;
    }

    // start date should be less than end date , start date should be greater than tommorow
    if (startDate > endDate) {
        alertmsg("Start date should be less than end date",'error');
        return;
    }

    if (startDate <= new Date().toISOString().split('T')[0]) {
        alertmsg("Start date should be greater than today",'error');
        return; 
    }



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

    // get cart items
   getCart()
}

function getCart(){
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/cart/viewCart',
        method: 'GET',
        success: function(data) {
            $('#cart-data').html(data);
            // console.log(data);
            
        },
        error: function(err) {
            console.log(err);
        }

    });


    cartModal.style.display = "block";
    console.log("cart modal clicked");

    // cartLoadScript();
    // wait and load the script
    setTimeout(cartLoadScript, 1000);
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


 function cartLoadScript(){

    // cart-item
    var removeButtons = document.querySelectorAll("#remove-from-cart");

    removeButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var id = button.closest('#cart-item').getAttribute('data-id');
            console.log(id);
            removeItem(id);
        });
    });
 }


 function removeItem(id) {
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
            'Content-Type': 'application/json'
        },
        url: '<?= ROOT_DIR ?>/api/cart/removeItem',
        method: 'POST',
        data: JSON.stringify({
            id: id
        }),
        success: function(data) {
            // console.log(data);
            // remove the item from the cart
            $(`[data-id=${id}]`).remove();
            getCartCount();
            getCart()

            
        },
        error: function(err) {
            console.log(err);
        }
    });
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
        // var type = document.getElementById("type").value;
        var type = 'all';
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
<script src="<?php echo ROOT_DIR ?>/assets/js/map.js"></script>

<script>

// #item-list load with ajax http post request

$(document).ready(function() {
    getCartCount();
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
        },
        error: function(err) {
        //    display change date modal
        dateModal.style.display = "block";
        $('#item-list').html('<h2> No items available for rent <br> Please try to change the date </h2>');


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
            getResults();
            getCartCount();
        },
        error: function(err) {
            console.log(err);
        }

    });

    $('#date-change-phase').text("Selected Date: " + start + " - " + end);


    

}


// Add to cart

    // Add to Cart Button click event
    $(document).on('click', '#add-to-cart', function() {
        console.log("add to cart clicked");
        var id = $(this).closest('#rent-item-details').attr('data-id');
        console.log(id);
        var count = $(this).closest('#rent-item-details').find('#item-count').val() || 1;

        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token'),
                'Content-Type': 'application/json'
            },
            url: '<?= ROOT_DIR ?>/api/cart/addItem',
            method: 'POST',
            data: JSON.stringify({
                equipment_id: id,
                count: count

            }),
            success: function(data) {
                // console.log(data);
                disableButton(id);
                $('#equipment-details-modal').css('display', 'none');
                alertmsg("Item added to cart",'success');

                getCartCount();
            },
            error: function(err) {
                // console.log(err);
            }
        });

        

    });

    // close the modal

    $(document).on('click', '.close', function() {
        var modal = $(this).closest('.modal');
        modal.css('display', 'none');
    });


    // after adding to cart the button should change to 'Added' and should be disabled

    // function / use jQuery
    function disableButton(id) {
        // var button = $(`[data-id=${id}]`).find('#add-to-cart');
        // button.text('Added');
        // button.prop('disabled', true);
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

    


    $(document).on('click', '#rent-item-card', function() {

        var id = $(this).attr('data-id');
        console.log(id);

        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/rent/item/' + id,
            method: 'GET',
            success: function(data) {
                console.log(data);
                $('#equipment-details').html(data);
                $('#equipment-details-modal').css('display', 'block');
            },
            error: function(err) {
                console.log(err);
            }
        });
        


    })

    // item count change price

    $(document).on('change', '#item-count', function() {
        var count = $(this).val();
        console.log(count);
        var fee = $('#item-fee').attr('data-fee');
        console.log(fee);
        var total = count * fee;
        $('#item-fee').text("Rs. " + total);
    });
  







</script>


<?php
require_once('../app/views/layout/footer.php');

?>