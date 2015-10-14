<button id="<?= $partId ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
        <?php if (isset($options['dataAction'])) : ?>data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
        <?php if (isset($options['viewClass'])) : ?>data-view="<?= $options['viewClass'] ?>"<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
        data-for="<?= $widgetId ?>"
        type="<?php if (!empty($options['submit'])) : ?>submit<?php else : ?>button<?php endif; ?>"><?= $options['label'] ?></button>
