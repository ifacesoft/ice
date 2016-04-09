<div <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
        ><?= $component->getLabel() ?></label>

    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <img src="<?php echo isset($params[$value]) ?  $params[$value] : ''; ?>" width="100px" />
        <input type="file" id="<?= $component->getPartId() ?>_file" class="form-control" />
        <input id="<?= $component->getPartId() ?>"
               type="hidden"
               class="<?= $component->getComponentName() ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $component->getName() ?>"
               data-params='<?= $component->getParams() ?>'
               data-for="<?= $component->getWidgetId() ?>"
            <?php if (isset($options['onchange'])) : ?>
                onchange="<?= $options['onchange'] ?>"
                data-action='<?= $options['dataAction'] ?>'
            <?php endif; ?>
               <?php if (!empty($options['placeholder'])) : ?>placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly"<?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required"<?php endif; ?>
               <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
            >

        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
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
