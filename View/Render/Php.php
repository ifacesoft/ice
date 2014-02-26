<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 11.01.14
 * Time: 23:10
 */

namespace ice\view\render;

use ice\core\Loader;
use ice\core\Logger;
use ice\core\View_Render;
use ice\Exception;

class Php extends View_Render
{
    const VIEW_RENDER_PHP_CLASS = 'ice\view\render\Php';
    const TEMPLATE_EXTENTION = '.php';

    public function init()
    {
    }

    public function display($template, array $data = array(), $ext)
    {
        extract($data);
        unset($data);

        $templateName = Loader::getFilePath($template, 'View/Template', $ext);

        try {
            require $templateName;
        } catch (\Exception $e) {
            throw new Exception('Render error in template "' . $templateName . '"', array(), $e);
        }
    }

    public function fetch($template, array $data = array(), $ext)
    {
        extract($data);
        unset($data);

        $templateName = Loader::getFilePath($template, 'View/Template', $ext);

        ob_start();
        ob_implicit_flush(false);

        $view = null;

        try {
            require $templateName;
            $view = ob_get_clean();
        } catch (\Exception $e) {
            Logger::outputErrors(new Exception('Render error in template "' . $templateName . '"', array(), $e));
            $view = ob_get_clean();
        }
        return $view;
    }
}