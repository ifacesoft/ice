<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label><?= $component->getLabel() ?></label>
    <?php foreach ($component->getOption('items', []) as $key => $radio) : ?>
        <div class="radio">
            <label for="<?= $component->getId($key) ?>">
                <input <?= $component->getIdAttribute($key) ?>
                    <?= $component->getClassAttribute($component->getComponentName() . '_' . $key) ?>
                    type="radio"
                    name="<?= $component->getName() ?>"
                    value="<?= $key ?>"
                    <?php if ($component->get($component->getName()) == $key) { ?>checked="checked" <?php } ?>
                    <?= $component->getEventAttributesCode() ?>
                    <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                    <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                    <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                >
                <?= $radio ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>
