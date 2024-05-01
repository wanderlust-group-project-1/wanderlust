<!-- <?php
show($items);
?> -->

<!-- Table -->

<div class="table-responsive">
    <table class="table table-hover table-custom">
        <thead>
            <tr>
                <th>Number</th>
                <th> Status </th>
                <th> Upcoming booking count </th>
                <th> Actions </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) : ?>
                <tr>
                    <td><?= $item->item_number ?></td>
                    <td><?= ucfirst($item->status) ?></td>
                    <td><?= $item->upcoming_rent_count ?></td>
                    <!-- Actions -->
                    <td> 
                        <button id="equipment-item" class="btn-text-blue" data-id="<?= $item->id ?>" data-status="<?= $item->status ?>" data-number="<?= $item->item_number ?>" data-count = "<?= $item->upcoming_rent_count ?>"
                         > <i class="fa fa-tasks" aria-hidden="true"></i>  Manage</button>
                    </td>
                         
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- End of Table -->

<!-- Modal  -->
        
<!-- Change item status -->
<div id="change-item-status-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <div class="flex-d-c justify-content-center text-center gap-3" >
            
        <h2>Change Item Status</h2>

        <p>Item Number: <span id="item-number"></span></p>

        <div id="item-actions">
            <button id="make-unavailable-t" class="btn-text-orange border">Make Unavailable Temporarily</button>
            <button id="make-unavailable-p" class="btn-text-red border"  data-tooltip="This action cannot be undone." >Make Unavailable Permanently</button>
            <button id="make-available" class="btn-text-green border">Make Available</button>

        </div>

        </div>
        
        <!-- if available -->
    </div>
</div>

<!-- End of Modal -->

