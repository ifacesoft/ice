<button id="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>_<?= $name ?>"
        class="btn<?php if (!empty($options['classes'])) : ?> <?= $options['classes'] ?><?php endif; ?>"
        data-for="<?= $widgetBaseClassName ?>_<?= $widgetClassName ?>_<?= $token ?>"
        onclick='<?php if (isset($onclick)) : ?><?= $onclick ?><?php else : ?>Ice_Widget_Form.submit($(this));<?php endif; ?>return false;'
        <?php if (!empty($options['action'])) : ?>data-action="<?= $options['action'] ?>"<?php endif; ?>
    ><?= $title ?></button>