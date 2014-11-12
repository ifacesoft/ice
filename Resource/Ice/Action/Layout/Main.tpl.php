<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $Resources[0] ?>
</head>
<body>
<div id="Layout_Main" class="container">
    <div id="iceMessages" class='notifications top-right'></div>

    <!--[if lt IE 7]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
        your browser</a> to improve your experience.</p>
    <![endif]-->

    <div>
        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">
                        <img alt="Brand" src="/resource/img/logo/ice400.jpg" style="height: 22px;">
                        <?= $moduleName ?>
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav nav-justified" style="width: 80%;">
                        <li class="first" style="float: none;"><a href="http://iceframework.net">Iceframework</a></li>
                        <li style="float: none;"><a href="https://github.com/ifacesoft/ice">On GitHub</a></li>
                        <li style="float: none;"><a href="http://iceframework.net/handbook">Handbook</a></li>
                        <li style="float: none;"><a href="http://iceframework.net/cookbook">Cookbook</a></li>
                        <li style="float: none;"><a href="http://iceframework.net/resource/api/Ice/0.0/">Api</a></li>
<!--                        <li style="float: none;"><a href="http://iceframework.net/faq">Faq</a></li>-->
<!--                        <li class="last" style="float: none;"><a href="http://iceframework.net/demo">Live Demo</a></li>-->
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <?= $main ?>
    </div>
</div>
<div class="footer">
    <div class="container" style="text-align: right; font-size: 24px;">
        Powered by <a href="http://iceframework.net"><strong>Ice</strong></a>
    </div>
</div>
</body>
</html>
