<div class="order-pay-content ">
    <h2 class="text-center">Pay</h2>
    
    <table class="payment-table w-100">
         <tr>
            <td>Order ID</td>
            <td><?= $rent->id ?></td>
         </tr>
            <tr>
                <td>Order Date</td>
                <td><?= $rent->created_at ?></td>
            </tr>
            <tr>
                <td>Total Amount</td>
                <td><?= $rent->total ?></td>
            </tr>
            <tr>
                <td>Paid Amount</td>
                <td><?= $rent->paid_amount ?></td>
            </tr>

            <tr> 
                <td>Amount Due</td>
                <td> <b> <?= number_format($rent->total - $rent->paid_amount, 2) ?> </b></td>
            </tr>

    </table>

    <div class="payment m-4 flex-d align-items-center justify-content-center gap-2">
        <button class="btn-text-green border"   id="full-pay-confirm" data-id="<?= $rent->id ?>"
        >Pay Now</button>

        <button class="btn-text-red border modal-close" >Cancel</button>
    </div>

</div>