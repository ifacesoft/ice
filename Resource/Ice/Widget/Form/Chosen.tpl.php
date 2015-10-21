<div<?php if (!isset($options['resetFormClass'])) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $partId ?>"
        class="control-label<?php if (!empty($options['srOnly'])) : ?> sr-only<?php endif; ?><?php if (!empty($widgetOptions['horizontal'])) : ?> col-md-<?= $widgetOptions['horizontal'] ?><?php endif; ?>"
    ><?= $options['label'] ?></label>

    <?php if (!empty($widgetOptions['horizontal'])) : ?>
    <div class="col-md-<?= 12 - $widgetOptions['horizontal'] ?>"><?php endif; ?>
        <select id="<?= $partId ?>"
                class="<?= $element ?> <?= $name ?><?php if (!isset($options['resetFormClass'])) : ?> form-control<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                name="<?= $name ?>"
                data-for="<?= $widgetId ?>"
                data-name="<?= $name ?>"
                data-params='<?= $dataParams ?>'
            <?php if (!empty($options['placeholder'])) : ?> data-placeholder="<?= $options['placeholder'] ?>"<?php endif; ?>
                <?php if (!empty($options['multiple'])) : ?>multiple<?php endif; ?>
                <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
                <?php if (isset($options['onchange'])) : ?>onchange="<?= $options['onchange'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
            <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>>
            <?php foreach ($options['rows'] as $option) : ?>
                <option value="<?= $option[$value] ?>"
                    <?php if ($params[$name] == $option[$value]) : ?> selected="selected"<?php endif; ?>
                ><?= $option[$label] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($widgetOptions['horizontal'])) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $partId ?>").chosen({dateFormat: 'yy-mm-dd'});
        });
    </script>
</div>
