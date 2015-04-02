<li id="<?= $menuName ?>_<?= $name ?>"
    <?php if (!empty($options['classes'])) { ?>class="<?= implode(' ', $options['classes']) ?>"<?php } ?>>
    <a href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php else : ?>#<?php endif; ?>"
       <?php if (isset($options['style'])) { ?>style="<?= $options['style'] ?>"<?php } ?>>
        <?= $title ?>
    </a>
</li>