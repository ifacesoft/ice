<button id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
        class="btn <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
        data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
        data-name="<?= $component->getName() ?>"
        data-params='<?= $component->getDataParams() ?>'
        data-for="<?= $component->getWidgetId() ?>"
        <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
        type="<?php if (isset($options['submit'])) : ?>submit<?php else : ?>button<?php endif; ?>"><?= $component->getLabel() ?></button>
