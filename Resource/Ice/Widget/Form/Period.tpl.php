<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $partId ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
    ><?= $label ?></label>
    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <input id="<?= $partId ?>_from"
               type="text"
               class="<?= $element ?> <?= $name ?>_from<?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $name ?>_from"
               value="<?= isset($params[$name . '_from']) ? $params[$name . '_from'] : '' ?>"
               data-for="<?= $widgetId ?>"
               <?php if (!empty($options['placeholder'])) : ?>placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
               <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
               <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
        <input id="<?= $partId ?>_to"
               type="text"
               class="<?= $element ?> <?= $name ?>_to<?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
               name="<?= $name ?>_to"
               value="<?= isset($params[$name . '_to']) ? $params[$name . '_to'] : '' ?>"
               data-for="<?= $widgetId ?>"
               <?php if (!empty($options['placeholder'])) : ?>placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
               <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
               <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
               <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
               <?php if (!empty($options['required'])) : ?>required="required" <?php endif; ?>
               <?php if (!empty($options['autofocus'])) : ?>autofocus="autofocus" <?php endif; ?>
        >
        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $partId ?>_from").datepicker({dateFormat: 'yy-mm-dd'});
            $("#<?= $partId ?>_to").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>
</div>
