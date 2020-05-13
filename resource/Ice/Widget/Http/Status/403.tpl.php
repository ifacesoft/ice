<h1>Forbidden</h1>
<h3>Technical reason: <?= $message ?></h3>
<?php if (isset($_SERVER['HTTP_REFERER'])) : ?><p>Return to previous page <a
        href="<?= $_SERVER['HTTP_REFERER'] ?>"><?= $_SERVER['HTTP_REFERER'] ?></a></p><?php endif; ?>
<p>Return to main page <a href="<?= 'http://' . $_SERVER['HTTP_HOST'] ?>"><?= 'http://' . $_SERVER['HTTP_HOST'] ?></a>
</p>
<!--pre><?= $stackTrace ?></pre-->