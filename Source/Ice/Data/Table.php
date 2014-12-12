<?php

namespace Ice\Data;

use Ice\Core\Data;

class Table extends Data {

    /**
     * Build column part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $option
     * @param string $template
     * @return Table
     */
    public function column($columnName, $columnTitle, $option = null, $template = 'Ice:Table_Column_Column') {
        return $this->addColumn($columnName, 'column', $columnTitle, $option, $template);
    }

    /**
     * Build link part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $option
     * @param string $template
     * @return Table
     */
    public function link($columnName, $columnTitle, $option = null, $template = 'Ice:Table_Column_Link') {
        return $this->addColumn($columnName, 'link', $columnTitle, $option, $template);
    }

    /**
     * Build button part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $option
     * @param string $template
     * @return Table
     */
    public function button($columnName, $columnTitle, $option = null, $template = 'Ice:Table_Column_Button') {
        return $this->addColumn($columnName, 'button', $columnTitle, $option, $template);
    }

    /**
     * Return instance of table data
     *
     * @param null $key
     * @param null $ttl
     * @return Table
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }
}