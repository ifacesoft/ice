<tr <?= $component->getHtmlTagAttributes() ?>>
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
            <td><?php if ($column instanceof \Ice\Core\Widget || $column instanceof \Ice\Core\WidgetComponent) : ?><?= $column->render() ?><?php else : ?><?= $column ?> <?php endif; ?></td>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>
