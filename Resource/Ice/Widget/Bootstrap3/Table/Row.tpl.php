<?php
$parts = reset($result) ?>

<tr id="<?= $widgetId ?>"
    data-widget='<?= $dataWidget ?>'
    data-params='<?= $dataParams ?>'
    data-for="<?= $parentWidgetId ?>"
    class="<?= $widgetClass ?> table<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>">
    <?php foreach ($parts as $part) : ?>
        <?php if ($part instanceof \Ice\WidgetComponent\Table_Row_Th) : // todo: Временно: Th и td будут в шаблонах компонента ?>
            <?= '<th>' . $part->render() . '</th>' ?>
        <?php else : ?>
            <?= '<td>' . $part->render() . '</td>' ?>
        <?php endif; ?>
    <?php endforeach; ?>
</tr>