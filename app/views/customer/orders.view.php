<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/components/navbar.php');

?>

<div class="container flex-d flex-md-c justify-content-center ">
    <div class=" col-lg-12 flex-d-c gap-2 ">

        <div class="card card-normal ">

        <h2 class="justify-content-center flex-d"> Orders </h2>

        <div class="row gap-2 ">
            <!-- scrollable cart items -->
            <!-- <div class="col-lg-12    " id="cart-items"> -->
            <div class="col-lg-12 checkout-items overflow-scroll " >

<div id="orders">



<div class="card"  data-id="1">
                <div class="row gap-2">

                <div class="col-lg-4 col-md-12">
                    <h3>Order ID: 1</h3>
                    <h3>Order Date: 2021-08-01</h3>
                 
                </div>

                <div class="col-lg-4 col-md-12">
                    <p> Tent, BBQ Grill, Table, Chair, Cooler</p>
                    
                </div>

                <div class="col-lg-3 col-md-12">

                <!-- view button -->
                <a class="btn btn-primary"  id="view-button">View</a>
                    
                    </div>

                </div>
                            
                        </div>



</div>

            </div>


        </div>

           
        </div>
    </div>
</div>




<!-- View modal -->
<div class="modal" id="order-item-modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        
        <div id="order-data">   </div>

    </div>
</div>


<script>

    function orderLoadScript(){

      var viewButtons = document.querySelectorAll('#view-button');

        var orderItemModal = document.getElementById("order-item-modal");
        var orderData = document.getElementById("order-data");

        // When the user clicks the button, open the modal

        viewButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                orderItemModal.style.display = "block";
                var orderId = button.closest('.card').getAttribute('data-id');
                $.ajax({
                    url: '<?= ROOT_DIR ?>/myOrders/viewOrder/' + orderId,
                    headers: {
                        'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
                    },
                    type: 'GET',
                    success: function(data) {
                        orderData.innerHTML = data;
                    }
                });
            });
        });
    }


</script>



<script>
    $(document).ready(function() {

        $.ajax({
            url: '<?= ROOT_DIR ?>/myOrders/list',
            headers: {
                'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
            },
            type: 'GET',
            success: function(data) {
                $('#orders').html(data);
            }
        });
    });
</script>