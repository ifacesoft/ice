<form id="<?= $widgetId ?>"
      class="<?= $widgetClass ?> form<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
      data-widget='<?= $dataWidget ?>'
      data-params='<?= $dataParams ?>'
      data-for="<?= $parentWidgetId ?>"
      action="<?= $action ?>"
      method="<?= $method ?>"
      <?php if ($onSubmit) : ?>onsubmit="<?= $onSubmit ?>"<?php endif; ?>
>
    <?php $parts = reset($result) ?>

    <?php if (isset($parts['header'])) : ?>
        <?= $parts['header']['content'] ?>
        <?php unset($parts['header']); ?>
    <?php endif; ?>

    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</form>
