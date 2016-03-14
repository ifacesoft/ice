<h4
    id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-name="<?= $name ?>"
    data-params='<?= $dataParams ?>'
    data-for="<?= $widgetId ?>"
>
    <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>>
        <?php if (isset($params[$label])) : ?><?= $params[$label] ?><?php else : ?><?= $label ?><?php endif; ?>
    </a>
</h4>