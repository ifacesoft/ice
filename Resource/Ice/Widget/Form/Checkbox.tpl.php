<div class="form-group">
    <label
        class="control-label<?php if (isset($options['horizontal'])) : ?> col-md-<?= $options['horizontal'] ?><?php endif; ?>"
        for="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
    ><?= $options['label'] ?></label>

    <?php if (isset($options['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <input id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
               type="checkbox"
               class="checkbox <?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $name ?>"
               <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
               <?php if (isset($params[$name])) { ?>checked="checked" <?php } ?>
               data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
               data-name="<?= $name ?>"
               data-params='<?= $dataParams ?>'
               <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
            <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
        <?php if (isset($options['horizontal'])) : ?></div><?php endif; ?>
</div>