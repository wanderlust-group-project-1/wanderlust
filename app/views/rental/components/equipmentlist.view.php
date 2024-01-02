<div class="table-container">

    <table class="data-table">
        <thead>
            <tr>
                <th>Equipment Name</th>
                <th>Type</th>
                <th>Cost</th>
                <th>Count</th>
                <th>Action</th> <!-- Added Action Column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $equipment): ?>
                <tr>
                    <td data-label="Equipment Name"><?= htmlspecialchars($equipment->name) ?></td>
                    <td data-label="Type"><?= htmlspecialchars($equipment->type) ?></td>
                    <td data-label="Cost">$<?= htmlspecialchars($equipment->fee) ?></td>
                    <td data-label="Count"><?= htmlspecialchars($equipment->count) ?></td>
                    <td data-label="Action"><button class="view-button">View</button></td> <!-- View Button for each row -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
