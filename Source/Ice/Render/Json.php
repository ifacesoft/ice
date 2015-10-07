<?php
/**
 * Ice view render implementation json class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Render;
use Ice\Helper\Json as Helper_Json;

/**
 * Class Json
 *
 * Implementation view render json template
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
class Json extends Render
{
    /**
     * Render view via current view render
     *
     * @param string $template
     * @param  array $data
     * @param null $layout
     * @param string $templateType
     * @return mixed
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 2.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $layout = null, $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        return Helper_Json::encode($data);
    }

    protected function init(array $params)
    {
    }
}
