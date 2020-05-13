<thead>
<tr>
    <?php foreach ($component->getOption('row', []) as $key => $column) : ?>
        <?php if (is_string($key)) : ?>
            <?php if (is_array($column)) : ?>
                <?php
                $options = $column;

                $column = $component->getOption('colspan', 1);
                ?>
            <?php endif; ?>

            <td colspan="<?= $column ?>"><?= $key ?></td>
        <?php else : ?>
            <th><?= $column ?></th>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
</thead>