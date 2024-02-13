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



</div>

            </div>


        </div>

           
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $.ajax({
            url: '<?= ROOT_DIR ?>/orders/list',
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