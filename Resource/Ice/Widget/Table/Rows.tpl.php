<tbody id="<?= $widgetId ?>"
       class="<?= $widgetClass ?>"
       data-widget='<?= $dataWidget ?>'
       data-for="<?= $parentWidgetId ?>"
>
<?php foreach ($result as $offset => $parts) : ?>
    <tr class="primary_row">
    <?php if ($isShowCount) : ?>
        <td rowspan="<?= ceil(count($parts) / $columnCount) ?>"><?= $offset ?></td><?php endif; ?>
    <?php
    $count = 0;
    foreach ($parts as $columnName => $column) :
        $colspan = isset($column['options']['colspan']) ? $column['options']['colspan'] : 1;
        $count += $colspan;
        if ($count <= $columnCount) : ?>
            <td<?php if (isset($column['options']['rowspan'])) : ?> rowspan="<?= $column['options']['rowspan'] ?>"<?php endif;
            ?><?php if (isset($column['options']['colspan'])) : ?> colspan="<?= $column['options']['colspan'] ?>"<?php endif;
            ?>>
        <?php else :
            $count = 1
            ?>
            </tr>
            <tr class="secondary_row">
            <td<?php if (isset($column['options']['rowspan'])) : ?> rowspan="<?= $column['options']['rowspan'] ?>"<?php endif;
            ?><?php if (isset($column['options']['colspan'])) : ?> colspan="<?= $column['options']['colspan'] ?>"<?php endif;
            ?>>
        <?php endif; ?>
        <?= $column['content']; ?>
        </td>
    <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</tbody>
