<button id="<?= $component->getPartId() ?>"
        class="btn <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
        data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
        data-name="<?= $component->getName() ?>"
        data-params='<?= $component->getParams() ?>'
        data-for="<?= $component->getWidgetId() ?>"
        type="button"><?= $component->getLabel() ?></button>
