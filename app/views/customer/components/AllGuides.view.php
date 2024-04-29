<?php foreach ($guides as $guide) : ?>
    <div class="info-data">
        <div data-id="<?= htmlspecialchars($guide->guide_id) ?>" class="guide-card-new">
            <div class="guide-card__image">
                <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
            </div>
            <div class="guide-card__info">
                <p><?= htmlspecialchars($guide->guide_name) ?></p>
                <p><?= htmlspecialchars($guide->languages) ?></p>
                <p><?= htmlspecialchars($guide->places) ?></p>
                <a href="<?= ROOT_DIR ?>/FindGuide/viewGuide/<?= htmlspecialchars($guide->guide_id) ?>/<?= htmlspecialchars($guide->package_ids) ?>">
                    <button class="btn-edit" id="book-guide-button">View Guide</button>
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>


