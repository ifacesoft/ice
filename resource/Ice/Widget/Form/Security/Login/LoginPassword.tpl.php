<form class="form-signin<?php if (!empty($classes)) : ?> <?= $classes ?><?php endif; ?>"
      method=" <?php if (!empty($component->getOption('method'))) : ?><?= $component->getOption('method') ?><?php else : ?>post<?php endif; ?>">
    <?php foreach ($fields as $field) { ?>
        <?= $field ?>
    <?php } ?>
</form>
