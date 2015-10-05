<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
        class="control-label<?php if (isset($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (isset($options['horizontal'])) : ?> col-md-<?= $options['horizontal'] ?><?php endif; ?>"
    ><?= $options['label'] ?></label>

    <?php if (isset($options['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <select id="<?= $widgetClassName . '_' . $widgetName . '_' . $name ?>"
                class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $name ?><?php if (isset($options['multiple'])) : ?>[]<?php endif; ?>"
                data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
                data-name="<?= $name ?>"
                data-params='<?= $dataParams ?>'
                <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
                <?php if (isset($options['multiple'])) : ?>multiple="multiple"<?php endif; ?>
                <?php if (isset($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
                <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>"<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
            <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>
            <?php if ($options['required']) : ?> required="required" <?php endif; ?>
            <?php if ($options['autofocus']) : ?> autofocus="autofocus" <?php endif; ?>>
            <?php foreach ($options['rows'] as $row) : ?>
                <option value="<?= $row[$value] ?>"
                    <?php if ($params[$name] == $row[$value]) : ?> selected="selected"<?php endif; ?>
                ><?= $row[$label] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($options['horizontal'])) : ?></div><?php endif; ?>
</div>
