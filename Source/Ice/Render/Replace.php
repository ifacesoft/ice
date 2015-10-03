<?php
/**
 * Ice view render implementation replace class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Action;
use Ice\Core\Debuger;
use Ice\Core\Loader;
use Ice\Core\Module;
use Ice\Core\Render;

/**
 * Class Replace
 *
 * Implementation view render cli template
 *
 * @see Ice\Core\Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage Render
 */
class Replace extends Render
{
    const TEMPLATE_EXTENTION = '.tpl.txt';

    /**
     * Return instanc of view render Replace
     *
     * @param  null $key
     * @param  null $ttl
     * @param array $params
     * @return Replace
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    public static function getInstance($key = null, $ttl = null, array $params = [])
    {
        return parent::getInstance($key, $ttl, $params);
    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param  array $data
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        if (empty($template)) {
            throw new \Exception('Template is empty');
        }

        if ($templateType == Render::TEMPLATE_TYPE_FILE) {
            $template = str_replace(['_', '\\'], '/', $template);

            $template = file_get_contents(
                Loader::getFilePath($template, self::TEMPLATE_EXTENTION, Module::RESOURCE_DIR)
            );
        }

        if (empty($data)) {
            return $template;
        }

        return str_replace(
            array_map(
                function ($var) {
                    return '{$' . $var . '}';
                },
                array_keys($data)
            ),
            array_values($data),
            $template
        );
    }

    /**
     * Init object
     *
     * @param array $params
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   2.0
     */
    protected function init(array $params)
    {
    }
}
