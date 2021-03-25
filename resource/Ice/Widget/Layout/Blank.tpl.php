<?php $parts = reset($result); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">

    <title><?= isset($title) ? $title : $parts['title']->render() ?></title>

    <?= isset($staticResources) ? $staticResources : $parts['staticResources']->render() ?>
    <?= isset($dynamicResources) ? $dynamicResources : $parts['dynamicResources']->render() ?>
</head>

<body>
<?= isset($main) ? $main : $parts['main']->render() ?>

<?= isset($footerJs) ? $footerJs : $parts['footerJs']->render() ?>
</body>
</html>
