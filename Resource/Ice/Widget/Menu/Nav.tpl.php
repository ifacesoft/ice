<nav class="Widget_Menu_Nav<?php if (!empty($navClasses)) { ?> <?= $navClasses ?><?php } ?>">
    <ul class="nav<?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>"
        <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>>

        <?php foreach ($items as $item) : ?>
            <?= $item ?>
        <?php endforeach; ?>
    </ul>
</nav>