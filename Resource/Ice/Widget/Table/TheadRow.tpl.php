<?php if ($columns = $component->getOption('row', [])) : ?>
    <thead>
    <tr>
        <?php foreach ($columns as $key => $column) : ?>
            <?php if (is_string($key)) : ?>
                <?php if (is_array($column)) : ?>
                    <?php
                    $options = $column;

                    $column = isset($options['colspan']) ? $options['colspan'] : 1;
                    ?>
                <?php endif; ?>

                <th colspan="<?= $column ?>"><?= $key ?></th>
            <?php else : ?>
                <th><?php if ($column instanceof \Ice\Core\Widget || $column instanceof \Ice\Core\WidgetComponent) : ?><?= $column->render() ?><?php else : ?><?= $column ?><?php endif; ?></th>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>
    </thead>
<?php endif; ?>