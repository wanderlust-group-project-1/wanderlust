<div class=" col-lg-12 flex-d-c gap-2 ">
    <h2 class="justify-content-center flex-d"> Cart </h2>
    <div class="row gap-2 ">
        <!-- scrollable cart items -->
        <!-- <div class="col-lg-12    " id="cart-items"> -->
        <div class="col-lg-12   cart-items overflow-scroll " id="cart-items">

        <!-- [0] => stdClass Object
        (
            [id] => 1
            [cart_id] => 12
            [item_id] => 1
            [e_name] => Baker Mueller
            [e_image] => 65bcc674ecbcb.jpg
        ) -->



            <?php 
            if (empty($data)) {
                echo "<h3>Cart is empty</h3>";
            }
            foreach ($data as $item): ?>
                <div class="card " data-id="<?= htmlspecialchars($item->item_id) ?>">
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
    <div class="row gap-2">
        <button id="checkout" class="btn" type="button">Checkout</button>
    </div>
</div>