<a id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
   class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
   href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
   <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?> return false;"<?php endif; ?>
   <?php if (isset($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>
   <?php if (isset($options['actionClass'])) : ?>data-action="<?= $options['actionClass'] ?>"<?php endif; ?>
   <?php if (isset($options['viewClass'])) : ?>data-view="<?= $options['viewClass'] ?>"<?php endif; ?>
   data-name="<?= $name ?>"
   data-params='<?= $dataParams ?>'
   data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"><?php if (isset($params[$name])) : ?><?= $params[$name] ?><?php else : ?><?= $options['label']?><?php endif; ?></a>