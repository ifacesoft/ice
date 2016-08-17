<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Example: Browsing Files</title>
    <script>
        // Helper function to get parameters from the query string.
        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);

            return ( match && match.length > 1 ) ? match[1] : null;
        }
        // Simulate user action of selecting a file to be returned to CKEditor.
        function returnFileUrl(url) {

            var funcNum = getUrlParam('CKEditorFuncNum');
            window.opener.CKEDITOR.tools.callFunction(funcNum, url);
            window.close();
        }
    </script>
    <style>
        .image {
            float: left;
            width: 19%;
            margin-right: 1%;
            height: 250px;
        }

        .image_wrapper img {
            width: 100%;
            max-height: 200px;
        }

        .image_wrapper {
            width: 90%;
            margin: 0 auto;
            height: 200px;
        }
    </style>
</head>
<body>
<?php foreach ($files as $file) { ?>
    <div class="image">
        <div style="border: 1px solid grey; padding: 4px;">
            <div class="image_wrapper">
                <img src="<?php echo $file; ?>"/>
            </div>
            <div style="text-align: center;">
                <button onclick="returnFileUrl('<?php echo $file; ?>')">выбрать</button>
                <a href="/ice/ckeditor/delete?file=<?php echo str_replace('/ckeditor/', '', $file); ?>">удалить</a>
            </div>
        </div>
    </div>
<?php } ?>
<div class="image">
    <div style="border: 1px solid grey; padding: 4px;">
        <form enctype="multipart/form-data" action="/ice/ckeditor/upload" method="POST">
            <input type="file" name="file"/>
            <input type="submit" value="Добавить Файл">
        </form>
    </div>
</div>
</body>
</html>