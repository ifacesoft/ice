<div class="form-group">
    <label for="<?= $formName . '_' . $fieldName ?>"><?= $title ?></label>
    <textarea class="form-control" id="<?= $formName . '_' . $fieldName ?>"
              placeholder="<?= $options['placeholder'] ?>" name="<?= $fieldName ?>"
              style="width: 100%;" rows="4"
              <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
              data-url='<?= $dataUrl ?>'
              data-json='<?= $dataJson ?>'
              data-action='<?= $dataAction ?>'
              data-block='<?= $dataBlock ?>'><?= $value ?></textarea
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
</div>

