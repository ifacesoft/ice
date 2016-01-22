<form id="<?= $widgetId ?>"
      class="<?= $widgetClass ?><?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
      data-widget='<?= $dataWidget ?>'
      data-params='<?= $dataParams ?>'
      data-for="<?= $parentWidgetId ?>"
      action="<?= $url ?>"
      method="<?= $method ?>"
      <?php if ($onSubmit) : ?>onsubmit="<?= $onSubmit ?>" data-action='<?= $dataAction ?>'<?php endif; ?>
>
    <?php $parts = reset($result) ?>

    <?php if (isset($parts['header'])) : ?>
        <?= $widget->renderPart($parts['header']) ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>

    <?php foreach ($parts as $part) : ?>
        <?= $widget->renderPart($part) ?>
    <?php endforeach; ?>
</form>
