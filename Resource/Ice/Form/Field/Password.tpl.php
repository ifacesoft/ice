<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <input type="password" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
           placeholder="<?= $placeholder ?>"
           name="<?= $fieldName ?>" value="<?= $value ?>" style="width: 100%;"
        <?php if ($disabled) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($readonly) : ?> readonly="readonly" <?php endif; ?>>
</div>
