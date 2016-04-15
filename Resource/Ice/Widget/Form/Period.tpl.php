<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <input <?= $component->getIdAttribute('from') ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            type="text"
            name="<?= $component->getName() ?>_from"
            value="<?= isset($params[$name . '_from']) ? $params[$name . '_from'] : '' ?>"
            <?= $component->getPlaceholderAttribute() ?>
            <?= $component->getEventAttributesCode() ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
        <input <?= $component->getIdAttribute('to') ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            type="text"
            name="<?= $component->getName() ?>_to"
            value="<?= isset($params[$name . '_to']) ? $params[$name . '_to'] : '' ?>"
            <?= $component->getPlaceholderAttribute() ?>
            <?= $component->getEventAttributesCode() ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getId() ?>_from").datepicker({dateFormat: '<?= isset($options['dateFormat']) ? $options['dateFormat'] : 'yy-mm-dd' ?>'});
            $("#<?= $component->getId() ?>_to").datepicker({dateFormat: '<?= isset($options['dateFormat']) ? $options['dateFormat'] : 'yy-mm-dd' ?>'});
        });
    </script>
</div>
