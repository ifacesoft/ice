<label
        for="<?= $component->getId() ?>">
    <div class="checkbox" style="margin-left: 6px; margin-top: 6px; margin-bottom: 0px;">
        <input <?= $component->getIdAttribute('hidden') ?>
                type="hidden" name="<?= $component->getComponentName() ?>" value="0"/>
        <input <?= $component->getIdAttribute() ?>
                type="checkbox"
            <?= $component->getClassAttribute() ?>
                name="<?= $component->getComponentName() ?>[<?= $component->get($component->getComponentName(), 'empty') ?>]"
                value="1"
                data-name="<?= $component->getComponentName() ?>"
            <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
        />
        <?= $component->getLabel($component->getValue()) ?>
    </div>
</label>