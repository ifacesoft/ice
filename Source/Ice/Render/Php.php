<?php
/**
 * Ice view render implementation php class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Environment;
use Ice\Core\Loader;
use Ice\Core\Logger;
use Ice\Core\Module;
use Ice\Core\ViiewOld;
use Ice\Core\Render;
use Ice\Helper\Emmet;

/**
 * Class Php
 *
 * Implementation view render php template
 *
 * @see Ice\Core\Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Render
 *
 * @version 0.0
 * @since   0.0
 */
class Php extends Render
{
    const TEMPLATE_EXTENTION = '.tpl.php';

    /**
     * Return php view render
     *
     * @param  mixed $key
     * @param  int $ttl
     * @param array $params
     * @return Php
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since   0.4
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    /**
     * Render view via current view render
     *
     * @param string $template
     * @param  array $data
     * @param null $layout
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        $templateFilePath = Loader::getFilePath($template, Php::TEMPLATE_EXTENTION, Module::RESOURCE_DIR, false);

        if (!file_exists($templateFilePath)) {
            if (Environment::getInstance()->isDevelopment()) {
                $this->getLogger()->info(
                    [
                        Php::getClassName() . ': View {$0} not found. Trying generate template {$1}...',
                        [$template, Php::getClassName()]
                    ],
                    Logger::WARNING
                );

                return Php::getCodeGenerator($template)->generate();
            } else {
                return $this->getLogger()->error(
                    ['Render error in template "{$0}" "{$1}"', [$templateFilePath, ob_get_clean()]],
                    __FILE__,
                    __LINE__
                );
            }
        }

        ob_start();
        ob_implicit_flush(false);

        extract($data);
        unset($data);

        include $templateFilePath;

        return $layout
            ? Emmet::translate($layout . '{{$content}}', ['content' => ob_get_clean()])
            : ob_get_clean();
    }


    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected function init(array $data)
    {
    }
}
