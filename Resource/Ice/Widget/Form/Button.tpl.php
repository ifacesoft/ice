<button id="<?= $partId ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
        data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        data-for="<?= $widgetId ?>"
        type="<?php if (!empty($options['submit'])) : ?>submit<?php else : ?>button<?php endif; ?>"><?= $label ?></button>
