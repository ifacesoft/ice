<a id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
   href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
   <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
   data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
   <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   data-name="<?= $name ?>"
   data-params='<?= $dataParams ?>'
   data-for="<?= $widgetId ?>" disabled="disabled">
    <?php if (isset($oneToMany)) : ?>
        <?= implode(', ', $oneToMany) ?>
    <?php elseif (isset($manyToMany)) : ?>
        <?= $manyToMany ?>
    <?php elseif (isset($params[$title])) : ?>
        <?= $params[$title] ?>
    <?php else : ?>
        <?= $title ?>
    <?php endif; ?>
</a>