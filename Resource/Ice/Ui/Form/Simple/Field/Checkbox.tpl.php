<div class="checkbox">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <input type="checkbox" class="form-control"
           id="<?= $formName . '_' . $fieldName ?>" placeholder="<?= $options['placeholder'] ?>" name="<?= $fieldName ?>"
           <?php if ($value) { ?>checked="checked" <?php } ?>
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
</div>
