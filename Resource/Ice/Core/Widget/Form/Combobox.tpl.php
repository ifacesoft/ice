<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
     <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <label
        for="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
        <?php if (isset($options['srOnly'])) : ?>class="sr-only"<?php endif; ?>><?= $title ?></label>
    <select id="<?= $widgetClassName . '_' . $token . '_' . $name ?>"
            class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
            name="<?= $name ?>"
            data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
            data-name="<?= $name ?>"
            data-params='<?= $dataParams ?>'
            <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?> return false;"<?php endif; ?>
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>>
        <?php foreach ($options['items'] as $option => $title) : ?>
            <option value="<?= $option ?>"
                <?php if ($value == $option) : ?> selected="selected"<?php endif; ?>
                ><?= $title ?></option>
        <?php endforeach; ?>
    </select>
    </label>
</div>
