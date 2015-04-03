<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <select class="form-control" id="<?= $formName . '_' . $fieldName ?>" name="<?= $fieldName ?>" style="width: 100%;"
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
        <?php foreach ($options['items'] as $option => $title) : ?>
            <option
                value="<?= $option ?>"<?php if ($value == $option) : ?> selected="selected"<?php endif; ?>>
                <?= $title ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
