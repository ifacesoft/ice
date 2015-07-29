<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
     <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <label
        for="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
        <?php if (isset($options['srOnly'])) : ?>class="sr-only"<?php endif; ?>><?= $title ?></label>
    <textarea
        class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        id="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
        <?php if (isset($options['placeholder'])) : ?> placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
        name="<?= $name ?>"
        style="width: 100%;" rows="4"
        data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
        <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
        ><?= $value ?></textarea
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
</div>

