<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <textarea type="text" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
              placeholder="<?= $placeholder ?>" name="<?= $fieldName ?>"
              style="width: 100%;" rows="4"><?= $value ?></textarea>
</div>

