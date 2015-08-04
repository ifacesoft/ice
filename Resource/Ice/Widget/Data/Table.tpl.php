<?php if (!empty($header)) : ?><h3><?= $header ?></h3><?php endif; ?>
<?php if (!empty($description)) : ?><h5><?= $description ?></h5><?php endif; ?>
<table class="table <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>"
       <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>>
    <?= $headerRow ?>
    <tr>
        <?php foreach ($topRow as $key => $column) : ?>
            <?php if (is_string($key)) : ?>
                <th colspan="<?= $column ?>"><?= $key ?></th>
            <?php else : ?>
                <th><?= $column ?></th>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($rows as $row) : ?>
        <?= $row ?>
    <?php endforeach; ?>
    <tr>
        <?php foreach ($bottomRow as $key => $column) : ?>
            <?php if (is_string($key)) : ?>
                <th colspan="<?= $column ?>"><?= $key ?></th>
            <?php else : ?>
                <th><?= $column ?></th>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>
</table>
