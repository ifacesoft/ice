<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $Resources[0] ?>
</head>
<body>
<div id="iceMessages" class='notifications top-right'></div>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<div id="Layout_Main">
    <div class="container">
        <?= $main ?>
    </div>
</div>
</body>
</html>
