<button <?= $component->getIdAttribute() ?>
    <?= $component->getClassAttribute() ?>
    <?= $component->getButtonType() == 'submit' ? $component->getButtonType() : $component->getEventAttributesCode() ?>
    <?php if ($component->getOption('tooltip')) : ?>
        data-toggle="tooltip"
        <?php if (isset($component->getOption('tooltip')['position'])) : ?>
            data-placement="<?= $component->getOption('tooltip')['position'] ?>"
        <?php endif; ?>
        title="<?= $component->getResource()->get($component->getOption('tooltip')['title']); ?>"
    <?php endif; ?>
    data-name="<?= $component->getName() ?>"
    <?php if (!empty($component->getOption('disabled'))) : ?>disabled="disabled"<?php endif; ?>
    type="<?= $component->getButtonType() ?>"><?= $component->getLabel() ?></button>
