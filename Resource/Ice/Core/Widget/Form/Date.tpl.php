<div id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
     <?php if (!isset($options['resetFormClass'])) : ?>class="form-group"<?php endif; ?>>
    <label
        for="<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
        <?php if (isset($options['srOnly'])) : ?>class="sr-only"<?php endif; ?>><?= $title ?></label>
    <input type="text"
           class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
           id="<?= $widgetClassName . '_' . $token . '_' . $name ?>"
           name="<?= $name ?>"
           value="<?= $params[$name] ?>"
           style="width: 100%;"
           data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
        <?php if (isset($options['placeholder'])) : ?>placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
           <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?> return false;"<?php endif; ?>
        <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
        <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>
        <?php if ($options['required']) : ?> required="required" <?php endif; ?>
        <?php if ($options['autofocus']) : ?> autofocus="autofocus" <?php endif; ?>>
    <script>
        $(function () {
            $("#<?= $widgetClassName . '_' . $token . '_' . $name ?>").datepicker({dateFormat: 'yy-mm-dd'});
        });
    </script>
</div>
