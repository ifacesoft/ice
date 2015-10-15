<button id="<?= $partId ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        data-for="<?= $widgetId ?>"
        type="button"><?= $options['label'] ?></button>
