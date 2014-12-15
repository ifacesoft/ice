<div class="checkbox">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <input type="checkbox" class="form-control"
           id="<?= $formName . '_' . $fieldName ?>" placeholder="<?= $placeholder ?>" name="<?= $fieldName ?>"
           <?php if ($value) { ?>checked="checked" <?php } ?>>
</div>
