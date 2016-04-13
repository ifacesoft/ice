<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <input type="number"
               class="<?= $component->getComponentName() ?><?php if (!$component->getOption('resetFormClass')) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               id="<?= $component->getPartId() ?>"
               name="<?= $component->getName() ?>"
               value="<?= $component->getValue() ?>"
            <?= $component->getPlaceholderAttribute() ?>
               data-for="<?= $component->getWidgetId() ?>"
               <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
        >
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
