<div class="form-group">
    <label
        class="<?php if ($component->getHorizontal()) : ?> col-md-<?= 12 - $component->getHorizontal() ?> col-md-offset-<?= $component->getHorizontal() ?><?php endif; ?>"
        for="<?= $component->getId() ?>">
        <div class="checkbox" style="margin-left: 20px;">
            <input <?= $component->getIdAttribute('hidden') ?> type="hidden" name="<?= $component->getName() ?>"
                                                               value="0"/>
            <input <?= $component->getIdAttribute() ?>
                type="checkbox"
                <?= $component->getClassAttribute() ?>
                name="<?= $component->getName() ?>"
                value="1"
                <?php if ($component->getValue()) : ?>checked="checked"<?php endif; ?>
                data-name="<?= $component->getName() ?>"
                <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            />
            <?= $component->getLabel() ?>
        </div>
    </label>
</div>