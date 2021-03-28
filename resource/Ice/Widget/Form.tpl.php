<form id="<?= $widgetId ?>"
      <?php if (!empty($widget->getOption('target'))) : ?>target="<?= $widget->getOption('target') ?>"<?php endif; ?>
      class="<?= $widgetClass ?><?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
      style="<?= $style ?>"
      data-widget='<?= $dataWidget ?>'
      data-params='<?= $dataParams ?>'
      data-for="<?= $parentWidgetId ?>"
      action="<?= $url ?>"
      method="<?= $method ?>"

      <?php if ($onSubmit) : ?>onsubmit="<?= $onSubmit ?>" data-action='<?= $dataAction ?>'<?php endif; ?>
>
    <?php $parts = reset($result) ?>

    <?php if (isset($parts['header'])) : ?>
        <?= $parts['header']->render() ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>

    <?php foreach ($parts as $partName => $part) : ?>
        <?= $part->render() ?>
    <?php endforeach; ?>
</form>
