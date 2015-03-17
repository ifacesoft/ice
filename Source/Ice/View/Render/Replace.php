<?php
/**
 * Ice view render implementation replace class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\View\Render;

use Ice\Core\Action;
use Ice\Core\Loader;
use Ice\Core\Module;
use Ice\Core\View_Render;

/**
 * Class Replace
 *
 * Implementation view render cli template
 *
 * @see Ice\Core\View_Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage View_Render
 *
 * @version 0.0
 * @since 0.0
 */
class Replace extends View_Render
{
    const TEMPLATE_EXTENTION = '.tpl.txt';

    /**
     * Constructor of replace view render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected function __construct()
    {
    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
//        $template = Action::getClass($template);

        if ($templateType == View_Render::TEMPLATE_TYPE_FILE) {
            $template = str_replace(['_', '\\'], '/', $template);
            $template = file_get_contents(Loader::getFilePath($template, self::TEMPLATE_EXTENTION, Module::RESOURCE_DIR));
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
     * Return instanc of view render Replace
     *
     * @param null $key
     * @param null $ttl
     * @return Replace
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}