<div<?php if (!empty($classes)) { ?> class="<?= $classes ?>"<?php } ?>>
    <?php if (!empty($header)) : ?><h3><?= $header ?></h3><?php endif; ?>
    <?php if (!empty($description)) : ?><h5><?= $description ?></h5><?php endif; ?>
    <form id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
          class="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>"
          <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>
          data-url='<?= $dataUrl ?>'
          data-json='<?= $dataJson ?>'
          data-action='<?= $dataAction ?>'
          data-block='<?= $dataBlock ?>'
          action="<?= $url ?>"
          method="<?php if (!empty($options['method'])) { ?><?= $options['method'] ?><?php } else { ?>post<?php } ?>">
        <?php foreach ($parts as $field) { ?>
            <?= $field ?>
        <?php } ?>
    </form>
</div>
