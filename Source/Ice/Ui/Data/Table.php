<?php

namespace Ice\Ui\Data;

use Ice\Core\Query_Builder;
use Ice\Core\Ui_Data;
use Ice\Helper\Json;
use Ice\View\Render\Php;

class Table extends Ui_Data {
    public function render()
    {
        /** @var Ui_Data $dataClass */
        $dataClass = get_class($this);
        $dataName = 'Data_' . $dataClass::getClassName();

        $rows = [];

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
            } else if ($this->getValues($columnName) == Query_Builder::SQL_ORDERING_ASC) {
                $ordering = Query_Builder::SQL_ORDERING_DESC;
            } else {
                $ordering = '';
            }

            $column['onclick'] = 'Ice_Ui_Data.click($(this), "' . $ordering . '"); return false;';
        }
        unset($column); // #^###%@@#@$% PHP


        $rows[] = Php::getInstance()->fetch(Ui_Data::getClass($dataClass . '_' . $this->getRowHeaderTemplate()), ['columns' => $columns]);

        $offset = $this->getOffset();

        foreach ($this->getRows() as $row) {
            $rowResult = [];

            foreach ($columns as $columnName => $column) {
                if (isset($column['options']['href']) && isset($column['options']['href_ext'])) {
                    $column['options']['href'] .= implode('/', array_intersect_key($row, array_flip((array)$column['options']['href_ext'])));
                }

                $column['value'] = array_key_exists($columnName, $row) ? $row[$columnName] : $columnName;

                $rowResult[] = Php::getInstance()->fetch(Ui_Data::getClass($dataClass . '_' . $column['template']), $column);
            }

            $rows[] = Php::getInstance()->fetch(
                Ui_Data::getClass($dataClass . '_' . $this->getRowDataTemplate()),
                [
                    'columns' => $rowResult,
                    'id' => ++$offset
                ]
            );
        }

        return Php::getInstance()->fetch(
            Ui_Data::getClass($dataClass),
            [
                'rows' => $rows,
                'classes' => $this->getClasses(),
                'style' => $this->getStyle()
            ]
        );
    }
}