<?php

namespace Ice\Core;

use Ice\Core;

class Container extends Factory
{
    private $_class = null;

    private function __construct($class)
    {
        $this->_class = $class;
    }

    /**
     * Return instance of resource for self class
     *
     * @return Resource
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function getResource()
    {
        return Resource::getInstance($this->_class);
    }

    /**
     * Create new instance of container
     *
     * @param $key
     * @param null $hash
     * @return Container
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected static function create($key, $hash = null)
    {
        return new Container($key);
    }
}