<?php

namespace Ice\DataProvider;

use Ice\Helper\Json;

class Request_Http_RawQueryString extends Request
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
            if ($rawInput[0] !== '[' && $rawInput[0] !== '{') {
                $parced = [];
                parse_str($rawInput, $parced);
                $input = array_merge($input, $parced);
            }
        }


        $connection->offsetSet($this->getKeyPrefix(), $input);

        return true;
    }
}