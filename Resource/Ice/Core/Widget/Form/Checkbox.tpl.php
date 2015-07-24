<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>" class="checkbox">
    <label>
        <input type="checkbox"
               <?php if (!empty($options['classes'])) : ?>class="<?= $options['classes'] ?>"<?php endif; ?>
               name="<?= $name ?>"
               <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
               <?php if ($value) { ?>checked="checked" <?php } ?>
               data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
            <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
        <?= $title ?>
    </label>
</div>
