<?php
$loopIndex = -1; // Initialize loop index
foreach ($packages as $package) :
    $loopIndex++; // Increment loop index
?>
    <div data-id="<?= htmlspecialchars($package->id) ?>" class="data">
        <div class="content-data">
            <div class="head">
                <h3 class="guide-topics">Package <?= $loopIndex + 1 ?></h3>
                <h3 class="guide-topics"><?= htmlspecialchars($package->price) ?></h3>
            </div>

            <div class="info-data mt-5">
                <?php
                $details = [
                    ['label' => 'Maximum Group Size', 'value' => htmlspecialchars($package->max_group_size)],
                    ['label' => 'Max Distance', 'value' => htmlspecialchars($package->max_distance)],
                    ['label' => 'Transport Needed', 'value' => $package->transport_needed ? 'Yes' : 'No'],
                    ['label' => 'Places', 'value' => htmlspecialchars($package->places)]
                ];
                ?>

                <?php foreach ($details as $detail) : ?>
                    <div class="guide-card-new">
                        <span class="label"><?= htmlspecialchars($detail['label']) ?></span>
                        <div class="booking-bar .flex-d mt-4 mb-2">
                            <p><?= htmlspecialchars($detail['value']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn-primary-lg rounded-6 edit-package mt-4" id="package-view-button" data-id="<?= htmlspecialchars($package->id) ?>">
                View Package
            </button>

        </div>
    </div>
    </div>
<?php endforeach; ?>


<!-- modal for view button

<div id="view-package-modal" class="view-package-modal modal">
    <div class="modal-content" id="package-modal-content">
        <span class="close-button">&times;</span>
        <h2>Packages Details</h2>
        <p><strong>Price:</strong> <span id="detail-price"></span></p>
        <p><strong>Maximum Group Size:</strong> <span id="detail-max-group-size"></span></p>
        <p><strong>Max Distance:</strong> <span id="detail-max-distance"></span></p>
        <p><strong>Transport Needed:</strong> <span id="detail-transport-needed"></span></p>
    </div>
</div> -->
