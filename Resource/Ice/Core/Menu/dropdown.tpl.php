<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= $title ?> <span class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
        <?php foreach ($dropdown as $title => $url) { ?>
            <li><a href="<?= $url ?>"><?= $title ?></a></li>
        <?php } ?>
    </ul>
</li>