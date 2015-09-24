<?php foreach ($options['items'] as $key => $radio) : ?>
    <div class="radio">
        <label for="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>_<?= $key ?>">
            <input type="radio"
                   class="<?= $element ?> <?= $name ?>_<?= $key ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                   id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>_<?= $key ?>"
                   name="<?= $name ?>"
                   value="<?= $key ?>"
                   <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
                   <?php if ($params[$name] == $key) { ?>checked="checked" <?php } ?>
                   data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
                <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
                <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
            <?= $radio ?>
        </label>
    </div>
<?php endforeach; ?>