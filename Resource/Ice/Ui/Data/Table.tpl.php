<table class="table <?php if (!empty($classes)) { ?><?= implode(' ', $classes) ?><?php } ?>"
       <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>>
    <?php foreach ($rows as $row) : ?>
        <?= $row ?>
    <?php endforeach; ?>
</table>