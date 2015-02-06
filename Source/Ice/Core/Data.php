<?php

namespace Ice\Core;

use Ice\Core;

abstract class Data extends Container
{
    use Core;

    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $_filterFields = [];

    private $columns = [];
    private $rows = [];

    private $key = null;

    protected function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Return instance of Data
     *
     * @param null $key
     * @param null $ttl
     * @return Data
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    /**
     * Create new instance of data
     *
     * @param $key
     * @return Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    protected static function create($key)
    {
        /** @var Data $class */
        $class = self::getClass();

        if ($class == __CLASS__) {
            $class = 'Ice\Data\\' . $key;
        }

        return new $class($key);
    }

    public function bind(array $rows)
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return Data
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Build column part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $option
     * @param string $template
     * @return Data
     */
    public function text($columnName, $columnTitle, $option = null, $template = 'Ice:Table_Column_Column')
    {
        return $this->addColumn($columnName, 'column', $columnTitle, $option, $template);
    }

    protected function addColumn($columnName, $columnType, $columnTitle, $option, $template)
    {
        $this->columns[] = [
            'name' => $columnName,
            'type' => $columnType,
            'title' => $columnTitle,
            'option' => $option,
            'template' => $template
        ];

        return $this;
    }

    /**
     * Add accepted fields
     *
     * @param array $filterFields
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function addFilterFields(array $filterFields)
    {
        if (empty($filterFields)) {
            return $this;
        }

        $this->_filterFields = array_merge($this->_filterFields, $filterFields);
        return $this;
    }

    /**
     * Build link part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $option
     * @param string $template
     * @return Data
     */
    public function link($columnName, $columnTitle, $option = null, $template = 'Ice:Table_Column_Link')
    {
        return $this->addColumn($columnName, 'link', $columnTitle, $option, $template);
    }

    /**
     * Build button part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $option
     * @param string $template
     * @return Data
     */
    public function button($columnName, $columnTitle, $option = null, $template = 'Ice:Table_Column_Button')
    {
        return $this->addColumn($columnName, 'button', $columnTitle, $option, $template);
    }

    /**
     * @return array
     */
    public function getFilterFields()
    {
        return $this->_filterFields;
    }


}