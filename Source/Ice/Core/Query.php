<?php
/**
 * Ice core query class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;

/**
 * Class Query
 *
 * Core query class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Query extends Container
{
    use Core;

    /**
     * Target model class
     *
     * @var Model
     */
    private $_modelClass = null;

    /**
     * Query statement type
     *
     * Available statment types: SELECT|INSERT|UPDATE|DELETE
     *
     * @var string
     */
    private $_queryType = null;

    /**
     * Bind parts
     *
     * @var array
     */
    private $_bindParts = [];

    /**
     * Translated query
     *
     * @var string
     */
    private $_sql = null;

    /**
     * Cache tags  (validate|invalidate)
     *
     * @var array
     */
    private $_cacheTags = null;

    /**
     * Data source name
     *
     * @var string
     */
    private $_sourceName = null;

    /**
     * Flag to needs calc found rows query execute
     *
     * @var boolean
     */
    private $_calcFoundRows = null;

    /**
     * Query limits
     *
     * @var array
     */
    private $_limit = null;

    /**
     * Query sql md5 hash
     *
     * @var string
     */
    private $_hash = null;

    /**
     * Serialized bind values
     *
     * @var string
     */
    private $_bindHash = null;

    /**
     * Page, perpage and totalCount
     *
     * @var array
     */
    private $_pagination = null;

    /**
     * Private constructor of query builder. Create: Query::getInstance()->...
     *
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct()
    {
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->_pagination;
    }

    /**
     * @param $foundRows
     */
    public function setPagination($foundRows)
    {
        $limit = $this->getLimit();

        if (!empty($limit)) {
            list($limit, $offset) = $limit;
            $this->_pagination = [$offset / $limit + 1, $limit, $foundRows];
        }
    }

    /**
     * Create new instance of query
     *
     * @param $data
     * @param null $hash
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    protected static function create($data, $hash = null)
    {
        $query = new Query();

        list($sourceName, $queryType, $sqlParts, $modelClass, $cacheTags) = $data;
        $query->_sourceName = empty($sourceName) ? Data_Source::getDefaultKey() : $sourceName;
        $query->_queryType = $queryType;
        $queryTranslator = Query_Translator::getInstance(get_class(Data_Source::getInstance($query->_sourceName)));
        $query->_sql = $queryTranslator->translate($sqlParts);
        $query->_modelClass = $modelClass;
        $query->_cacheTags = $cacheTags;
        $query->_calcFoundRows = reset($sqlParts[Query_Builder::PART_SELECT]);
        $query->_limit = $sqlParts[Query_Builder::PART_LIMIT];
        $query->_hash = $hash;

        return $query;
    }

    /**
     * Return query limits
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Return calc found rows flag
     *
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function isCalcFoundRows()
    {
        return $this->_calcFoundRows;
    }

    /**
     * Return data source name
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getDataSource()
    {
        return Data_Source::getInstance($this->_sourceName);
    }

    /**
     * Bind values
     *
     * @param array $bindParts
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function bind(array $bindParts)
    {
        $this->_bindParts = $bindParts;

        if ($this->getQueryType() == Query_Builder::TYPE_SELECT) {
            $this->_bindHash = md5(json_encode($bindParts));
        }

        return $this;
    }

    /**
     * Return query statement type
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getQueryType()
    {
        return $this->_queryType;
    }

    /**
     * Get bind params
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function getBinds()
    {
        $binds = [];

        foreach ($this->getBindParts() as $bindPart) {
            if (!is_array(reset($bindPart))) {
                $binds = array_merge($binds, array_values($bindPart));
                continue;
            }

            foreach ($bindPart as $values) {
                $binds = array_merge($binds, array_values($values));
                continue;
            }
        }

        return $binds;
    }

    /**
     * Return rows
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getBindParts()
    {
        return $this->_bindParts;
    }

    /**
     * Return translated query
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getSql()
    {
        return $this->_sql;
    }

    /**
     * Return model class
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * Return validate tags
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValidateTags()
    {
        return $this->_cacheTags['validate'];
    }

    /**
     * Return invalidate tags
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getInvalidateTags()
    {
        return $this->_cacheTags['invalidate'];
    }

    /**
     * Return full hash of query
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFullHash()
    {
        if ($this->_bindHash === null) {
            Query::getLogger()->fatal('Bind hash is empty', __FILE__, __LINE__, null, $this);
        }

        return $this->_hash . '/' . $this->_bindHash;
    }

    public function execute($ttl = 3600)
    {
        return $this->getDataSource()->execute($this, $ttl);
    }

    public static function __set_state(array $data)
    {
        $query = new Query();

        foreach ($data as $fieldName => $fieldValue) {
            $query->$fieldName = $fieldValue;
        }

        return $query;
    }
}