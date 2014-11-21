<?php foreach ($items as $position => $block): ?>
    <ul class="nav navbar-nav navbar-<?= $position ?>">
        <?php foreach ($block as $item): ?>
            <?= $item ?>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>