<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\Helper\Directory;


class Vendor_CKEditor_Upload extends Action
{


    protected static function config()
    {
        return [
            'access' => ['roles' => ['ROLE_ICE_ADMIN'], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => [],
            'output' => []
        ];
    }

    public function run(array $input)
    {

        if (array_key_exists('file', $_FILES) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            if (false === @exif_imagetype($_FILES['file']['tmp_name'])) {
                $this->back();
            }
            $directory = Directory::get(MODULE_DIR . '/web/ckeditor/');
            $extension = '.' . pathinfo($_FILES['file']['name'], \PATHINFO_EXTENSION);
            $filename = md5(time() . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            while (file_exists($directory . $filename . $extension)) {
                $filename = md5($filename);
            }
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $directory . $filename . $extension)) {
                $this->back();
            }
        }
        echo 'При загрузке файла произошла ошибка. <a href="/ice/ckeditor/browse">Назад</a>';
        exit;


    }

    private function back()
    {

        header('Location: /ice/ckeditor/browse');
        exit;

    }
}