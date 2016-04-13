<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
        ><?= $component->getLabel() ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <img src="<?php $component->getValue() ?>" width="100px" />
        <input type="file" id="<?= $component->getPartId() ?>_file" class="form-control" />
        <input id="<?= $component->getPartId() ?>"
               type="hidden"
               class="<?= $component->getComponentName() ?><?php if (!$component->getOption('resetFormClass')) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $component->getName() ?>"
               data-for="<?= $component->getWidgetId() ?>"
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly"<?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required"<?php endif; ?>
               <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
            >

        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
<script>
    $("#<?= $component->getPartId() ?>_file").change(function(event) {
        $.each(event.target.files, function(index, file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $('input[name=<?= $component->getName() ?>]').val(event.target.result);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
