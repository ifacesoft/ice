<thead>
<tr>
    <?php if ($options['widget']->isShowCount()) : ?>
    <th rowspan="<?= ceil(count($options['widget']->getParts()) / $options['widget']->getColumnCount()) ?>">
            #</th><?php endif; ?>
    <?php
    $count = 0;
    foreach ($options['widget']->getParts() as $name => $column) :
    $label = isset($column['options']['label']) ? $column['options']['label'] : $name;
    $colspan = isset($column['options']['colspan']) ? $column['options']['colspan'] : 1;
    $count += $colspan;
    if ($count <= $options['widget']->getColumnCount()) : ?>
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
        <?= isset($resource) && $resource instanceof Ice\Core\Resource ? $resource->get($label) : $label ?>
        <?php if (isset($column['options']['sortable']) && $column['options']['sortable'] === true) : ?>
            <a href="<?= $column['href'] ?>" onclick='<?= $column['onclick'] ?>'
               data-action='<?= $column['dataAction'] ?>'
               data-widget='<?= $dataWidget ?>'
               data-for="<?= $dataFor ?>"
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
</thead>
