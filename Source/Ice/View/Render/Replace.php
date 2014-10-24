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
use Ice\Core\Config;
use Ice\Core\Loader;
use Ice\Core\Response;
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
 * @version stable_0
 * @since stable_0
 */
class Replace extends View_Render
{
    const TEMPLATE_EXTENTION = '.tpl.txt';

    /**
     * Constructor of replace view render
     *
     * @param Config $config
     */
    protected function __construct(Config $config)
    {
    }

    /**
     * Display rendered view in standard output
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     */
    public function display($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        Response::send($this->fetch($template, $data, $templateType));
    }

    /**
     * Render view via current view render
     *
     * @param $template
     * @param array $data
     * @param string $templateType
     * @return mixed
     */
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
//        $template = Action::getClass($template);

        if ($templateType == View_Render::TEMPLATE_TYPE_FILE) {
            $template = str_replace(['_', '\\'], '/', $template);
            $template = file_get_contents(Loader::getFilePath($template, self::TEMPLATE_EXTENTION, 'Resource/'));
        }

        if (empty($data)) {
            return $template;
        }

        return str_replace(
            array_map(
                function ($var) {
                    return '{$' . $var . '}';
                }, array_keys($data)
            ), array_values($data),
            $template
        );
    }
}