<?php

namespace Ice\Core;

abstract class Form_Security_Login extends Form
{
    /**
     * Create new instance of form security login
     *
     * @param $key
     * @return Form_Security_Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected static function create($key)
    {
        /** @var Form_Security_Login $class */
        $class = self::getClass();

        if ($class == __CLASS__) {
            $class = 'Ice\Form\Security\Login\\' . $key;
        }

        return new $class($key);
    }
}
