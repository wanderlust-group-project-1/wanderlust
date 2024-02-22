<!-- <?php
show($items);
?> -->

<!-- Table -->

<div class="table-responsive">
    <table class="table table-hover">
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
                        <a href="<?= ROOT_DIR ?>/rental/rent/<?= $item->id ?>" class="btn btn-primary">Change</a>
                    </td>
                         
                </tr>
            <?php endforeach; ?>
        