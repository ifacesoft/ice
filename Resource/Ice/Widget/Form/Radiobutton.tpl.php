<?php foreach ($options['items'] as $key => $radio) : ?>
    <div class="radio">
        <label for="<?= $component->getPartId() ?>_<?= $key ?>">
            <input type="radio"
                   class="<?= $component->getComponentName() ?>_<?= $key ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                   id="<?= $component->getPartId() ?>_<?= $key ?>"
                   name="<?= $component->getName() ?>"
                   value="<?= $key ?>"
                   <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
                   <?php if ($params[$name] == $key) { ?>checked="checked" <?php } ?>
                   data-for="<?= $component->getWidgetId() ?>"
                   <?php if (!empty($options['disabled'])) : ?>disabled="disabled"<?php endif; ?>
                   <?php if (!empty($options['readonly'])) : ?>readonly="readonly" <?php endif; ?>
            >
            <?= $radio ?>
        </label>
    </div>
<?php endforeach; ?>