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
use Ice\Core\Loader;
use Ice\Core\Module;
use Ice\Core\Render;
use Ice\Helper\Emmet;

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
     * @param string $template
     * @param  array $data
     * @param null $layout
     * @param string $templateType
     * @return mixed
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   0.0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
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

        $data = array_filter($data, function ($param) {
            return !is_array($param);
        });

        if (empty($data)) {
            return $template;
        }

        $content = str_replace(
            array_map(
                function ($var) {
                    return '{$' . $var . '}';
                },
                array_keys($data)
            ),
            array_values($data),
            $template
        );

        return $layout
            ? Emmet::translate($layout . '{{$content}}', ['content' => $content])
            : $content;
    }

    /**
     * Init object
     *
     * @param array $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 1.1
     * @since   1.1
     */
    protected function init(array $data)
    {
    }
}
