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
use Ice\Data\Provider\Cacher;
use Ice\Helper\Json;

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
 */
class Query
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
    private $_dataSourceKey = null;

    /**
     * Sql parts
     *
     * @var array
     */
    private $_bodyParts = null;

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
     * Private constructor of query builder. Create: Query::create()->...
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
     * Create new instance of query
     *
     * @param $key
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @todo Need caching
     * @version 0.4
     * @since 0.0
     */
    public static function create($key)
    {
        $query = new Query();
        $query->_hash = md5(Json::encode($key));

        list($dataSourceKey, $queryType, $sqlParts, $modelClass, $cacheTags) = $key;

        if (!$dataSourceKey) {
            $dataSourceKey = $modelClass::getDataSourceKey();
        }

        $query->_dataSourceKey = $dataSourceKey;
        $query->_queryType = $queryType;
        $query->_modelClass = $modelClass;
        $query->_cacheTags = $cacheTags;
        $query->_bodyParts = $sqlParts;

        return $query;
    }

    /**
     * Restore object
     *
     * @param array $data
     * @return object
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public static function __set_state(array $data)
    {
        $object = new self();

        foreach ($data as $fieldName => $fieldValue) {
            $object->$fieldName = $fieldValue;
        }

        return $object;
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
     * Return query limits
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function getLimit()
    {
        return $this->_bodyParts[Query_Builder::PART_LIMIT];
    }

    /**
     * Return calc found rows flag
     *
     * @return boolean
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function isCalcFoundRows()
    {
        return reset($this->_bodyParts[Query_Builder::PART_SELECT]);
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
     * Return data source name
     *
     * @return Data_Source
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function getDataSource()
    {
        return Data_Source::getInstance($this->getDataSourceKey());
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
     * Return query cacher
     *
     * @return Cacher
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.5
     */
    public function getCacher()
    {
        return Cacher::getInstance(__CLASS__);
    }

    /**
     * Return cache tags
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.5
     * @since 0.0
     */
    public function getCacheTags()
    {
        return $this->_cacheTags;
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
            Query::getLogger()->exception('Bind hash is empty', __FILE__, __LINE__, null, $this);
        }

        return $this->_hash . '/' . $this->_bindHash;
    }

    /**
     * Execute query
     *
     * @param int $ttl
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function execute($ttl = 3600)
    {
        return $this->getDataSource()->execute($this, $ttl);
    }

    /**
     * Return data source key of query
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getDataSourceKey()
    {
        return $this->_dataSourceKey;
    }

    /**
     * Return query body parts
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.4
     */
    public function getBodyParts()
    {
        return $this->_bodyParts;
    }
}