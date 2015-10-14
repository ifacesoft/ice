<a id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
   href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
   <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
   <?php if (isset($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   <?php if (isset($options['dataAction'])) : ?>data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
   data-name="<?= $name ?>"
   data-params='<?= $dataParams ?>'
   <?php if (!empty($dataAction)) : ?>data-action='<?= $dataAction ?>'<?php endif; ?>
   data-for="<?= $widgetId ?>"><?php if (isset($params[$name])) : ?><?= $params[$name] ?><?php else : ?><?= $options['label']?><?php endif; ?></a>