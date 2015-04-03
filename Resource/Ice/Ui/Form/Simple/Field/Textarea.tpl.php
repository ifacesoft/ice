<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <textarea type="text" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
              placeholder="<?= $options['placeholder'] ?>" name="<?= $fieldName ?>"
              style="width: 100%;" rows="4"><?= $value ?></textarea
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
</div>

