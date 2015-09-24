<tbody id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
       class="Widget_<?= $widgetClassName ?>"
       data-url='<?= $dataUrl ?>'
       data-action='<?= $dataAction ?>'
       data-view='<?= $dataView ?>'
       data-widget='<?= $dataWidget ?>'
       data-token="<?= $dataToken ?>"
       data-for="<?= $dataFor ?>"
>
<?php foreach ($result as $offset => $parts) : ?>
    <tr>
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
            <tr>
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
