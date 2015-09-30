<button id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?> return false;"<?php endif; ?>
        <?php if (isset($options['actionClass'])) : ?>data-action="<?= $options['actionClass'] ?>"<?php endif; ?>
        <?php if (isset($options['viewClass'])) : ?>data-view="<?= $options['viewClass'] ?>"<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
        type="<?php if (!empty($options['submit'])) : ?>submit<?php else : ?>button<?php endif; ?>"><?= $options['label'] ?></button>
