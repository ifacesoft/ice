<button id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
        data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        data-for="<?= $widgetId ?>"
        type="<?php if (isset($options['submit'])) : ?>submit<?php else : ?>button<?php endif; ?>"><?= $label ?></button>
