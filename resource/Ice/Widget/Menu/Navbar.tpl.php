<nav class="navbar<?php if (!empty($classes)) { ?> <?= $classes ?><?php } ?>">
    <div class="container-fluid">
        <div class="collapse navbar-collapse">
            <?php if ($brand) : ?>
                <div class="navbar-header">
                    <a href="/" class="navbar-brand"><?= $brand?></a>
                </div>
            <?php endif; ?>
            <?php foreach ($items as $position => $block): ?>
                <ul class="nav navbar-nav <?php if ($position) : ?> navbar-<?= $position ?><?php endif; ?>">
                    <?php foreach ($block as $item): ?>
                        <?= $item ?>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    </div>
</nav>
