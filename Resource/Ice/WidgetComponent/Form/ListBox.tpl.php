<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select i<?= $component->getIdAttribute() ?>
                <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>name="<?= $component->getName() ?><?php if (!empty($options['multiple'])) : ?>[]<?php endif; ?>"
                <?php if (!empty($options['multiple'])) : ?>multiple="multiple"<?php endif; ?>
                <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
            <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php foreach ($component->getItems() as $item) : ?>
                <option value="<?= htmlentities($item[$component->getItemKey()]) ?>"
                    <?php if (htmlentities($item[$component->getItemKey()]) == $component->getValue()) : ?> selected="selected"<?php endif; ?>
                ><?= htmlentities($item[$component->getItemTitle()]) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
