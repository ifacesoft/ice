<thead>
<tr>
    <?php if ($component->getWidget()->isShowCount()) : ?>
    <th rowspan="<?= $component->getWidget()->getColumnCount() ? ceil(count($component->getWidget()->getParts()) / $component->getWidget()->getColumnCount()) : 1 ?>">#</th><?php endif; ?>
    <?php
    $count = 0;

    foreach ($component->getWidget()->getParts() as $name => $column) : ?>
    <?php
    $colspan = $column->getOption('colspan') ? $column->getOption('colspan') : 1;
    $count += $colspan;
    ?>
    <?php if ($count <= $component->getWidget()->getColumnCount()) : ?>
    <th<?php if ($column->getOption('rowspan')) : ?> rowspan="<?= $column->getOption('rowspan') ?>"<?php endif;
    ?><?php if ($column->getOption('colspan')) : ?> colspan="<?= $column->getOption('colspan') ?>"<?php endif;
    ?>><?php else : $count = 1 ?></tr>
<tr>
    <th<?php if ($column->getOption('rowspan')) : ?> rowspan="<?= $column->getOption('rowspan') ?>"<?php endif;
    ?><?php if ($column->getOption('colspan')) : ?> colspan="<?= $column->getOption('colspan') ?>"<?php endif;
    ?>><?php endif; ?><?= $column->getLabel() ?><?php if ($column->getOption('sortable')) : ?>
            <a href="<?= $column['href'] ?>" onclick='<?= $column['onclick'] ?>'
               data-widget='<?= $dataWidget ?>'
               data-for="<?= $parentWidgetId ?>"
               data-name='<?= $column->getName() ?>'
               data-params='<?= $column['dataParams'] ?>'
               class="btn btn-default btn-sm<?php if ($column['dataValue']) : ?> active<?php endif; ?>">
                <?php if ($column['dataValue'] == 'ASC') : ?>
                    &darr;
                <?php elseif ($column['dataValue'] == 'DESC') : ?>
                    &uarr;
                <?php else : ?>
                    &darr;&uarr;
                <?php endif; ?>
            </a><?php endif; ?></th>
    <?php endforeach; ?>
</tr>
</thead>
