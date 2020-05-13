<?php

namespace Ice\DataProvider;

use Ice\Helper\Json;

class Request_Http_Raw extends Request
{
    /**
     * @param \ArrayObject $connection
     * @return bool
     * @throws \Ice\Exception\Error
     */
    protected function connect(&$connection)
    {
        Registry::connect($connection);

        $input = [];

        if ($rawInput = file_get_contents('php://input')) {
            if ($rawInput[0] === '[' || $rawInput[0] === '{') {
                $input = array_merge($input, Json::decode($rawInput));
            }
        }

        $connection->offsetSet($this->getKeyPrefix(), $input);

        return true;
    }
}