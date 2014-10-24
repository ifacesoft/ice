<ul class="pagination">
    <?php if (isset($first)) { ?>
        <li><a onclick="Paginator.page('<?= $first ?>', '<?= $actionName ?>', <?= $params ?>); return false;">1 <<<</a></li>
    <?php } if (isset($fastPrev)) { ?>
        <li><a onclick="Paginator.page('<?= $fastPrev ?>', '<?= $actionName ?>', <?= $params ?>); return false;">-5 <<</a></li>
    <?php } if (isset($prev)) { ?>
        <li><a onclick="Paginator.page('<?= $prev ?>', '<?= $actionName ?>', <?= $params ?>); return false;">-1 <</a></li>
    <?php } if (isset($before2) && $before2 > 1) { ?>
        <li class="disabled"><a>...</a></li>
    <?php } if (isset($before2)) { ?>
        <li><a onclick="Paginator.page('<?= $before2 ?>', '<?= $actionName ?>', <?= $params ?>); return false;"><?= $before2 ?></a></li>
    <?php } if (isset($before1)) { ?>
        <li><a onclick="Paginator.page('<?= $before1 ?>', '<?= $actionName ?>', <?= $params ?>); return false;"><?= $before1 ?></a></li>
    <?php } ?>
    <li class="active"><a onclick="Paginator.page('<?= $curr ?>', '<?= $actionName ?>', <?= $params ?>); return false;"><?= $curr ?> (<?= $limit ?>/<?= $foundRows ?>)</a>
    </li>
    <?php if (isset($after1)) { ?>
        <li><a onclick="Paginator.page('<?= $after1 ?>', '<?= $actionName ?>', <?= $params ?>); return false;"><?= $after1 ?></a></li>
    <?php } if (isset($after2)) { ?>
        <li><a onclick="Paginator.page('<?= $after2 ?>', '<?= $actionName ?>', <?= $params ?>); return false;"><?= $after2 ?></a></li>
    <?php } if (isset($after2) && $after2 < $last) { ?>
        <li class="disabled"><a>...</a></li>
    <?php } if (isset($next)) { ?>
        <li><a onclick="Paginator.page('<?= $next ?>', '<?= $actionName ?>', <?= $params ?>); return false;">> +1</a></li>
    <?php } if (isset($fastNext)) { ?>
        <li><a onclick="Paginator.page('<?= $fastNext ?>', '<?= $actionName ?>', <?= $params ?>); return false;">>> +5</a></li>
    <?php } if (isset($last)) { ?>
        <li><a onclick="Paginator.page('<?= $last ?>', '<?= $actionName ?>', <?= $params ?>); return false;">>>> <?= $last ?></a></li>
    <?php } ?>
</ul>