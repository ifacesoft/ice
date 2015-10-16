<li id="<?= $partId ?>"
    class="<?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if (!empty($options['active'])) : ?> active<?php endif; ?>">
    <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
       data-name="<?= $name ?>"
       data-params='<?= $dataParams ?>'
       data-for="<?= $widgetId ?>"><?= $options['label'] ?></a>
</li>