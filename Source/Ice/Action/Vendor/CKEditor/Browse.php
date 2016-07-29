<?php
namespace Ice\Action;

use Ice\App;
use Ice\Core\Action;
use Ice\Helper\Directory;
use Ice\Render\Php;

class Vendor_CKEditor_Browse extends Action
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

        $files = [];
        foreach (new \DirectoryIterator(Directory::get(MODULE_DIR . '/web/ckeditor/')) as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            $files[] = '/ckeditor/' . $fileInfo->getFilename();
        }
        $view = Php::getInstance()->fetch('Ice\Action\Vendor_CKEditor_Browse', ['files' => $files]);
        App::getResponse()->setContent($view);


    }
}