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
    private $_rowHeaderTemplate = 'Row_Header';
    private $_rowDataTemplate = 'Row_Data';

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
        return 'Ice:Table';
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
     * @param null $options
     * @param string $template
     * @return Ui_Data
     */
    public function text($columnName, $columnTitle, $options = null, $template = 'Column_Column')
    {
        return $this->addColumn($columnName, 'column', $columnTitle, $options, $template);
    }

    protected function addColumn($columnName, $columnType, $columnTitle, $options, $template)
    {
        $this->columns[] = [
            'name' => $columnName,
            'type' => $columnType,
            'title' => $columnTitle,
            'options' => $options,
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
     * @param null $options
     * @param string $template
     * @return Ui_Data
     */
    public function link($columnName, $columnTitle, $options = null, $template = 'Column_Link')
    {
        return $this->addColumn($columnName, 'link', $columnTitle, $options, $template);
    }

    /**
     * Build button part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $options
     * @param string $template
     * @return Ui_Data
     */
    public function button($columnName, $columnTitle, $options = null, $template = 'Column_Button')
    {
        return $this->addColumn($columnName, 'button', $columnTitle, $options, $template);
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

    /**
     * @return string
     */
    public function getRowHeaderTemplate()
    {
        return $this->_rowHeaderTemplate;
    }

    /**
     * @param string $rowHeaderTemplate
     * @return Ui_Data
     */
    public function setRowHeaderTemplate($rowHeaderTemplate)
    {
        $this->_rowHeaderTemplate = $rowHeaderTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getRowDataTemplate()
    {
        return $this->_rowDataTemplate;
    }

    /**
     * @param string $rowDataTemplate
     * @return Ui_Data
     */
    public function setRowDataTemplate($rowDataTemplate)
    {
        $this->_rowDataTemplate = $rowDataTemplate;
        return $this;
    }


}