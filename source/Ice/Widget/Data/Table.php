<?php

namespace Ice\Widget\Data;

use Ice\Core\Debuger;
use Ice\Core\Query_Builder;
use Ice\Core\Route;
use Ice\Core\Widget_Data;
use Ice\Helper\Json;
use Ice\Render\Php;

class Table extends Widget_Data
{
    private $headerStyle = '';

    /**
     * @param string $headerStyle
     * @return Table
     */
    public function setHeaderStyle($headerStyle)
    {
        $this->headerStyle = $headerStyle;
        return $this;
    }

    public function render()
    {
        /**
         * @var Widget_Data $dataClass
         */
        $dataClass = get_class($this);

        return Php::getInstance()->fetch(
            Widget_Data::getClass($dataClass),
            [
                'rows' => $this->prepareRows($dataClass, $this->prepareColums($dataClass)),
                'classes' => $this->getClasses(),
                'style' => $this->getStyle(),
            ]
        );
    }

    private function prepareRows($dataClass, $columns)
    {
        $rows = [];

        $rows[] = Php::getInstance()->fetch(
            Widget_Data::getClass($dataClass . '_' . $this->getRowHeaderTemplate()),
            [
                'columns' => $columns,
                'columnCount' => $this->getColumnCount() ? $this->getColumnCount() : count($columns),
                'headerStyle' => $this->headerStyle
            ]
        );

        $offset = $this->getOffset();

        foreach ($this->getRows() as $row) {
            $rowResult = [];

            foreach ($columns as $columnName => $column) {
                if (isset($column['options']['href']) && isset($column['options']['href_ext'])) {
                    $column['options']['href'] .= implode('/', array_intersect_key($row, array_flip((array)$column['options']['href_ext'])));
                } elseif (isset($column['options']['routeName'])) {
                    $routeName = isset($row[$column['options']['routeName']])
                        ? $row[$column['options']['routeName']]
                        : $column['options']['routeName'];

                    $routeParams = isset($column['options']['routeParams'])
                        ? $column['options']['routeParams']
                        : [];

                    if (is_string($routeParams) && isset($row[$routeParams])) {
                        $routeParams = $row[$routeParams];
                    }

                    $column['options']['href'] = Route::getInstance($routeName)->getUrl($routeParams);
                }

                $column['value'] = array_key_exists($columnName, $row) ? $row[$columnName] : $columnName;

                $rowResult[$columnName] = Php::getInstance()->fetch(Widget_Data::getClass($dataClass . '_' . $column['template']), $column);
            }

            $rows[] = Php::getInstance()->fetch(
                Widget_Data::getClass($dataClass . '_' . $this->getRowDataTemplate()),
                [
                    'columns' => $columns,
                    'rowResult' => $rowResult,
                    'id' => ++$offset,
                    'columnCount' => $this->getColumnCount() ? $this->getColumnCount() : count($columns)
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
        $dataName = 'Data_' . $dataClass::getClassName();

        $columns = $this->getColumns();

        $columnNames = array_keys($columns);

        if ($filterFields = $this->getFilterFields()) {
            $columnNames = array_intersect($columnNames, $filterFields);
        }

        $columns = array_intersect_key($columns, array_flip($columnNames));

        foreach ($columns as $columnName => &$column) {
            $column['dataName'] = $dataName;
            $column['name'] = $columnName;
            $column['href'] = $this->getUrl();
            $column['dataUrl'] = $this->getUrl();
            $column['dataJson'] = Json::encode($this->getParams());
            $column['dataAction'] = $this->getAction();
            $column['dataBlock'] = $this->getBlock();
            $column['dataValue'] = $this->getValues($columnName);

            if ($this->getValues($columnName) == '') {
                $ordering = Query_Builder::SQL_ORDERING_ASC;
            } elseif ($this->getValues($columnName) == Query_Builder::SQL_ORDERING_ASC) {
                $ordering = Query_Builder::SQL_ORDERING_DESC;
            } else {
                $ordering = '';
            }

            $column['onclick'] = 'Ice_Widget_Data.click($(this), "' . $ordering . '"); return false;';
        }
        unset($column); // #^###%@@#@$% PHP

        return $columns;
    }
}
