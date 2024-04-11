<!-- Report Complaint -->

<?php
// show($items)

// [equipment_id] => 53
// [equipment_name] => BBQ Grill
// [item_number] => I000531268


?>

<h2> Report Complaint </h2>

<table class="table" id="report-complaint-table" data-order-id="<?= $order->id ?>">
    <thead>
        <tr>
            <th>Item_number </th>
            <th>Name</th>
           <!-- checkbox for complaint -->
            <th>Complaint</th>
            <th>Complaint Description</th>
            <th> Charge </th>
            
            



        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item) { ?>
            <tr data-id="<?= $item->equipment_id ?>">
                <td><?= $item->item_number ?></td>
                <td><?= $item->equipment_name ?></td>
                <td>
                    <input id="report-item-checkbox" class="report-item-checkbox" type="checkbox" name="complaint[]" value="<?= $item->equipment_id ?>">
                </td>
                <td>
                    <!-- <input type="text" name="complaint_description[]" class="form-control"> -->
                    <textarea name="complaint_description[]" class="form-control-lg" disabled></textarea>
                </td>
                <td>
                    <!-- <input type="text" name="charge[]" class="form-control-lg" disabled> -->
                    <!-- Price with constraints min 0, max 1000, step 0.01 -->
                    <input type="number" name="charge[]" class="form-control-lg" min="0" max="<?php echo $item->equipment_cost ?>" step="0.01" disabled>


                </td> 
            </tr>
            <!-- submit -->

        <?php } ?>

    </tbody>

    <script>

        // if checkbox is checked, enable the input field
        $('.report-item-checkbox').on('change', function() {
            if($(this).is(':checked')) {
                // $(this).closest('tr').find('input[type="text"]').prop('disabled', false);
                $(this).closest('tr').find('input[type="number"]').prop('disabled', false);
                // textarea
                $(this).closest('tr').find('textarea').prop('disabled', false);
            } else {
                // $(this).closest('tr').find('input[type="text"]').prop('disabled', true);
                $(this).closest('tr').find('input[type="number"]').prop('disabled', true);
                // textarea
                $(this).closest('tr').find('textarea').prop('disabled', true);
            }
        });


        </script>
    

</table>
<button class="btn btn-primary" id="report-complaint-submit">Report Complaint</button>
