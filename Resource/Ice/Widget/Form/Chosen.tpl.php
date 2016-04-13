<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select id="<?= $component->getPartId() ?>"
                class="<?= $component->getComponentName() ?><?php if (!$component->getOption('resetFormClass')) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $component->getName() ?>"
                data-for="<?= $component->getWidgetId() ?>"
                data-name="<?= $component->getName() ?>"
                data-params='<?= $component->getDataParams() ?>'
            <?= $component->getPlaceholderAttribute() ?>
                <?php if (!empty($options['multiple'])) : ?>multiple<?php endif; ?>
                <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
                <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>>
            <?php foreach ($options['rows'] as $option) : ?>
                <option value="<?= htmlentities($option[$name], ENT_QUOTES) ?>"
                    <?php if ($params[$name] == $option[$value]) : ?> selected="selected"<?php endif; ?>
                ><?= $option[$label] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getPartId() ?>").chosen({dateFormat: 'yy-mm-dd'});
        });
    </script>
</div>
