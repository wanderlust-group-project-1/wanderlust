<div class=" col-lg-12 flex-d-c gap-2 ">
    <h2 class="justify-content-center flex-d"> Cart </h2>
    <div class="row gap-2 ">
        <!-- scrollable cart items -->
        <!-- <div class="col-lg-12    " id="cart-items"> -->
        <div class="col-lg-12   cart-items overflow-scroll " id="cart-items">




            <?php 
            if (empty($data)) {
                echo "<h3>Cart is empty</h3>";
            }
            foreach ($data as $item): ?>
                <div class="card" id='cart-item' data-id="<?= htmlspecialchars($item->id) ?>">
                <div class="row gap-2">
                    <div class="cart-item-image col-lg-4">
                        <img src="<?=OSURL?>images/equipment/<?php echo htmlspecialchars($item->e_image); ?>" alt="Image" class="img-fluid">
                    </div>
                    <div class="cart-item-details col-lg-5">
                        <h5 class="cart-item-name"><?php echo htmlspecialchars($item->e_name); ?></h5>
                        <div class="cart-item-price">
                            <h5>Fee: Rs. <?php echo htmlspecialchars($item->e_fee); ?></h5>
                            <button id="remove-from-cart" class="btn btn-primary">Remove from Cart</button>
                        </div>
                    </div>
                </div>
                </div>
            <?php endforeach; ?>


        </div>
    </div>
    <div class="row gap-4">
        <h3>Total: Rs. <span id="total">

        <?php
        $total = 0;
        foreach ($data as $item) {
            $total += $item->e_fee;
        }
        echo $total;
        ?>

        </span></h3>
    </div>
    <div class="row gap-2">
     <a href= <?php echo ROOT_DIR . "/cart/checkout" ?> class="btn btn-primary">Checkout</a>

        <!-- <button id="checkout" class="btn" type="button">Checkout</button> -->
    </div>
</div>