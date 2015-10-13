<p
    id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
    data-name="<?= $name ?>"
    data-params='<?= $dataParams ?>'
    <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
    data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"><?= $options['label'] ?></p>