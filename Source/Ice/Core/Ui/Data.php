<?php

namespace Ice\Core;

use Ice\Core;

abstract class Ui_Data extends Ui
{
    private $offset = 0;

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $_filterFields = [];

    private $columns = [];
    private $rows = [];
    private $_rowHeaderTemplate = 'Row_Header';
    private $_rowDataTemplate = 'Row_Data';

    public function setRows(array $rows)
    {
        $this->rows = $rows;
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
     * Build column part
     *
     * @param $columnName
     * @param $columnTitle
     * @param array $options
     * @param string $template
     * @return Ui_Data
     */
    public function text($columnName, $columnTitle, $options = [], $template = 'Column_Column')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    protected function addColumn($columnName, $columnTitle, $options, $template)
    {
        $this->columns[$columnName] = [
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
     * @return Ui_Data
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
     * @param array $options
     * @param string $template
     * @return Ui_Data
     */
    public function link($columnName, $columnTitle, $options = [], $template = 'Column_Link')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    /**
     * Build button part
     *
     * @param $columnName
     * @param $columnTitle
     * @param array $options
     * @param string $template
     * @return Ui_Data
     */
    public function button($columnName, $columnTitle, $options = [], $template = 'Column_Button')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    /**
     * @return array
     */
    public function getFilterFields()
    {
        return $this->_filterFields;
    }

    public static function schemeColumnPlugin($columnName, $table)
    {
        return 'text';
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

    public function bind($key, $value)
    {
        $ascPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_ASC . '$/';
        $descPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_DESC . '$/';

        if (preg_match($ascPattern, $value)) {
            $value = Query_Builder::SQL_ORDERING_ASC;
        } else if (preg_match($descPattern, $value)) {
            $value = Query_Builder::SQL_ORDERING_DESC;
        } else {
            $value = '';
        }

        if (isset($this->columns[$key])) {
            if (empty($value) && isset($this->columns[$key]['options']['default'])) {
                $value = $this->columns[$key]['options']['default'];
            }

            $this->addValue($key, $value);
        }

        return $value;
    }

    public function setQueryResult(Query_Result $queryResult)
    {
        $this->setRows($queryResult->getRows());
        $this->setOffset($queryResult->getQuery()->getQueryBuilder()->getSqlParts()[Query_Builder::PART_LIMIT]['offset']);
    }
}