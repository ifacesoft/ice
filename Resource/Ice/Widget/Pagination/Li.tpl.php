<?php if (!empty($options['prev'])) : ?>
    <li><span><?= $options['prev'] ?></span></li><?php endif; ?>
    <li class="menu_item<?php if ($component->isActive()) : ?> active<?php endif; ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>">
        <a href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $component->getComponentName() ?>"
           <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"
           data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
           data-name="<?= $component->getName() ?>"
           data-params='<?= $component->getParams() ?>'
           data-for="<?= $component->getWidgetId() ?>"><?= $component->getLabel() ?></a>
    </li>
<?php if (!empty($options['next'])) : ?>
    <li><span><?= $options['next'] ?></span></li><?php endif; ?>