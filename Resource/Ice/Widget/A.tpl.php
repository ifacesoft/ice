<a id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
   href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
   <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
   <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   data-name="<?= $name ?>"
   data-params='<?= $dataParams ?>'
   data-for="<?= $widgetId ?>"><?php if (isset($params[$name])) : ?><?= $params[$name] ?><?php else : ?><?= $options['label']?><?php endif; ?></a>