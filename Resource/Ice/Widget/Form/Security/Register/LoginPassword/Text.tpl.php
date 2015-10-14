<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>" class="sr-only"><?= $options['label'] ?></label>
    <input type="text" class="form-control" id="<?= $formName . '_' . $fieldName ?>"
           placeholder="<?= $options['placeholder'] ?>"
           name="<?= $fieldName ?>" value="<?= $value ?>"
           <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
           data-params='<?= $dataParams ?>'
           <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
           data-widget='<?= $dataWidget ?>'
           data-for="<?= $parentWidgetId ?>"
    >
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>
           required autofocus>
</div>
