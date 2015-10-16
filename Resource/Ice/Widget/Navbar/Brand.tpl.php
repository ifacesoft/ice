<div
    id="<?= $partId ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
    class="navbar-header <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
    data-name="<?= $name ?>"
    data-params='<?= $dataParams ?>'
    data-for="<?= $widgetId ?>">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
            aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand"
       href="<?php if (!empty($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"
       <?php if (isset($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>" data-action='<?= $options['dataAction'] ?>'<?php endif; ?>
       <?php if (!empty($options['target'])) : ?>target="<?= $options['target'] ?>"<?php endif; ?>><?= $options['label'] ?></a>
</div>