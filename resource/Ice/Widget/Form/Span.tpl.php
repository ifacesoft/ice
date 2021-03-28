<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
            for="<?= $component->getId() ?>"
            class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?><?php if ($component->getOption('required', false)) : ?> <sup
                style="color: red;">*</sup><?php endif; ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <?php if ($component->getValue()) : ?>
            <span <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            <?= $component->getEventAttributesCode() ?>
            style="border: 0; box-shadow: none; <?= $component->getOption('style', '') ?>"
            ><?= $component->getValue() ?></span><?php endif; ?>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
