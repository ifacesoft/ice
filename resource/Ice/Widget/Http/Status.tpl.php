<h1><?= $code ?> <?= $status ?></h1>
<h3>Technical reason: <?= $message ?></h3>
<?php if (isset($_SERVER['HTTP_REFERER'])) : ?><p>Return to previous page <a href="<?= $_SERVER['HTTP_REFERER'] ?>"><?= $_SERVER['HTTP_REFERER'] ?></a></p><?php endif; ?>
<p>Refresh page <a href="<?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>"><?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'] ?></a></p>
<p>Go to main page <a href="<?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ?>"><?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] ?></a></p>
<pre><?= $stackTrace ?></pre>