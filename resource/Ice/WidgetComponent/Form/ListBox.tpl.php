<div <?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"
    <?php if ($component->getOption('turnOffEnter', false)) : ?>onkeypress="return event.keyCode != 13;"<?php endif; ?>
<?php endif; ?>>
    <label
            for="<?= $component->getId() ?>"
            class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?><?php if ($component->getOption('required', false)) : ?> <sup
                style="color: red;">*</sup><?php endif; ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <input type="hidden"
               name="<?= $component->getName() ?><?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
               value="">
        <select <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
                name="<?= $component->getName() ?><?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
                size="<?= $component->getOption('size', 1) ?>"
                <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
            <?= $component->getEventAttributesCode() ?>
            <?= $component->getPlaceholderAttribute('data-placeholder') ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php if ($component->getOption('emptyDefault', false)) : ?>
                <option value=""></option>
            <?php endif; ?>

            <?php
            $value = $component->getValue();
            $itemKey = $component->getItemKey();

            if (is_array($itemKey)) {
                $itemKey = reset($itemKey);
            }

            foreach ($component->getItems() as $item) : ?>
                <?php
                $selected = (is_array($value) && in_array($item[$itemKey], $value))
                    || (!is_array($value) && (string)$item[$itemKey] === html_entity_decode($value));

                $disabled = isset($item['disabled']) && $item['disabled'] === true;
                ?>
                <option value="<?= htmlentities($item[$itemKey]) ?>"
                        <?php if ($selected) : ?>selected="selected"<?php endif; ?>
                        <?php if ($disabled) : ?>disabled="disabled"<?php endif; ?>
                ><?= $component->getTitle($item) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
