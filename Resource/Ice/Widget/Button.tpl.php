<button id="<?= $partId ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
        <?php if (isset($options['dataAction'])) : ?>data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
        data-for="<?= $widgetId ?>"
        type="button"><?= $options['label'] ?></button>
