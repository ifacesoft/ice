<table class="table <?php if (!empty($classes)) { ?><?= $classes ?><?php } ?>">
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
