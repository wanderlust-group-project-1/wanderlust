<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/navbar/customer-navbar.php');

?>

<div class="container flex-d-c justify-content-center gap-2">



<div class="row gap-2 justify-content-end sticky">

    <button id="cart" class="btn " type="button"> <i class="fa fa-shopping-cart"></i> Cart <span id="cart-count" class="badge bg-primary">0</span></button>

 
</div>


<div class="row">

<div class="col-lg-12 flex-d-c gap-2">

<div class="card-normal justify-content-between align-items-center flex-d gap-2">

<!-- Rental Image and name  -->
<div class="flex-d gap-2 flex-sm-c">
    <!-- hard coded image -->
    <!-- <?php show($rental); ?> -->
    <!-- <img src="<? echo ROOT_DIR?>/assets/images/rental/1.webp" alt="Image" class="img-fluid mh-200px rounded-7"> -->
    <img src="<? echo OSURL?>images/rental_services/<?php echo $rental->image; ?>" alt="Image" class="img-fluid mh-200px rounded-7">
   
    <div class="flex-d gap-2 align-items-center">
        <!-- Rental Service name -->
        <h1 class="rental-name"> <?php echo $rental->name; ?> </h1>
        <!-- Rental Service description -->
    <!-- Rental Service name -->
</div>
</div>

<div class="flex-d gap-2 align-items-center col-lg-7  ">
    <!-- Rent Statics, Orders , Equiment types -->

    <div class="flex-d gap-2 align-items-center  card-invert">
        <i class="fa fa-shopping-cart"></i>
        <span>Orders: <?php echo $stat->orders_count; ?></span>

       
    </div>

    <div class="flex-d gap-2 align-items-center card-invert">
        <i class="fa fa-cogs"></i>
        <span>Equipment Types: <?php echo $stat->equipments_count; ?></span>

    </div>


</div>

</div>



</div>



</div>



<div class="row">
<div class="col-lg-8 flex-d justify-content-center " >


<div class = "search-container col-lg-12">
    <form action="<?= ROOT_DIR ?>/search" method="get">
    <div class="row gap-2">
        <input type="text" id="search-input"  placeholder="Search.." name="search">
        <!-- Select Type of result -->
        <select name="type" id="type">
            <option value="all">All</option>
            <option value="shops">Shops</option>
            <option value="items">Items</option>
        </select>
        <!-- Select location button  -->
        <button id="search-button"  class="btn btn-lg" type="submit"><i class="fa fa-search "></i></button>
    </div>
    </form>
</div>

</div>
</div>


<div class="row" id="item-list">


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
// Cart Modal

var cartModal = document.getElementById("cart-modal");

// Get the button that opens the modal
var cartModalBtn = document.getElementById("cart");

// Get the <span> element that closes the modal

var cartClose = document.getElementById("cart-modal").querySelector(".close");

// When the user clicks the button, open the modal
cartModalBtn.onclick = function() {

    // get cart items
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
        },
        error: function(err) {
            console.log(err);
        }
    });
}



    </script>




<!-- Search -->
<script>



$(document).ready(function() {
    $.ajax({
        headers: {
            'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
        },
        
        url: '<?= ROOT_DIR ?>/rentalService/availableEquipments/<?= $rental->id ?>',

        type: 'POST',
        contentType:"application/json; charset=utf-8",

        data: JSON.stringify({
            search: '',
            type: 'all',
        }),
        
        success: function(response) {
            // console.log(response);
            $('#item-list').html(response);
        }
    });
});




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


        console.log(search);
        console.log(type);


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




    </script>



<?php
require_once('../app/views/layout/footer.php');

?>