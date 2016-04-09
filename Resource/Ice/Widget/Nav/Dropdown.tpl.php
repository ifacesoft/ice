<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?= $component->getLabel() ?> <span
            class="caret"></span></a>
    <ul class="dropdown-menu" role="menu">
        <?php foreach ($options as $label => $url) { ?>
            <li><a href="<?= $url ?>"><?= $component->getLabel() ?></a></li>
        <?php } ?>
    </ul>
</li>