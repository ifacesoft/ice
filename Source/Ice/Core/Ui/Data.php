<?php

namespace Ice\Core;

use Ice\Core;

abstract class Ui_Data extends Container
{
    use Stored;

    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $_filterFields = [];

    private $_title = 'Title';
    private $columns = [];
    private $rows = [];

    private $key = null;

    private function __construct()
    {
    }

    /**
     * Return instance of Data
     *
     * @param string $key
     * @param null $ttl
     * @return Ui_Data
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
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
     * @return Ui_Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.2
     */
    protected static function create($key)
    {
        $class = self::getClass();
//
//        if ($key) {
//            $class .= '_' . $key;
//        }

        $data = new $class();

        $data->key = $key;

        return $data;
    }

    protected static function getDefaultClassKey()
    {
        return 'Ice:Simple';
    }

    protected static function getDefaultKey()
    {
        return 'default';
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
     * @return Ui_Data
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
     * @return Ui_Data
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

    public static function schemeColumnPlugin($columnName, $table) {
        return 'text';
    }

    public function title($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }
}