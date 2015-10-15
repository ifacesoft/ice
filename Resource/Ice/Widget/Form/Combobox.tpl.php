<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $partId ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($options['horizontal'])) : ?> col-md-<?= $options['horizontal'] ?><?php endif; ?>"
    ><?= $options['label'] ?></label>

    <?php if (!empty($options['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <select id="<?= $widgetClassName . '_' . $widgetName . '_' . $name ?>"
                class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $name ?><?php if (!empty($options['multiple'])) : ?>[]<?php endif; ?>"
                data-for="<?= $widgetId ?>"
                data-name="<?= $name ?>"
                data-params='<?= $dataParams ?>'
                <?php if (!empty($options['multiple'])) : ?>multiple="multiple"<?php endif; ?>
                <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
                <?php if (!empty($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>"<?php endif; ?>
                <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
                <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
                <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
                <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
            <?php foreach ($options['rows'] as $row) : ?>
                <option value="<?= $row[$value] ?>"
                    <?php if ($params[$name] == $row[$value]) : ?> selected="selected"<?php endif; ?>
                ><?= $row[$label] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($options['horizontal'])) : ?></div><?php endif; ?>
</div>
