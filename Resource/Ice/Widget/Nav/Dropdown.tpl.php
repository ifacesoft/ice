<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?= $options['label'] ?> <span
            class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
        <?php foreach ($options as $options['label'] => $url) { ?>
            <li><a href="<?= $url ?>"><?= $options['label'] ?></a></li>
        <?php } ?>
    </ul>
</li>