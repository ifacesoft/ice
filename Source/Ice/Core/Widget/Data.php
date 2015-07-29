<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Json;
use Ice\Helper\Object;
use Ice\Helper\String;
use Ice\View\Render\Php;

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
    protected $headerRowStyle = '';
    private $isShowCount = true;
    private $topRow = [];
    private $bottomRow = [];

    protected $defaultOptions = [];

    public static function schemeColumnPlugin($columnName, $table)
    {
        return 'text';
    }

    /**
     * @param string $headerRowStyle
     * @return Widget_Data
     */
    public function setHeaderRowStyle($headerRowStyle)
    {
        $this->headerRowStyle = $headerRowStyle;
        return $this;
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
    public function text($columnName, $columnTitle, $options = [], $template = 'Ice\Core\Widget_Text')
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
    public function a($columnName, $columnTitle, array $options = [], $template = 'Ice\Core\Widget_A')
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
    public function button($columnName, $columnTitle, array $options = [], $template = 'Ice\Core\Widget_Button')
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

//    /**
//     * @param array $params
//     * @return Widget_Data
//     */
//    public function bind(array $params)
//    {
//        foreach ($params as $key => $value) {
//
//            $ascPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_ASC . '$/';
//            $descPattern = '/(?:[^\/]+\/)?' . Query_Builder::SQL_ORDERING_DESC . '$/';
//
//            if (preg_match($ascPattern, $value)) {
//                $value = Query_Builder::SQL_ORDERING_ASC;
//            } elseif (preg_match($descPattern, $value)) {
//                $value = Query_Builder::SQL_ORDERING_DESC;
//            } else {
//                $value = '';
//            }
//
//            if (isset($this->columns[$key])) {
//                if (empty($value) && isset($this->columns[$key]['options']['default'])) {
//                    $value = $this->columns[$key]['options']['default'];
//                }
//
//                $this->addValue($key, $value);
//            }
//        }
//
//        return $this;
//    }

    public function setQueryResult(Query_Result $queryResult)
    {
        $limitQueryPart = $queryResult->getQuery()->getQueryBuilder()->getSqlParts()[Query_Builder::PART_LIMIT];

        $limit = isset($this->getParams()['limit'])
            ? $this->getParams()['limit']
            : $limitQueryPart['limit'];

        $offset = isset($this->getParams()['page'])
            ? $limit * ($this->getParams()['page'] - 1)
            : $limitQueryPart['offset'];

        if ($limit && $limit < $queryResult->getNumRows()) {
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

    public function queryBuilderPart(Query_Builder $queryBuilder)
    {
    }

    public function render()
    {
        /** @var Widget_Data $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();
        $widgetBaseClass = Object::getBaseClass($widgetClass, Widget::getClass());
        $widgetBaseClassName = $widgetBaseClass::getClassName();

        $columns = $this->prepareColums($widgetClass);

        $headerRow = Php::getInstance()->fetch(
            Widget_Data::getClass($widgetClass . '_' . $this->getRowHeaderTemplate()),
            [
                'columns' => $columns,
                'columnCount' => $this->getColumnCount() ? $this->getColumnCount() : count($columns),
                'headerRowStyle' => $this->headerRowStyle,
                'isShowCount' => $this->isShowCount(),
            ]
        );

        return Php::getInstance()->fetch(
            empty($this->getTemplate()) ? $widgetClass : $this->getTemplate(),
            [
                'widgetData' => $this->getData(),
                'widgetClass' => $widgetClass,
                'widgetClassName' => $widgetClassName,
                'widgetBaseClassName' => $widgetBaseClassName,
                'headerRow' => $headerRow,
                'rows' => $this->prepareRows($widgetClass, $columns),
                'classes' => $this->getClasses(),
                'style' => $this->getStyle(),
                'header' => $this->getHeader(),
                'description' => $this->getDescription(),
                'topRow' => $this->getTopRow(),
                'bottomRow' => $this->getBottomRow()
            ]
        );
    }

    private function prepareRows($dataClass, $parts)
    {
        $rows = [];

        $offset = $this->getOffset();

        foreach ($this->getRows() as $values) {
            $rowResult = [];

            foreach ($parts as $partName => $part) {
                $part['name'] = isset($part['options']['name']) ? $part['options']['name'] : $partName;

                $params = [];

                if (isset($part['options']['params'])) {
                    foreach ((array)$part['options']['params'] as $key => $param) {
                        if (is_int($key)) {
                            $params[$param] = $values[$param];
                        } else {
                            $params[$key] = array_key_exists($key, $values) ? $values[$param] : $param;
                        }
                    }
                } else {
                    $params = [$part['name'] => isset($values[$part['name']]) ? $values[$part['name']] : null];
                }

                if (isset($part['options']['title'])) {
                    $part['title'] = implode($part['options']['title'], $params);
                } else {
                    if (array_key_exists($partName, $values)) {
                        $part['title'] = $values[$partName];
                    }
                }

                $part['params'] = $params;
                $part['dataParams'] = Json::encode($params);

                if (!empty($part['options']['route'])) {
                    if (is_array($part['options']['route'])) {
                        list($routeName, $routeParams) = each($part['options']['route']);

                        $routeParams = array_merge($part['params'], (array)$routeParams);
                    } else {
                        $routeParams = $part['params'];

                        $routeName = $part['options']['route'] === true
                            ? $partName
                            : $part['options']['route'];
                    }

                    $part['options']['href'] = $this->getFullUrl(Router::getInstance()->getUrl($routeName, $routeParams));
                }

                $part['offset'] = $offset + 1;

                $template = $part['template'][0] == '_'
                    ? $dataClass . $part['template']
                    : $part['template'];

                $rowResult[$partName] = Php::getInstance()->fetch($template, $part);
            }

            $rows[] = Php::getInstance()->fetch(
                Widget_Data::getClass($dataClass . '_' . $this->getRowDataTemplate()),
                [
                    'columns' => $parts,
                    'rowResult' => $rowResult,
                    'id' => ++$offset,
                    'columnCount' => $this->getColumnCount() ? $this->getColumnCount() : count($parts),
                    'isShowCount' => $this->isShowCount()
                ]
            );
        }

        return $rows;
    }

    /**
     * @param Widget_Data $dataClass
     * @return array
     */
    private function prepareColums($dataClass)
    {
        /** @var Widget $widgetClass */
        $widgetClass = get_class($this);
        $widgetClassName = $widgetClass::getClassName();
        $widgetBaseClass = Object::getBaseClass($widgetClass, Widget::getClass());
        $widgetBaseClassName = $widgetBaseClass::getClassName();

        $dataName = 'Data_' . $dataClass::getClassName();

        $columns = $this->getColumns();

        $columnNames = array_keys($columns);

        if ($filterFields = $this->getFilterFields()) {
            $columnNames = array_intersect($columnNames, $filterFields);
        }

        $columns = array_intersect_key($columns, array_flip($columnNames));

        foreach ($columns as $columnName => &$column) {
            $column['widgetClassName'] = $widgetClassName;
            $column['widgetBaseClassName'] = $widgetBaseClassName;
            $column['token'] = $this->getToken();

            $column['dataName'] = $dataName;
            $column['name'] = $columnName;
            $column['href'] = $this->getUrl();
            $column['dataUrl'] = $this->getUrl();
            $column['dataJson'] = Json::encode($this->getParams());
            $column['dataAction'] = $this->getAction();
            $column['dataBlock'] = $this->getBlock();
            $column['dataValue'] = $this->getValue($columnName);

            if ($this->getValue($columnName) == '') {
                $ordering = Query_Builder::SQL_ORDERING_ASC;
            } elseif ($this->getValue($columnName) == Query_Builder::SQL_ORDERING_ASC) {
                $ordering = Query_Builder::SQL_ORDERING_DESC;
            } else {
                $ordering = '';
            }

            $column['onclick'] = 'Ice_Widget_Data.click($(this), "' . $ordering . '"); return false;';
        }

        return $columns;
    }

    /**
     * @return boolean
     */
    public function isShowCount()
    {
        return $this->isShowCount;
    }

    /**
     * @param boolean $isShowCount
     * @return $this
     */
    public function setShowCount($isShowCount)
    {
        $this->isShowCount = $isShowCount;
        return $this;
    }

    /**
     * @return array
     */
    public function getTopRow()
    {
        return $this->topRow;
    }

    /**
     * @param array $topRow
     */
    public function setTopRow(array $topRow)
    {
        $this->topRow = $topRow;
    }

    /**
     * @return array
     */
    public function getBottomRow()
    {
        return $this->bottomRow;
    }

    /**
     * @param array $bottomRow
     */
    public function setBottomRow(array $bottomRow)
    {
        $this->bottomRow = $bottomRow;
    }
}
