<tr>
    <?php if ($isShowCount) : ?><th rowspan="<?= ceil(count($columns) / $columnCount) ?>">#</th><?php endif; ?>
    <?php
    $count = 0;
    foreach ($columns as $column) :
    $colspan = isset($column['options']['colspan']) ? $column['options']['colspan'] : 1;
    $count += $colspan;
    if ($count <= $columnCount) : ?>
    <th<?php if (isset($column['options']['rowspan'])) : ?> rowspan="<?= $column['options']['rowspan'] ?>"<?php endif;
    ?><?php if (isset($column['options']['colspan'])) : ?> colspan="<?= $column['options']['colspan'] ?>"<?php endif;
    ?>>
        <?php else :
        $count = 1
        ?>
</tr>
<tr>
    <th<?php if (isset($column['options']['rowspan'])) : ?> rowspan="<?= $column['options']['rowspan'] ?>"<?php endif;
    ?><?php if (isset($column['options']['colspan'])) : ?> colspan="<?= $column['options']['colspan'] ?>"<?php endif;
    ?>>
        <?php endif; ?>
        <?= $column['title'] ?>
        <?php if (isset($column['options']['sortable']) && $column['options']['sortable'] === true) : ?>
            <a href="<?= $column['href'] ?>" onclick='<?= $column['onclick'] ?>'
               data-action='<?= $column['dataAction'] ?>'
               data-widget='<?= $dataWidget ?>'
               data-for="<?= $parentWidgetId ?>"
               data-name='<?= $column['name'] ?>'
               data-params='<?= $column['dataParams'] ?>'
               class="btn btn-default btn-sm<?php if ($column['dataValue']) : ?> active<?php endif; ?>">
                <?php if ($column['dataValue'] == 'ASC') : ?>
                    &darr;
                <?php elseif ($column['dataValue'] == 'DESC') : ?>
                    &uarr;
                <?php else : ?>
                    &darr;&uarr;
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </th>
    <?php endforeach; ?>
</tr>