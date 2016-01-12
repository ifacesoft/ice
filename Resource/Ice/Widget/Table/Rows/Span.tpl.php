<span
    id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    <?php if (!empty($options['style'])) : ?>style="<?= $options['style'] ?>"<?php endif; ?>
    <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
    data-name="<?= $name ?>"
    data-params='<?= $dataParams ?>'
    data-for="<?= $widgetId ?>">
    <?php if (isset($params[$title])) : ?>
        <?php if (isset($oneToMany)) : ?>
            <?= $oneToMany ?>
        <?php elseif (isset($manyToMany)) : ?>
            <?= $manyToMany ?>
        <?php else : ?>
            <?= $params[$title] ?>
        <?php endif; ?>
    <?php else : ?>
        &nbsp;
    <?php endif; ?>
</span>
