<div class="form-group">
    <label
        class="control-label<?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
        for="<?= $partId ?>"
    ><?= $label ?></label>

    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <?php var_dump($params) ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <input id="<?= $partId ?>"
               type="checkbox"
               class="checkbox <?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $name ?>"
               <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
               <?php if (!empty($params[$name])) { ?>checked="checked" <?php } ?>
               data-for="<?= $widgetId ?>"
               data-name="<?= $name ?>"
               data-params='<?= $dataParams ?>'
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
        >
        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
</div>