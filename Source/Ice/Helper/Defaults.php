<?php
namespace Ice\Helper;


class Defaults
{
    public static function get($data, array $defaults = array())
    {
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = is_callable($value) ? $value($key) : $value;
            }
        }

        return $data;
    }
}