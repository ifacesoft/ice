<h2><?= $title ?></h2>

<table class="table table-striped table-bordered table-hover table-condensed">
    <?php foreach ($rows as $row) : ?>
        <?= $row ?>
    <?php endforeach; ?>
</table>