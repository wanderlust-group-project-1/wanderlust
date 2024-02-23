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
                    <td><?= $item->status ?></td>
                    <td><?= $item->upcoming_rent_count ?></td>
                    <!-- Actions -->
                    <td> 
                        <button id="equipment-item" class="btn btn-primary" data-id="<?= $item->id ?>">Manage</button>
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
        <h2>Change Item Status</h2>

        <div id="item-actions">
            <button id="make-unavailable-t" class="btn btn-danger">Make Unavailable Temporarily</button>
            <button id="make-unavailable-p" class="btn btn-danger">Make Unavailable Permanently</button>
            <button id="make-available" class="btn btn-success">Make Available</button>

        </div>
        
        <!-- if available -->
    </div>
</div>

<!-- End of Modal -->

