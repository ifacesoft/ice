<?php

namespace Ice\Core;

use Ice\Core;

abstract class Widget_Data extends Widget
{
    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $filterFields = [];
    /**
     * @var int
     */
    private $offset = 0;
    /**
     * @var int
     */
    private $columnCount = 0;
    private $columns = [];
    private $rows = [];
    private $rowHeaderTemplate = 'Row_Header';
    private $rowDataTemplate = 'Row_Data';

    public static function schemeColumnPlugin($columnName, $table)
    {
        return 'text';
    }

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
     * @param array $rows
     * @return Widget_Data
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Build column part
     *
     * @param  $columnName
     * @param  $columnTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Data
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
     * @param  array $filterFields
     * @return Widget_Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function addFilterFields(array $filterFields)
    {
        if (empty($filterFields)) {
            return $this;
        }

        $this->filterFields = array_merge($this->filterFields, $filterFields);
        return $this;
    }

    /**
     * Build link part
     *
     * @param  $columnName
     * @param  $columnTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Data
     */
    public function link($columnName, $columnTitle, $options = [], $template = 'Column_Link')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    /**
     * Build button part
     *
     * @param  $columnName
     * @param  $columnTitle
     * @param  array $options
     * @param  string $template
     * @return Widget_Data
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
        return $this->filterFields;
    }

    /**
     * @return string
     */
    public function getRowHeaderTemplate()
    {
        return $this->rowHeaderTemplate;
    }

    /**
     * @param string $rowHeaderTemplate
     * @return Widget_Data
     */
    public function setRowHeaderTemplate($rowHeaderTemplate)
    {
        $this->rowHeaderTemplate = $rowHeaderTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getRowDataTemplate()
    {
        return $this->rowDataTemplate;
    }

    /**
     * @param string $rowDataTemplate
     * @return Widget_Data
     */
    public function setRowDataTemplate($rowDataTemplate)
    {
        $this->rowDataTemplate = $rowDataTemplate;
        return $this;
    }

    public function bind($key, $value)
    {
        $ascPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_ASC . '$/';
        $descPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_DESC . '$/';

        if (preg_match($ascPattern, $value)) {
            $value = Query_Builder::SQL_ORDERING_ASC;
        } elseif (preg_match($descPattern, $value)) {
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
        $limitQueryPart = $queryResult->getQuery()->getQueryBuilder()->getSqlParts()[Query_Builder::PART_LIMIT];

        $limit = isset($this->getParams()['limit'])
            ? $this->getParams()['limit']
            : $limitQueryPart['limit'];

        $offset = isset($this->getParams()['page'])
            ? $limit * ($this->getParams()['page'] - 1)
            : $limitQueryPart['offset'];

        if ($limit < $queryResult->getNumRows()) {
            $this->setRows(array_slice($queryResult->getRows(), $offset, $limit));
        } else {
            $this->setRows($queryResult->getRows());
        }

        $this->setOffset($offset);
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columnCount;
    }

    /**
     * @param int $columnCount
     * @return Widget_Data
     */
    public function setColumnCount($columnCount)
    {
        $this->columnCount = $columnCount;
        return $this;
    }
}
