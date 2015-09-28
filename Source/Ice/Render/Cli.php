<?php
/**
 * Ice view render implementation cli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Render;

use Ice\Core\Render;

/**
 * Class Cli
 *
 * Implementation view render cli "template"
 *
 * @see Ice\Core\Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage View_Render
 */
class Cli extends Render
{
    /**
     * Render view via current view render
     *
     * @param  $template
     * @param  array $data
     * @param  string $templateType
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function fetch($template, array $data = [], $templateType = Render::TEMPLATE_TYPE_FILE)
    {
        return $data['cli'];
    }

    protected function init(array $params)
    {
    }
}
