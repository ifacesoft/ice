<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>" class="sr-only"><?= $label ?></label>
    <input type="text" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
           placeholder="<?= $options['placeholder'] ?>"
           name="<?= $fieldName ?>" value="<?= $value ?>"
           <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
           data-params='<?= $dataParams ?>'
           data-widget='<?= $dataWidget ?>'
           data-for="<?= $parentWidgetId ?>"
           <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
           <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
           <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
           autofocus
    >
</div>
