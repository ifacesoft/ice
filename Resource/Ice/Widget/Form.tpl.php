<?php $parts = reset($result) ?>
<?php if (isset($parts['header'])) : ?>
    <?= $parts['header']['content'] ?>
    <?php unset($parts['header']); ?>
<?php endif; ?>

<form id="Widget_<?= $widgetClassName ?>_<?= $widgetName ?>"
      class="Widget_<?= $widgetClassName ?> form<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
      data-action='<?= $dataAction ?>'
      data-widget='<?= $dataWidget ?>'
      data-params='<?= $dataParams ?>'
      data-for="<?= $dataFor ?>"
      action="<?= $action ?>"
      method="<?= $method ?>"
      <?php if ($onSubmit) : ?>onsubmit="<?= $onSubmit ?>"<?php endif; ?>
>
    <?php foreach ($parts as $part) : ?>
        <?= $part['content'] ?>
    <?php endforeach; ?>
</form>
