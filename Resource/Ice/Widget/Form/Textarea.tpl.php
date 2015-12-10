<div <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <label
        for="<?= $partId ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"><?= $label ?></label>
    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <textarea
            class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
            id="<?= $partId ?>"
            <?php if (!empty($options['placeholder'])) : ?> placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
            name="<?= $name ?>"
            rows="4"
            data-params='<?= $dataParams ?>'
            data-for="<?= $widgetId ?>"
            <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
            <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
            <?php if (!empty($options['readonly'])) : ?>readonly="readonly"<?php endif; ?>
            <?php if (!empty($options['required'])) : ?>required="required"<?php endif; ?>
        ><?= $value ?></textarea>
        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
</div>

