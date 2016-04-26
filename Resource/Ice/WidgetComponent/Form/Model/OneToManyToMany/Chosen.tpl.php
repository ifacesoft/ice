<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>_many"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getManyLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select <?= $component->getIdAttribute('many') ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            name="<?= $component->getName() ?>_many<?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
            <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
            <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
            <?= $component->getPlaceholderManyAttribute('data-placeholder') ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php foreach ($component->getManyItems() as $item) : ?>
                <option value="<?= htmlentities($item[$component->getManyItemKey()]) ?>"
                        <?php if ((is_array($component->getManyValue()) && in_array($item[$component->getManyItemKey()], $component->getManyValue())) || $item[$component->getManyItemKey()] == $component->getValue()) : ?>selected="selected"<?php endif; ?>
                ><?= $item[$component->getManyItemTitle()] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getId('many') ?>")
                .chosen({
                        max_selected_options: 5<?php if ($component->getOption('required', false) === false) : ?>,
                        allow_single_deselect: true<?php endif; ?>
                });
        });
    </script>
</div>
<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            name="<?= $component->getName() ?><?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
            <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
            <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
            <?= $component->getEventAttributesCode() ?>
            <?= $component->getPlaceholderAttribute('data-placeholder') ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php foreach ($component->getItems() as $item) : ?>
                <option value="<?= htmlentities($item[$component->getItemKey()]) ?>"
                        <?php if ((is_array($component->getValue()) && in_array($item[$component->getItemKey()], $component->getValue())) || $item[$component->getItemKey()] == $component->getValue()) : ?>selected="selected"<?php endif; ?>
                ><?= $item[$component->getItemTitle()] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getId() ?>").chosen({max_selected_options: 5<?php if ($component->getOption('required', false) === false) : ?>, allow_single_deselect: true<?php endif; ?>});
        });
    </script>
</div>