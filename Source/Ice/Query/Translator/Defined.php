<?php
/**
 * Ice query translator implementation defined class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Query\Translator;

use Ice\Core\Exception;
use Ice\Core\Query_Translator;

/**
 * Class Defined
 *
 * Translate with query translator defined
 *
 * @see Ice\Core\Query_Translator
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Query_Translator
 *
 * @version stable_0
 * @since stable_0
 */
class Defined extends Query_Translator
{
    /**
     * Translate query parts to sql string
     *
     * @param array $sqlParts
     * @return string
     * @throws Exception
     */
    public function translate(array $sqlParts)
    {
        return 'Not Implements';
    }
}