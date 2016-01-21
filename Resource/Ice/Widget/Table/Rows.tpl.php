<?php if (count($result) == 1) : ?>
    <?php
    $parts = reset($result);
    $part = reset($parts);

    if ($part['empty']) {
        return;
    }
    ?>
<?php endif; ?>

<tbody id="<?= $widgetId ?>"
       class="<?= $widgetClass ?>"
       data-widget='<?= $dataWidget ?>'
       data-params='<?= $dataParams ?>'
       data-for="<?= $parentWidgetId ?>"
>
<?php foreach ($result as $offset => $parts) : ?>
    <tr class="primary_row">
    <?php if ($isShowCount) : ?>
        <td rowspan="<?= $columnCount ? ceil(count($parts) / $columnCount) : 1 ?>"><?= $offset ?></td><?php endif; ?>
    <?php
    $count = 0;
    foreach ($parts as $columnName => $part) :
        $colspan = isset($part['options']['colspan']) ? $part['options']['colspan'] : 1;
        $count += $colspan;
        if ($count <= $columnCount) : ?>
            <td<?php if (isset($part['options']['rowspan'])) : ?> rowspan="<?= $part['options']['rowspan'] ?>"<?php endif;
            ?><?php if (isset($part['options']['colspan'])) : ?> colspan="<?= $part['options']['colspan'] ?>"<?php endif;
            ?>>
        <?php else :
            $count = 1
            ?>
            </tr>
            <tr class="secondary_row">
            <td<?php if (isset($part['options']['rowspan'])) : ?> rowspan="<?= $part['options']['rowspan'] ?>"<?php endif;
            ?><?php if (isset($part['options']['colspan'])) : ?> colspan="<?= $part['options']['colspan'] ?>"<?php endif;
            ?>>
        <?php endif; ?>
        <?= $widget->renderPart($part) ?>
        </td>
    <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</tbody>
