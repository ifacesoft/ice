<li id="<?= $widgetClassName?>_<?= $widgetName ?>_<?= $name ?>"
    class="<?= $element ?> <?= $name ?><?php if (isset($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>
<?php if (!empty($options['active'])) : ?> active<?php endif; ?>">
    <a href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?> return false;"<?php endif; ?>
       data-name="<?= $name ?>"
       data-params='<?= $dataParams ?>'
       data-for="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"><?= $options['label'] ?></a>
</li>