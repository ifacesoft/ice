<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>" class="form-group">
    <label for="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"><?= $title ?></label>
    <textarea class="form-control" id="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
              placeholder="<?= $options['placeholder'] ?>" name="<?= $name ?>"
              style="width: 100%;" rows="4"
              data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
              <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
        ><?= $value ?></textarea
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
</div>

