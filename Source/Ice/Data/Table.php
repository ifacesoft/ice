<?php

namespace Ice\Data;

use Ice\Core\Data;

class Table extends Data {

    public function column($columnName, $columnTitle, $template = 'Ice:Table_Column_Column') {
        return $this->addColumn($columnName, 'column', $columnTitle, $template);
    }

    public function link($columnName, $columnTitle, $template = 'Ice:Table_Column_Link') {
        return $this->addColumn($columnName, 'link', $columnTitle, $template);
    }

    public function button($columnName, $columnTitle, $template = 'Ice:Table_Column_Button') {
        return $this->addColumn($columnName, 'button', $columnTitle, $template);
    }
}