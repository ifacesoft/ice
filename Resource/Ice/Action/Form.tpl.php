<form class="<?= implode(' ', $classes); ?>"
      method="<?php if (!empty($options['method'])) { ?><?= $options['method'] ?><?php } else {?>post<?php } ?>">
    <?php foreach ($fields as $field) { ?>
        <?= $field ?>
    <?php } ?>
</form>