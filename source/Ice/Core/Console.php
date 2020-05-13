<?php

namespace Ice\Core;

class Console
{
    public static function getCommand($args = [])
    {
        if ($args === null) {
            $args = $_SERVER['argv'];
        }

        $params = [];

        foreach ($args as $param => $value) {
            $params[] = $param . '=' . $value;
        }

        return 'php cli ' . implode(' ', $params);
    }
}