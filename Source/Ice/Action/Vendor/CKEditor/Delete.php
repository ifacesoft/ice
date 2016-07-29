<?php
namespace Ice\Action;

use Ice\Core\Action;
use Ice\DataProvider\Request;
use Ice\Helper\Directory;


class Vendor_CKEditor_Delete extends Action
{
    protected static function config()
    {
        return [
            'access' => ['roles' => ['ROLE_ICE_ADMIN'], 'request' => null, 'env' => null, 'message' => 'Action: Access denied!'],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'actions' => [],
            'input' => ['file' => ['providers' => Request::class]],
            'output' => []
        ];
    }

    public function run(array $input)
    {
        $directory = Directory::get(MODULE_DIR . '/web/ckeditor/');
        if (strpos($input['file'], '/') || strpos($input['file'], '\\')) {
            echo 'Файл не найден. <a href="/ice/ckeditor/browse">Вернуться</a>';
            exit;
        }
        if (is_file($directory . $input['file'])) {
            if (unlink($directory . $input['file'])) {
                header('Location: /ice/ckeditor/browse');
            }
        } else {
            echo 'Файл не найден. <a href="/ice/ckeditor/browse">Вернуться</a>';
        }
        exit;
    }
}