<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <input type="text" class="form-control" id="<?= $formName . '_' . $fieldName ?>" placeholder="<?= $placeholder ?>"
           name="<?= $fieldName ?>" value="<?= $value ?>" style="width: 100%;">
    <script>
        $(function () {
            $("#<?= $formName . '_' . $fieldName ?>").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>
</div>
