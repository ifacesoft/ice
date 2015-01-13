<div class="checkbox">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <input type="checkbox" class="form-control"
           id="<?= $formName . '_' . $fieldName ?>" placeholder="<?= $placeholder ?>" name="<?= $fieldName ?>"
           <?php if ($value) { ?>checked="checked" <?php } ?>
        <?php if ($disabled) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($readonly) : ?> readonly="readonly" <?php endif; ?>>
</div>
