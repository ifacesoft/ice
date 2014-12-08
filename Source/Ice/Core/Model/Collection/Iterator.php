<?php
/**
 * Ice core model collection iterator class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core\Model\Collection;

use Ice\Core\Query_Result;
use Ice\Core\Model;

/**
 * Iterator for model collection
 *
 * @package Ice\Core\Model\Collection
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.0
 * @since 0.0
 */
class Iterator extends Query_Result
{

    /**
     * Constructor of model collection iterator
     *
     * @param Query_Result $data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    function __construct(Query_Result $data)
    {
        $this->setResult($data->getResult());
    }

    /**
     * Set iterator data result
     *
     * @param array $result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function setResult($result)
    {
        $this->_result = $result;
    }

    /**
     * Return the current row of iterator
     *
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function current()
    {
        /** @var Model $modelClass */
        $modelClass = $this->_result[Query_Result::RESULT_MODEL_CLASS];
        return $modelClass::create(parent::current());
    }
}