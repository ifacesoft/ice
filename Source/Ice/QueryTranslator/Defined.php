<?php
/**
 * Ice query translator implementation defined class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\QueryTranslator;

use Ice\Core\Exception;
use Ice\Core\QueryTranslator;

/**
 * Class Defined
 *
 * Translate with query translator defined
 *
 * @see Ice\Core\QueryTranslator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage QueryTranslator
 *
 * @version 0.0
 * @since   0.0
 */
class Defined extends QueryTranslator
{
    /**
     * Translate query parts to sql string
     *
     * @param  array $sqlParts
     * @return string
     * @throws Exception
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function translate(array $sqlParts)
    {
        return 'Not Implements';
    }
}
