<tr>
    <th>#</th>
    <?php foreach ($columns as $column) : ?>
        <th>
            <?= $column['title'] ?>
            <?php if (isset($column['options']['sortable']) && $column['options']['sortable'] === true) : ?>
                <a href="<?= $column['href'] ?>" onclick='<?= $column['onclick'] ?>'
                   data-url='<?= $column['dataUrl'] ?>'
                   data-json='<?= $column['dataJson'] ?>'
                   data-action='<?= $column['dataAction'] ?>'
                   data-block='<?= $column['dataBlock'] ?>'
                   data-name='<?= $column['name'] ?>'
                   data-value='<?= $column['dataValue'] ?>'
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