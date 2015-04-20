<ul class="pagination <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
    <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>>

    <?php foreach ($items as $item) : ?>
        <?= $item ?>
    <?php endforeach; ?>
</ul>
