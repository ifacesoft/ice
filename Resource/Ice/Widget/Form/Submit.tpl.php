<div <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <?php if (isset($options['horizontal'])) : ?>
    <div class="col-md-<?= $options['horizontal'] ?>">&nbsp;</div>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <button id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
            class="btn <?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
            <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?> return false;"<?php endif; ?>
            data-name="<?= $name ?>"
            data-params='<?= $dataParams ?>'
            data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
            type="submit"><?= $options['label'] ?></button>
        <?php if (isset($options['horizontal'])) : ?></div><?php endif; ?>
</div>