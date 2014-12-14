<ul class="pagination">
    <?php if (isset($first)) { ?>
        <li><a onclick="Paginator.page('<?= $first ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;">1 <<<</a></li>
    <?php } if (isset($fastPrev)) { ?>
        <li><a onclick="Paginator.page('<?= $fastPrev ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;">-5 <<</a></li>
    <?php } if (isset($prev)) { ?>
        <li><a onclick="Paginator.page('<?= $prev ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;">-1 <</a></li>
    <?php } if (isset($before2) && $before2 > 1) { ?>
        <li class="disabled"><a>...</a></li>
    <?php } if (isset($before2)) { ?>
        <li><a onclick="Paginator.page('<?= $before2 ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;"><?= $before2 ?></a></li>
    <?php } if (isset($before1)) { ?>
        <li><a onclick="Paginator.page('<?= $before1 ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;"><?= $before1 ?></a></li>
    <?php } ?>
    <li class="active"><a onclick="Paginator.page('<?= $curr ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;"><?= $curr ?> (<?= $limit ?>/<?= $foundRows ?>)</a>
    </li>
    <?php if (isset($after1)) { ?>
        <li><a onclick="Paginator.page('<?= $after1 ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;"><?= $after1 ?></a></li>
    <?php } if (isset($after2)) { ?>
        <li><a onclick="Paginator.page('<?= $after2 ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;"><?= $after2 ?></a></li>
    <?php } if (isset($after2) && $after2 < $last) { ?>
        <li class="disabled"><a>...</a></li>
    <?php } if (isset($next)) { ?>
        <li><a onclick="Paginator.page('<?= $next ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;">> +1</a></li>
    <?php } if (isset($fastNext)) { ?>
        <li><a onclick="Paginator.page('<?= $fastNext ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;">>> +5</a></li>
    <?php } if (isset($last)) { ?>
        <li><a onclick="Paginator.page('<?= $last ?>', '<?= $actionClassName ?>', <?= $params ?>); return false;">>>> <?= $last ?></a></li>
    <?php } ?>
</ul>