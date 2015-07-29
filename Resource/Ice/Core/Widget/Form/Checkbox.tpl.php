<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>" class="checkbox">
    <label>
        <input type="checkbox"
               class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $name ?>"
               <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
               <?php if (isset($params[$name])) { ?>checked="checked" <?php } ?>
               data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
               data-name="<?= $name ?>"
               data-params='<?= $dataParams ?>'
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
            <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
        <?= $title ?>
    </label>
</div>
