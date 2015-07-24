<?php if (!empty($header)) : ?><h3><?= $header ?></h3><?php endif; ?>
<?php if (!empty($description)) : ?><h5><?= $description ?></h5><?php endif; ?>
<table class="table <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
       <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>>
    <?= $headerRow ?>
    <?php foreach ($rows as $row) : ?>
        <?= $row ?>
    <?php endforeach; ?>
</table>
