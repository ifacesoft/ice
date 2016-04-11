<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= $widgetOptions['horizontal'] ?>">&nbsp;</div>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <button id="<?= $component->getPartId() ?>"
                class="btn <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
            <?= $component->getEventAttributesCode() ?>
                data-name="<?= $component->getName() ?>"
                data-for="<?= $component->getWidgetId() ?>"
                type="submit"><?= $component->getLabel() ?></button>
        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
</div>