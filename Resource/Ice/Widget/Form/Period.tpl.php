<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getPartId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <input id="<?= $component->getPartId() ?>_from"
               type="text"
               class="<?= $component->getComponentName() ?>_from<?php if (!$component->getOption('resetFormClass')) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $component->getName() ?>_from"
               value="<?= isset($params[$name . '_from']) ? $params[$name . '_from'] : '' ?>"
               data-params='<?= $component->getDataParams() ?>'
               data-for="<?= $component->getWidgetId() ?>"
            <?php if (isset($options['onchange'])) : ?>
                onchange="<?= $options['onchange'] ?>"
                data-action='<?= $options['dataAction'] ?>'
            <?php endif; ?>
            <?= $component->getPlaceholderAttribute() ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
               <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
        <input id="<?= $component->getPartId() ?>_to"
               type="text"
               class="<?= $component->getComponentName() ?>_to<?php if (!$component->getOption('resetFormClass')) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $component->getName() ?>_to"
               value="<?= isset($params[$name . '_to']) ? $params[$name . '_to'] : '' ?>"
               data-params='<?= $component->getDataParams() ?>'
               data-for="<?= $component->getWidgetId() ?>"
            <?php if (isset($options['onchange'])) : ?>
                onchange="<?= $options['onchange'] ?>"
                data-action='<?= $options['dataAction'] ?>'
            <?php endif; ?>
            <?= $component->getPlaceholderAttribute() ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
               <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getPartId() ?>_from").datepicker({dateFormat: '<?= isset($options['dateFormat']) ? $options['dateFormat'] : 'yy-mm-dd' ?>'});
            $("#<?= $component->getPartId() ?>_to").datepicker({dateFormat: '<?= isset($options['dateFormat']) ? $options['dateFormat'] : 'yy-mm-dd' ?>'});
        });
    </script>
</div>
