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
     * Private constructor of query builder. Create: Query::getInstance()->...
     *
     * @param $data
     * @param $hash
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($data, $hash = null)
    {
        list($sourceName, $queryType, $sqlParts, $modelClass, $cacheTags) = $data;
        $this->_sourceName = empty($sourceName) ? Data_Source::getDefaultKey() : $sourceName;
        $this->_queryType = $queryType;
        $queryTranslator = Query_Translator::getInstance(get_class(Data_Source::getInstance($this->_sourceName)));
        $this->_sql = $queryTranslator->translate($sqlParts);
        $this->_modelClass = $modelClass;
        $this->_cacheTags = $cacheTags;
        $this->_calcFoundRows = reset($sqlParts[Query_Builder::PART_SELECT]);
        $this->_limit = $sqlParts[Query_Builder::PART_LIMIT];
        $this->_hash = $hash;
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
     * @version 0.0
     * @since 0.0
     */
    protected static function create($data, $hash = null)
    {
        return new Query($data, $hash);
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
            $this->_bindHash = serialize($bindParts);
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
}