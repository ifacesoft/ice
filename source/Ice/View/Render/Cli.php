<?php
/**
 * Ice view render implementation cli class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\View\Render;

use Ice\Core\View_Render;

/**
 * Class Cli
 *
 * Implementation view render cli "template"
 *
 * @see Ice\Core\View_Render
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage View_Render
 *
 * @version 0.0
 * @since   0.0
 */
class Cli extends View_Render
{
    /**
     * Constructor of cli view render
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    protected function __construct()
    {
    }

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
    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        return $data['cli'];
    }
}
