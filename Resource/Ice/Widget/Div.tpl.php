<div
    id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
    data-name="<?= $name ?>"
    data-params='<?= $dataParams ?>'
    data-for="<?= $widgetId ?>"><?= $options['label'] ?></div>
