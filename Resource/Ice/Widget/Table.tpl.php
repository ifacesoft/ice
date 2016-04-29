<div id="<?= $widgetId ?>"
     data-widget='<?= $dataWidget ?>'
     data-params='<?= $dataParams ?>'
     data-for="<?= $parentWidgetId ?>">
    <?php $parts = reset($result) ?>
    <?php foreach ($parts as $partName => $part) : ?>
        <?php if ($part instanceof \Ice\WidgetComponent\Widget) : ?>
            <?php if ($part->getWidget() instanceof \Ice\Widget\Header) : ?>
                <?= $widget->renderPart($part) ?>
                <?php unset($parts[$partName]); ?>
                <?php continue; ?>
            <?php endif; ?>
            <?php if ($part->getWidget() instanceof \Ice\Widget\Form) : ?>
                <?php $form = $part; ?>
                <?php unset($parts[$partName]); ?>
                <?php continue; ?>
            <?php endif; ?>
            <?php if ($part->getWidget() instanceof \Ice\Widget\Pagination) : ?>
                <?php $pagination = $part; ?>
                <?php unset($parts[$partName]); ?>
                <?php continue; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <table class="<?= $widgetClass ?> table<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>">
        <?php foreach ($parts as $part) : ?>
            <?= $widget->renderPart($part) ?>
        <?php endforeach; ?>
    </table>
    <?php if (isset($pagination)) : ?>
        <?= $widget->renderPart($pagination) ?>
    <?php endif; ?>
</div>