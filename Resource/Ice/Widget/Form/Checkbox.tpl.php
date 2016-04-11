<div class="form-group">
    <label
        class="<?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= 12 - $widgetOptions['horizontal'] ?> col-md-offset-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
        for="<?= $component->getPartId() ?>">
        <div class="checkbox" style="margin-left: 20px;">
            <input type="hidden" id="<?= $component->getPartId() . '_hidden' ?>" name="<?= $component->getName() ?>" value="0"/>
            <input id="<?= $component->getPartId() ?>"
                   type="checkbox"
                   class="<?= $component->getComponentName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                   name="<?= $component->getName() ?>"
                   value="1"
                   <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
                   <?php if ($component->getValue()) : ?>checked="checked"<?php endif; ?>
                   data-for="<?= $component->getWidgetId() ?>"
                   data-name="<?= $component->getName() ?>"
                <?= $component->getEventAttributesCode() ?>
                   <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
                   <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
            />
            <?= $component->getLabel() ?>
        </div>
    </label>
</div>