<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
        class="control-label<?php if (isset($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (isset($options['horizontal'])) : ?> col-md-<?= $options['horizontal'] ?><?php endif; ?>"
    ><?= $options['label'] ?></label>

    <?php if (isset($options['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <select id="<?= $widgetClassName . '_' . $widgetName . '_' . $name ?>"
                class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $name ?>"
                data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
                data-name="<?= $name ?>"
                data-params='<?= $dataParams ?>'
                <?php if (isset($options['multiple'])) : ?>multiple size="<?= $options['multiple'] ?>"<?php endif; ?>
                <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?> return false;"<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>>
            <?php foreach ($options['rows'] as $option) : ?>
                <option value="<?= $option[$value] ?>"
                    <?php if ($params[$name] == $option[$value]) : ?> selected="selected"<?php endif; ?>
                ><?= $option[$label] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($options['horizontal'])) : ?></div><?php endif; ?>
</div>
