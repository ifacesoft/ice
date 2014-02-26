<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <title><?= $Html_Head_Title[0]->render() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $Html_Head_Resources[0]->render() ?>
</head>
<body>
<div id="Layout_Main">
    <div class="container">
        <?= $layout['Action'][0]->render() ?>
    </div>
</div>
</body>
</html>
