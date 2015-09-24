<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>" class="sr-only"><?= $options['label'] ?></label>
    <input type="password" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
           placeholder="<?= $options['placeholder'] ?>"
           name="<?= $fieldName ?>" value="<?= $value ?>"
           <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
           data-url='<?= $dataUrl ?>'
           data-params='<?= $dataParams ?>'
           data-action='<?= $dataAction ?>'
           data-view='<?= $dataView ?>'
           data-widget='<?= $dataWidget ?>'
           data-token="<?= $dataToken ?>"
           data-for="<?= $dataFor ?>"
    >
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>
           required>
</div>
