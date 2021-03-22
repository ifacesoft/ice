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

    <title><?= $title ? $title : $parts['title']->render() ?></title>

    <!-- Bootstrap core CSS -->
<!--    <link href="../../dist/css/bootstrap.min.css" rel="stylesheet">-->

    <script>
        var CKEDITOR_BASEPATH = '/resource/node_modules/ckeditor4/';
    </script>

    <?= $staticResources ? $staticResources : $parts['staticResources']->render() ?>
    <?= $dynamicResources ? $dynamicResources : $parts['dynamicResources']->render() ?>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">-->

    <!-- Custom styles for this template -->
<!--    <link href="starter-template.css" rel="stylesheet">-->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><!--<script src="../../assets/js/ie8-responsive-file-warning.js"></script>--><![endif]-->
<!--    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Главная</a>
        </div>
<!--        <div id="navbar" class="collapse navbar-collapse">-->
<!--            <ul class="nav navbar-nav">-->
<!--                <li class="active"><a href="#">Home</a></li>-->
<!--                <li><a href="#about">About</a></li>-->
<!--                <li><a href="#contact">Contact</a></li>-->
<!--            </ul>-->
<!--        </div>-->
        <!--/.nav-collapse -->
    </div>
</nav>

<div class="container">

    <div class="starter-template">
        <?= $main ? $main : $parts['main']->render() ?>
    </div>

</div><!-- /.container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
<!--<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>-->
<!--<script src="../../dist/js/bootstrap.min.js"></script>-->
<!--<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>-->
<?= $footerJs ? $footerJs : $parts['footerJs']->render() ?>
</body>
</html>
