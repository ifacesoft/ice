<?php foreach ($options['items'] as $key => $radio) : ?>
    <div class="radio">
        <label for="<?= $component->getId($key) ?>">
            <input <?= $component->getIdAttribute($key) ?>
                <?= $component->getClassAttribute($component->getComponentName() . '_' . $key) ?>
                type="radio"
                name="<?= $component->getName() ?>"
                value="<?= $key ?>"
                <?php if ($params[$name] == $key) { ?>checked="checked" <?php } ?>
                data-for="<?= $component->getWidgetId() ?>"
                <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            >
            <?= $radio ?>
        </label>
    </div>
<?php endforeach; ?>