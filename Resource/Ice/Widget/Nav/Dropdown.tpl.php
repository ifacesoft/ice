<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?= $label ?> <span
            class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
        <?php foreach ($options as $label => $url) { ?>
            <li><a href="<?= $url ?>"><?= $label ?></a></li>
        <?php } ?>
    </ul>
</li>