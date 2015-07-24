<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>" class="form-group">
    <label for="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>" class="sr-only"><?= $title ?></label>
    <input type="text"
           class="form-control<?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
           id="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
           placeholder="<?= $options['placeholder'] ?>"
           name="<?= $name ?>" value="<?= $params[$name] ?>"
           data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
           <?php if (isset($onchange)) : ?>onchange="<?= $onchange ?> return false"<?php endif; ?>
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>
        <?php if ($options['required']) : ?> required="required" <?php endif; ?>
        <?php if ($options['autofocus']) : ?> autofocus="autofocus" <?php endif; ?>>
</div>
