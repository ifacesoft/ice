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
                   <?php if (!empty($params[$name])) { ?>checked="checked"<?php } ?>
                   data-for="<?= $component->getWidgetId() ?>"
                   data-name="<?= $component->getName() ?>"
                   data-params='<?= $component->getDataParams() ?>'
                   <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
                   <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
            />
            <?= htmlentities($label, ENT_QUOTES) ?>
        </div>
    </label>
</div>