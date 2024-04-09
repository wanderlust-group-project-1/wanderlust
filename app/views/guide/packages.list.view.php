<?php 
$loopIndex = -1; // Initialize loop index
foreach ($packages as $package) : 
    $loopIndex++; // Increment loop index
?>
    <div data-id="<?= htmlspecialchars($package->id) ?>" class="data">
        <div class="content-data">
            <div class="head">
            <h3>Package <?= $loopIndex + 1 ?></h3>
                <h2><?= htmlspecialchars($package->price) ?></h2>
            </div>

            <div class="info-data mt-5">
                <?php
                $details = [
                    ['label' => 'Maximum Group Size', 'value' => htmlspecialchars($package->max_group_size) ],
                    ['label' => 'Max Distance', 'value' => htmlspecialchars($package->max_distance)],
                    ['label' => 'Transport Needed', 'value' => $package->transport_needed ? 'Yes' : 'No'],
                    ['label' => 'Places', 'value' => htmlspecialchars($package->places) ]
                ];
                ?>

                <?php foreach ($details as $detail) : ?>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2><?= htmlspecialchars($detail['value']) ?></h2>
                                <p><?= htmlspecialchars($detail['label']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card rounded-4">
                <button type="submit" class="btn-edit rounded-6 edit-package">
                    View Package
                </button>
            </div>
        </div>
    </div>
<?php endforeach; ?>