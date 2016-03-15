<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $partId ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
    ><?= $label ?></label>
    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <select id="<?= $partId ?>"
                class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $name ?><?php if (!empty($options['multiple'])) : ?>[]<?php endif; ?>"
                data-for="<?= $widgetId ?>"
                data-name="<?= $name ?>"
                data-params='<?= $dataParams ?>'
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
