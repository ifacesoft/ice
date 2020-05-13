<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
            for="<?= $component->getId() ?>"
            class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"><?= $component->getLabel() ?><?php if ($component->getOption('required', false)) : ?><sup style="color: red;">*</sup><?php endif; ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <textarea <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            <?= $component->getPlaceholderAttribute() ?>
                name="<?= $component->getName() ?>"
                rows="4"
            <?= $component->getPlaceholderAttribute() ?>
            <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        ><?= $component->getValue() ?></textarea>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
<script>
    CKEDITOR.replace('<?= $component->getName() ?>', {
        filebrowserBrowseUrl: '/ice/ckeditor/browse'
    });

    CKEDITOR.config.image_previewText = ' ';

    for (var i in CKEDITOR.instances) {
        if (CKEDITOR.instances[i].name == '<?= $component->getId() ?>') {
            CKEDITOR.instances[i].on('change', function (evt) {
                $('#<?= $component->getId() ?>').val(evt.editor.getData());
            });
        }
    }
</script>