<?php foreach ($guides as $guide) : ?>
    <div data-id="<?= htmlspecialchars($guide->guide_id) ?>" class="guide-card p-6">
        <div class="guide-card__image">
            <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
        </div>
        <div class="guide-card__info gap-3 p-5">
            <h3><?= htmlspecialchars($guide->guide_name) ?></h3>
            <p>Languages : <?= htmlspecialchars($guide->languages) ?></p>
            <p>Locations : <?= htmlspecialchars($guide->places) ?></p>
            <a href="<?= ROOT_DIR ?>/FindGuide/viewGuide/<?= htmlspecialchars($guide->guide_id) ?>/<?= htmlspecialchars($guide->package_ids) ?>">
                <button class="btn-text-blue mt-4" id="book-guide-button">View Guide</button>
            </a>
        </div>
    </div>
<?php endforeach; ?>


