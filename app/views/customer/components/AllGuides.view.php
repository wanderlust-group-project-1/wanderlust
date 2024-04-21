<?php foreach ($guides as $guide) : ?>
    <div data-id="<?= htmlspecialchars($guide->guide_id) ?>" class="guide-card">
        <div class="guide-card__image">
            <img src="<?= ROOT_DIR ?>/assets/images/7.png" alt="guide">
        </div>
        <div class="guide-card__info">
            <h2><?= htmlspecialchars($guide->guide_id) ?></h2>
            <p><?= htmlspecialchars($guide->guide_name) ?></p>
            <p><?= htmlspecialchars($guide->languages) ?></p>
            <p><?= htmlspecialchars($guide->places) ?></p>
            <a href="<?= ROOT_DIR ?>/FindGuide/viewGuide/<?= htmlspecialchars($guide->guide_id) ?>">
                <button class="btn-edit" id="book-guide-button">Book Guide</button>
            </a>
        </div>
    </div>
<?php endforeach; ?>


