<?php if (!empty($options['prev'])) : ?><li><span><?= $options['prev'] ?></span></li><?php endif; ?>
    <li class="menu_item<?php if (!empty($options['active'])) : ?> active<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>">
        <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
           <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
           data-name="<?= $name ?>"
           data-params='<?= $dataParams ?>'
           data-for="<?= $widgetId ?>"><?= $options['label'] ?></a>
    </li>
<?php if (!empty($options['next'])) : ?><li><span><?= $options['next'] ?></span></li><?php endif; ?>