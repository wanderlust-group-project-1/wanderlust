<div class=" col-lg-12 flex-d-c gap-2 ">
    <h2 class="justify-content-center flex-d">Cart</h2>

    <div class="row gap-2">
        <h3> <?php echo htmlspecialchars($cart->start_date); ?> - <?php echo htmlspecialchars($cart->end_date); ?> </h3>
        </div>
    <div class="row gap-2 ">
        <!-- scrollable cart items -->
        <!-- <div class="col-lg-12    " id="cart-items"> -->
        <div class="col-lg-12   cart-items overflow-scroll " id="cart-items">




            <?php 
            if (empty($items)) {
                echo "<div class='emptycart-container'>
                        <div class='row gap-2'>
                            <div class='emptycart'><h1>Empty Cart</h1></div>
                        </div>
                        <hr>
                        <div class='row gap-2'>
                            <button class='btn-text-green'>Shop now</button>
                        </div>
                    </div>";
            }
            foreach ($items as $item): ?>
                <div class="card-grid" id='cart-item' data-id="<?= htmlspecialchars($item->id) ?>">
                <div class="row gap-5">
                    <div class="cart-item-image col-lg-4">
                        <img src="<?=OSURL?>images/equipment/<?php echo htmlspecialchars($item->e_image); ?>" alt="Image" class="img-fluid">
                    </div>
                    <div class="cart-item-details col-lg-5">
                        <h4 class="cart-item-name"><?php echo htmlspecialchars($item->e_name); ?></h4>
                        <p class="cart-item-description"><?php echo htmlspecialchars($item->e_description); ?></p>
                        <!-- <div class="item-count">
                        </div> -->
                        <div class="cart-item-price">
                            <h4>Rs. <?php echo htmlspecialchars($item->total); ?></h4>
                            <input class="form-control-lg" type="number" name="count" id="item-count" value="1" min="1" max="48">
                        </div>
                    </div>
                    <button id="remove-from-cart" class="btn-icon"><i class="fa fa-trash" aria-hidden="true"></i></button>

                </div>
                </div>
            <?php endforeach; ?>


        </div>
    </div>
    <div class="row gap-2">
        <div class="total">
            <h4>Subtotal: Rs. <span id="total">

            <?php
            // $total = 0;
            // foreach ($items as $item) {
            //     $total += $item->e_fee;
            // }
            echo $total;
            ?>

            </span></h4>
        </div>
    </div>
    <div class="row gap-2">
     <a href= <?php echo ROOT_DIR . "/cart/checkout" ?> class="btn btn-primary">Go to checkout</a>

        <!-- <button id="checkout" class="btn" type="button">Checkout</button> -->
    </div>
</div>