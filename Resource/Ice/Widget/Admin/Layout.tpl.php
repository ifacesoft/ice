<?php extract(reset($result)); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?= $title['content'] ?></title>

    <?= $staticResources['content'] ?>
    <?= $dynamicResources['content'] ?>

    <?php /*
    <!--[if lt IE 7]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
        your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
 */ ?>
</head>

<body>
<div id="iceMessages" class='notifications top-right'></div>
<div id="icePreloader">
    <div id="blockG_1" class="preloaderBlock">
    </div>
    <div id="blockG_2" class="preloaderBlock">
    </div>
    <div id="blockG_3" class="preloaderBlock">
    </div>
</div>
<div class="Layout_Admin">
    <?= $navigation['content'] ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <?= $sidebar['content'] ?>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?= $main['content']?>
            </div>
        </div>
    </div>
</div>
<?= $footerJs['content'] ?>
</body>
</html>
