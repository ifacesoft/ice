<?php foreach ($options['items'] as $key => $radio) : ?>
    <div class="radio">
        <label for="<?= $partId ?>_<?= $key ?>">
            <input type="radio"
                   class="<?= $element ?> <?= $name ?>_<?= $key ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
                   id="<?= $partId ?>_<?= $key ?>"
                   name="<?= $name ?>"
                   value="<?= $key ?>"
                   <?php if (isset($onchange)) : ?>onchange='<?= $onchange ?>'<?php endif; ?>
                   <?php if ($params[$name] == $key) { ?>checked="checked" <?php } ?>
                   data-for="<?= $widgetId ?>"
                <?php if ($options['disabled']) : ?> disabled="disabled"<?php endif; ?>
                <?php if ($options['readonly']) : ?> readonly="readonly" <?php endif; ?>>
            <?= $radio ?>
        </label>
    </div>
<?php endforeach; ?>