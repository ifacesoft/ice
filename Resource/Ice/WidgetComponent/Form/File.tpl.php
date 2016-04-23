<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <?php if ($component->getShowFile()) : ?>
            <img src="<?= $component->getShowFile() ?>" width="100px"/>
        <?php endif; ?>

            <input <?= $component->getIdAttribute('file') ?> type="file" class="btn btn-default"/>
        <input <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            type="hidden"
            name="<?= $component->getName() ?>"
            <?= $component->getEventAttributesCode() ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >

        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
<script>
    $("#<?= $component->getId() ?>_file").change(function (event) {
        $.each(event.target.files, function (index, file) {
            var reader = new FileReader();
            reader.onload = function (event) {
                $('input[name=<?= $component->getName() ?>]').val(event.target.result);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
