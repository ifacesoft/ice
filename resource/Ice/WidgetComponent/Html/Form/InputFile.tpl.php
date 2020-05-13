<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
            for="<?= $component->getId() ?>"
            class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <input <?= $component->getIdAttribute('file') ?>
                type="file"
                style="display: inline-block;"
            <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        />
        <input <?= $component->getIdAttribute() ?>
                type="hidden"
                name="<?= $component->getName() ?>"

            <?php if ($downloadUrl = $component->getDownLoadUrl()) : ?>
                value="<?= strstr($downloadUrl, '/book/') ?>"
            <?php endif; ?>
        />
        <?php if ($downloadUrl = $component->getDownLoadUrl()) : ?>
            <a href="<?= $downloadUrl ?>">скачать</a>
            <!--            <a style="color: red;" href="--><?//= $deleteFile ?><!--">удалить</a>-->
        <?php endif; ?>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
<script>
    $("#<?= $component->getId('file') ?>").change(function (event) {
        $.each(event.target.files, function (index, file) {
            var reader = new FileReader();
            reader.onload = function (event) {
                $('input[name=<?= $component->getName() ?>]').val(event.target.result + ',' + file.name + ',' + file.size);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
