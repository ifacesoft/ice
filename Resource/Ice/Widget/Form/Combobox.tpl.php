<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <select id="<?= $component->getPartId() ?>"
                class="<?= $component->getComponentName() ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $component->getName() ?><?php if (!empty($options['multiple'])) : ?>[]<?php endif; ?>"
                data-for="<?= $component->getWidgetId() ?>"
                data-name="<?= $component->getName() ?>"
                data-params='<?= $component->getDataParams() ?>'
                <?php if (!empty($options['multiple'])) : ?>multiple="multiple"<?php endif; ?>
                <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
                <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
                <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
                <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
                <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
                <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
            <?php foreach ($options['rows'] as $option) : ?>
                <option value="<?= htmlentities($option[$name], ENT_QUOTES) ?>"
                    <?php if ($params[$name] == $option[$value]) : ?> selected="selected"<?php endif; ?>
                ><?= \Ice\Helper\String::truncate(implode(', ', array_intersect_key($option, array_flip((array)$title))), isset($options['truncate']) ? $options['truncate'] : 100) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
</div>
