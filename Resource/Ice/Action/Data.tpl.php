<h2><?php if ($title) { ?><?= $title ?><?php } ?></h2>
<h3><?php if ($desc) { ?><?= $desc ?><?php } ?></h3>
<table class="table table-striped table-bordered table-hover table-condensed">
    <?php foreach ($rows as $row) : ?>
        <?= $row ?>
    <?php endforeach; ?>
</table>