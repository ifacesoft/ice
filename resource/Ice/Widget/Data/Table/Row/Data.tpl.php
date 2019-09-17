<tr>
    <td rowspan="<?= ceil(count($columns) / $columnCount) ?>"><?= $id ?></td>
    <?php
    $count = 0;
    foreach ($columns as $columnName => $column) :
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
        <?= $rowResult[$columnName] ?>
    </td>
    <?php endforeach; ?>
</tr>
