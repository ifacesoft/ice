<?php //var_dump(get_defined_vars());?>
<button id="<?= $partId ?>"
        class="btn <?= $element ?> <?= $name ?><?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        <?php if (!empty($options['onclick'])) : ?>onclick="<?= $options['onclick'] ?>"<?php endif; ?>
        data-name="<?= $name ?>"
        data-params='<?= $dataParams ?>'
        data-for="<?= $widgetId ?>"
        type="<?php if (!empty($options['submit'])) : ?>submit<?php else : ?>button<?php endif; ?>"><?= $options['label'] ?></button>
