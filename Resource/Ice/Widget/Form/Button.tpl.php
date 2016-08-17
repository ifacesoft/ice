<button <?= $component->getIdAttribute() ?>
    <?= $component->getClassAttribute() ?>
    <?= $component->getButtonType() == 'submit' ? $component->getButtonType() : $component->getEventAttributesCode() ?>
    <?php if (isset($options['tooltip'])) : ?>
        data-toggle="tooltip"
        <?php if (isset($options['tooltip']['position'])) : ?>
            data-placement="<?= $options['tooltip']['position'] ?>"
        <?php endif; ?>
        title="<?= $resource->get($options['tooltip']['title']); ?>"
    <?php endif; ?>
    data-name="<?= $component->getName() ?>"
    <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
    type="<?= $component->getButtonType() ?>"><?= $component->getLabel() ?></button>
