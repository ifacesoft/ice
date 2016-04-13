<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= $component->getHorizontal() ?>">&nbsp;</div>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <button id="<?= $component->getPartId() ?>"
                class="btn <?= $component->getName() ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
            <?= $component->getEventAttributesCode() ?>
                data-name="<?= $component->getName() ?>"
                data-for="<?= $component->getWidgetId() ?>"
                type="submit"><?= $component->getLabel() ?></button>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>