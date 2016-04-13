<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select id="<?= $component->getPartId() ?>"
                class="<?= $component->getComponentName() ?><?php if (!$component->getOption('resetFormClass')) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $component->getName() ?><?php if (!empty($options['multiple'])) : ?>[]<?php endif; ?>"
                data-for="<?= $component->getWidgetId() ?>"
                data-name="<?= $component->getName() ?>"
            <?= $component->getEventAttributesCode() ?>
                <?php if (!empty($options['multiple'])) : ?>multiple="multiple"<?php endif; ?>
                <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
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
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
