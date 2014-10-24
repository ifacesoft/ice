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
 * @version stable_0
 * @since stable_0
 */
class Query extends Container
{
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
     * Inserted rows
     *
     * @var array
     */
    private $_insertRows = null;

    /**
     * Translated query
     *
     * @var string
     */
    private $_sql = null;

    /**
     * Bind values
     *
     * @var array
     */
    private $_binds = null;

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
     */
    private function __construct($data, $hash = null)
    {
        list($sourceName, $queryType, $sqlParts, $modelClass, $cacheTags) = $data;
        $this->_sourceName = empty($sourceName) ? Data_Source::getDefaultKey() : $sourceName;
        $this->_queryType = $queryType;
        $queryTranslator = Query_Translator::getInstance(get_class(Data_Source::getInstance($sourceName)));
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
     */
    protected static function create($data, $hash = null)
    {
        return new Query($data, $hash);
    }

    /**
     * Return query limits
     *
     * @return array
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Return calc found rows flag
     *
     * @return boolean
     */
    public function isCalcFoundRows()
    {
        return $this->_calcFoundRows;
    }

    /**
     * Return data source name
     *
     * @return string
     */
    public function getDataSource()
    {
        return Data_Source::getInstance($this->_sourceName);
    }

    /**
     * Bind values
     *
     * @param array $bindParts
     * @param array $rows
     * @return Query
     */
    public function bind(array $bindParts, array $rows)
    {
        $this->_binds = [];

        foreach ($bindParts as $bindPart) {
            if (!is_array(reset($bindPart))) {
                $this->_binds = array_merge($this->_binds, $bindPart);
                continue;
            }

            foreach ($bindPart as $values) {
                $this->_binds = array_merge($this->_binds, $values);
                continue;
            }
        }

        if ($this->getQueryType() == Query_Builder::TYPE_SELECT) {
            $this->_bindHash = serialize($this->_binds);
        }

        $this->_insertRows = $rows;

        return $this;
    }

    /**
     * Return query statement type
     *
     * @return string
     */
    public function getQueryType()
    {
        return $this->_queryType;
    }

    /**
     * Return bind values
     *
     * @return array
     */
    public function getBinds()
    {
        return $this->_binds;
    }

    /**
     * Return translated query
     *
     * @return string
     */
    public function getSql()
    {
        return $this->_sql;
    }

    /**
     * Return model class
     *
     * @return Model
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * Return insert rows
     *
     * @return array
     */
    public function getInsertRows()
    {
        return $this->_insertRows;
    }

    /**
     * Return validate tags
     *
     * @return array
     */
    public function getValidateTags()
    {
        return $this->_cacheTags['validate'];
    }

    /**
     * Return invalidate tags
     *
     * @return array
     */
    public function getInvalidateTags()
    {
        return $this->_cacheTags['invalidate'];
    }

    /**
     * Return data of query
     *
     * @param int $ttl
     * @throws Exception
     * @return Data
     */
    public function getData($ttl = 3600)
    {
        return Data::getInstance([$this, $ttl]);
    }

    /**
     * Return full hash of query
     *
     * @return string
     */
    public function getFullHash()
    {
        if ($this->_bindHash === null) {
            Query::getLogger()->fatal('Bind hash is empty', __FILE__, __LINE__, null, $this);
        }

        return $this->_hash . '/' . $this->_bindHash;
    }
}