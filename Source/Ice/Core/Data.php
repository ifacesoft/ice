<?php

namespace Ice\Core;

use Ice\Core;

abstract class Data extends Container
{
    use Core;

    private $columns = [];
    private $rows = [];

    private $key = null;

    private function __construct($key)
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
     * @param null $hash
     * @return Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    protected static function create($key, $hash = null)
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

    protected function addColumn($columnName, $columnType, $columnTitle, $template)
    {
        $this->columns[] = [
            'name' => $columnName,
            'type' => $columnType,
            'title' => $columnTitle,
            'template' => $template
        ];

        return $this;
    }

}