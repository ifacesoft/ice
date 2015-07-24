<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>" class="form-group">
    <label>
        <?= $title ?>
        <select <?php if (!empty($options['classes'])) : ?>class="<?= $options['classes'] ?>"<?php endif; ?>
                name="<?= $name ?>"
                data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
                <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?> return false;"<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
            <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
            <?php foreach ($options['items'] as $option => $title) : ?>
                <option value="<?= $option ?>"
                    <?php if ($value == $option) : ?> selected="selected"<?php endif; ?>
                    ><?= $title ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</div>
