<tbody id="<?= $widgetId ?>"
       class="<?= $widgetClass ?>"
       data-widget='<?= $dataWidget ?>'
       data-params='<?= $dataParams ?>'
       data-for="<?= $parentWidgetId ?>"
>
<?php foreach ($result as $offset => $row) : ?>
    <tr class="primary_row">
    <?php if ($isShowCount) : ?>
        <td rowspan="<?= $columnCount ? ceil(count($row) / $columnCount) : 1 ?>"><?= $offset ?></td><?php endif; ?>
    <?php
    $count = 0;
    foreach ($row as $columnName => $component) :
        $colspan = $component->getOption('colspan') ? $component->getOption('colspan') : 1;
        $count += $colspan;
        if ($count <= $columnCount) : ?>
            <td class="<?= $component->getComponentName() ?>"<?php if ($component->getOption('rowspan')) : ?> rowspan="<?= $component->getOption('rowspan') ?>"<?php endif;
            ?><?php if ($component->getOption('colspan')) : ?> colspan="<?= $component->getOption('colspan') ?>"<?php endif;
            ?>><?php else : $count = $colspan ?>
            </tr>
            <tr class="secondary_row">
            <td class="<?= $component->getComponentName() ?>"<?php if ($component->getOption('rowspan')) : ?> rowspan="<?= $component->getOption('rowspan') ?>"<?php endif;
            ?><?php if ($component->getOption('colspan')) : ?> colspan="<?= $component->getOption('colspan') ?>"<?php endif;
            ?>><?php endif; ?><?= $component->render() ?></td>

    <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</tbody>
