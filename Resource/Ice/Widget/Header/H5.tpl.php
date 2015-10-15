<h5
    id="<?= $partId ?>"
    class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
    data-name="<?= $name ?>"
    data-params='<?= $dataParams ?>'
    data-for="<?= $widgetId ?>"
><?= $options['label'] ?></h5>