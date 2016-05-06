<thead>
<tr>
    <?php foreach ($component->getOption('row', []) as $key => $column) : ?>
        <?php if (is_string($key)) : ?>
            <th colspan="<?= $column ?>"><?= $key ?></th>
        <?php else : ?>
            <th><?= $column ?></th>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
</thead>