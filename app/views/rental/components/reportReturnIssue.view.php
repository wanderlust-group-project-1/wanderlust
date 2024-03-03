<!-- Report Issue -->

<?php
// show($items)

// [equipment_id] => 53
// [equipment_name] => BBQ Grill
// [item_number] => I000531268


?>

<h2> Report Issue </h2>

<table class="table">
    <thead>
        <tr>
            <th>Item_number </th>
            <th>Name</th>
           <!-- checkbox for issue -->
            <th>Issue</th>
            <th>Issue Description</th>
            <th> Charge </th>
            
            



        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item) { ?>
            <tr data-id="<?= $item->equipment_id ?>">
                <td><?= $item->item_number ?></td>
                <td><?= $item->equipment_name ?></td>
                <td>
                    <input id="report-item-checkbox" class="report-item-checkbox" type="checkbox" name="issue[]" value="<?= $item->equipment_id ?>">
                </td>
                <td>
                    <!-- <input type="text" name="issue_description[]" class="form-control"> -->
                    <textarea name="issue_description[]" class="form-control-lg" disabled></textarea>
                </td>
                <td>
                    <input type="text" name="charge[]" class="form-control-lg" disabled>
                </td> 
            </tr>
        <?php } ?>
    </tbody>

    <script>

        // if checkbox is checked, enable the input field
        $('.report-item-checkbox').on('change', function() {
            if($(this).is(':checked')) {
                $(this).closest('tr').find('input[type="text"]').prop('disabled', false);
                // textarea
                $(this).closest('tr').find('textarea').prop('disabled', false);
            } else {
                $(this).closest('tr').find('input[type="text"]').prop('disabled', true);
                // textarea
                $(this).closest('tr').find('textarea').prop('disabled', true);
            }
        });


        </script>
    

</table>
