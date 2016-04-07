<div class="form-group">
    <label
        class="<?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= 12 - $widgetOptions['horizontal'] ?> col-md-offset-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
        for="<?= $partId ?>">
        <div class="checkbox" style="margin-left: 20px;">
            <input type="hidden" id="<?= $partId . '_hidden' ?>" name="<?= $name ?>" value="0"/>
            <input id="<?= $partId ?>"
                   type="checkbox"
                   class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                   name="<?= $name ?>"
                   value="1"
                   <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
                   <?php if (!empty($params[$name])) { ?>checked="checked"<?php } ?>
                   data-for="<?= $widgetId ?>"
                   data-name="<?= $name ?>"
                   data-params='<?= $dataParams ?>'
                   <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
                   <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
            />
            <?= htmlentities($label, ENT_QUOTES) ?>
        </div>
    </label>
</div>