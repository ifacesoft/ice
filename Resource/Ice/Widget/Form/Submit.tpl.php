<div <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <?php if (!empty($options['horizontal'])) : ?>
    <div class="col-md-<?= $options['horizontal'] ?>">&nbsp;</div>
    <div class="col-md-<?= 12 - $options['horizontal'] ?>"><?php endif; ?>
        <button id="<?= $partId ?>"
                class="btn <?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
                data-name="<?= $name ?>"
                data-params='<?= $dataParams ?>'
                data-for="<?= $widgetId ?>"
                type="submit"><?= $options['label'] ?></button>
        <?php if (!empty($options['horizontal'])) : ?></div><?php endif; ?>
</div>