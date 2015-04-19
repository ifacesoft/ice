<?php

namespace Ice\Core;

abstract class Widget_Form_Security_Login extends Widget_Form
{
    /**
     * Create new instance of form security login
     *
     * @param  $key
     * @return Widget_Form_Security_Login
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since   0.1
     */
    protected static function create($key)
    {
        /**
         * @var Widget_Form_Security_Login $class
         */
        $class = self::getClass();

        if ($class == __CLASS__) {
            $class = 'Ice\Form\Security\Login\\' . $key;
        }

        return new $class($key);
    }
}
